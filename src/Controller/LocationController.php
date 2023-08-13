<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/location")
 */
class LocationController extends AbstractController
{
    /**
     * @Route("/", name="app_location_index", methods={"GET"})
     */
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_location_new", methods={"GET", "POST"})
     */
    public function new(Request $request, LocationRepository $locationRepository, PictureService $pictureService): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $image = $pictureService->add($image, 'location');
            $location->setImage($image);
            $locationRepository->add($location, true);

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_location_show", methods={"GET"})
     */
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_location_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Location $location, LocationRepository $locationRepository): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locationRepository->add($location, true);

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_location_delete")
     */
    public function delete(Request $request, Location $location, LocationRepository $locationRepository, PictureService $pictureService): Response
    {
        $pictureService->delete($location->getImage(), 'location');
        $locationRepository->remove($location, true);
        return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
    }

    public function counterAll(LocationRepository $locationRepository)
    {
        
        $locations = $locationRepository->findAll();

        return $this->render(
            'components/counter.html.twig',
            ['counter' => count($locations)]
        );
    }
}
