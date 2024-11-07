<?php

namespace App\Controller;

use App\DTO\PropertyDTO;
use App\Repository\PropertyRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api")
 */
class ApiController extends AbstractFOSRestController
{

    /**
     * @Route("/properties",  methods={"GET"})
     */
    public function all(PropertyRepository $propertyRepository, SerializerInterface $serializer): Response
    {
        $properties = $propertyRepository->findAll();
        $propertyDTOs = [];

        foreach ($properties as $property) {
            $propertyDTO = new PropertyDTO(
                $property->getId(),
                $property->getName(),
                $property->getType(),
                $property->getPrice(),
                $property->getBath(),
                $property->getRoom(),
                $property->getMesure(),
                $property->getLocation()
            );

            $propertyDTOs[] = $propertyDTO;
        }

        $serializedProperties = $serializer->serialize($propertyDTOs, 'json');

        return new Response($serializedProperties, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
