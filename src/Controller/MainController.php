<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'pictures' => $pictures,
        ]);
    }

    /**
     * @Route("/galerie", name="gallery")
     */
    public function gallery()
    {
        return $this->render('main/gallery.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
