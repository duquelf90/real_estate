<?php


namespace App\Controller;

use App\Entity\Location;
use App\Entity\Property;
use App\Form\ExcelUploadType;
use App\Service\ExcelReader;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ExcelController extends AbstractController
{
    private $excelReader;

    public function __construct(ExcelReader $excelReader)
    {
        $this->excelReader = $excelReader;
    }

    /**
     * @Route("/upload", name="xlsx")
     * @param Request $request
     * @throws \Exception
     */
    public function uploadExcel(Request $request, EntityManagerInterface $entityManager)
    {
        $file = $request->files->get('file');
        $fileFolder = __DIR__ . '/../../public/uploads/';
        $filePathName = md5(uniqid()) . $file->getClientOriginalName();
        $fullPath = $fileFolder . $filePathName;

        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $spreadsheet = IOFactory::load($fileFolder . $filePathName);
        $spreadsheet->getActiveSheet()->removeRow(1);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $sheetData = array_slice($sheetData, 1);

        // Estados permitidos
        $allowedStates = ['ESTADO DE MÉXICO', 'CIUDAD DE MÉXICO', 'CDMX', 'PUEBLA'];
        $uniqueStates = [];

        $uniqueStates = []; // Almacena los estados únicos
        foreach ($sheetData as $Row) {
            $estadoNombre = trim($Row['D']);

            if ($estadoNombre === 'CIUDAD DE MÉXICO') {
                $estadoNombre = 'CDMX'; // Normaliza a CDMX
            }

            if (in_array($estadoNombre, $allowedStates) && !in_array($estadoNombre, $uniqueStates)) {
                $uniqueStates[] = $estadoNombre; // Agrega el estado a la lista de únicos

                $location = $entityManager->getRepository(Location::class)->findOneBy(['name' => $estadoNombre]);

                if (!$location) {
                    $location = new Location();
                    $location->setName($estadoNombre);
                    $entityManager->persist($location);
                }
            }
        }
        try {
            $entityManager->flush(); // Realiza el flush para guardar los estados
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        foreach ($sheetData as $Row) {
            $folio = $Row['A'];
            $foto = $Row['B'];
            $direccion = $Row['C'];
            $estadoNombre = $Row['D'];
            $ciudad = $Row['E'];
            $tipo_vivienda = $Row['I'];

            if ($estadoNombre === 'CIUDAD DE MÉXICO') {
                $estadoNombre = 'CDMX'; // Normaliza a CDMX
            }

            // Limpieza y conversión del precio
            $precioString = $Row['Q'];
            $precioString = str_replace(['$', ',', ' '], '', $precioString);
            $precio = intval($precioString);

            // Otras conversiones
            $m2_terreno = intval($Row['J']);
            $m2_construccion = intval($Row['K']);
            $recamaras = intval($Row['L']);
            $bath = intval($Row['M']);
            $parking = intval($Row['N']);

            // Verifica si el item ya existe
            $item_exist = $entityManager->getRepository(Property::class)->findOneBy(['name' => $folio]);

            if (!$item_exist) {
                // Verifica si los campos requeridos están vacíos
                if (empty($folio) || empty($tipo_vivienda) || $precio === 0) {
                    continue; // Omite esta fila si faltan campos requeridos
                }

                // Manejo de la entidad Location
                $location = $entityManager->getRepository(Location::class)->findOneBy(['name' => $estadoNombre]);

                // Verifica si la ubicación existe antes de crear la propiedad
                if ($location) {
                    // Creación de la nueva entidad Property
                    $item = new Property();
                    $item->setName($folio);
                    $item->setType($tipo_vivienda);
                    $item->setPrice($precio);
                    $item->setAddress($direccion);
                    $item->setCity($ciudad);
                    $item->setLocation($location); // Asocia la propiedad con la ubicación
                    $item->setMesure($m2_terreno);
                    $item->setBuild($m2_construccion);
                    $item->setPark($parking);
                    $item->setBath($bath);
                    $item->setRoom($recamaras);
                    $entityManager->persist($item);
                }
            }
        }

        // Realiza el flush una sola vez al final
        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // Elimina el archivo temporal después de procesar exitosamente
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }


        return $this->json('Propiedades registradas', 200);
    }
}