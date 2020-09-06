<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use App\Form\UploadType;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function browse(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'pictures' => $pictures,
            ]);
        }

    /**
     * @Route("/admin/edit/{id}", name="admin_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function edit(Picture $picture, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UploadType::class, $picture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($picture);
            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/edit.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/ajout", name="admin_add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $picture = new Picture;

        $form = $this->createForm(UploadType::class, $picture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->getData()->getName();
            $picture->setName($name);

            $description = $form->getData()->getDescription();
            $picture->setDescription($description);

            $file = $form['file']->getData();
            // Fonction de création aléatoire d'un nom pour le fichier image
            function generateRandomString($length = 10)
            {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $maxLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++)
                {
                    $randomString .= $characters[rand(0, $maxLength - 1)];
                }
                return $randomString;
            }

            // Appel de la fonction pour le retour du nom de fichier créé
            $filename = generateRandomString();

            // Inscription de l'emplacement où doit être stocker le fichier image dans une variable
            $directory = 'assets/pictures/';

            // L'objet Picture reçoit dans sa propriété file l'emplacement final du fichier image pour ensuite pouvoir l'utiliser par le FRONT
            $finalDirectory = $directory.$filename;
            $picture->setFile($finalDirectory);

            $em->persist($picture);
            $em->flush();
            
            
            // Maintenant il faut uploader et bouger le fichier image sur le projet/serveur
            // Inscription dans le services.yaml de l'emplacement de destination des fichiers images
            $file->move($this->getParameter('pictures_directory'), $filename.'.jpg');

            // Création de la miniature pour la page d'accueil avec la librairie PHP Intervention Image
            Image::configure(array('driver' => 'gd'));

            $preview = Image::make('assets/pictures/'.$filename.'.jpg');
            $preview->resize(450, 450);
            $preview->save('assets/pictures/'.$filename.'-preview.jpg');
            
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/add.html.twig', [
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
        ]);
    }


}
