<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/ajout", name="admin_add")
     */
    public function add()
    {
        $picture = new Picture;

        $form = $this->createForm(UploadType::class, $picture);

        return $this->render('admin/add.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
        ]);
    }

}
