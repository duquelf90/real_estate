<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PhotoRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/property")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="app_property_index", methods={"GET"})
     */
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_property_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PropertyRepository $propertyRepository, PictureService $pictureService): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('photos')->getData();

            foreach ($images as $image) {
                $image = $pictureService->add($image, 'property');
                $img = new Photo();
                $img->setName($image);
                $property->addImage($img);
            }
            $propertyRepository->add($property, true);
            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_property_show", methods={"GET"})
     */
    public function show(Property $property): Response
    {
        return $this->render('property/show.html.twig', [
            'property' => $property,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_property_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Property $property, PropertyRepository $propertyRepository): Response
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyRepository->add($property, true);

            return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('property/edit.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_property_delete")
     */
    public function delete(Request $request, Property $property, PropertyRepository $propertyRepository, PictureService $pictureService, EntityManagerInterface $entityManager): Response
    {
        $properties = $propertyRepository->find($property);
        foreach ($properties->getImages() as $image) {
            $pictureService->delete($image->getName(), 'property');
            $entityManager->remove($image);
        }

        $entityManager->remove($property);
        $entityManager->flush();
        return $this->redirectToRoute('app_property_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/supprime/image/{id}", name="property_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Photo $image, Request $request, PhotoRepository $photoRepository, PictureService $photoService)
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $photoService->delete($image->getName(), 'property');
            $photoRepository->remove($image, true);
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    public function counterAll(PropertyRepository $propertyRepository)
    {
        // make a database call or other logic
        // to get the "$max" most recent articles
        $properties = $propertyRepository->findAll();

        return $this->render(
            'components/counter.html.twig',
            ['counter' => count($properties)]
        );
    }
}
