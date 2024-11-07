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
        return $this->json($propertyRepository->findAll(), 200, [], ['groups' => ['property_list']]);

    }
}
