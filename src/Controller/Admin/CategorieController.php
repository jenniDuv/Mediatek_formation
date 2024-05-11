<?php

namespace App\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class CategorieController extends AbstractController
{


    
    //Liste les catégories

    /**
     * @Route("/admin/categories", name="admin.categories")
     */

     public function index(CategorieRepository $categorieRepository, Request $request, ManagerRegistry $managerRegistry): Response {

        
        $categorie = new Categorie;

        $formBuilder = $this->createFormBuilder($categorie)
        ->add('name', TextType::class, [
            'label'=> 'Catégorie'
        ]);
        $form = $formBuilder->getForm();




        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


                $manager = $managerRegistry->getManager();
                $manager->persist($categorie);
                $manager->flush();
                $this->addFlash('success', 'La nouvelle catégorie a été ajoutée avec succès.');


                unset($categorie);
                $categorie = new Categorie;
                $formBuilder->setData($categorie);
                $form = $formBuilder->getForm();
            }

            $categories = $categorieRepository->findAll();


        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);

     }

    


    //Supprimer une catégorie


    /**
     * @Route("/admin/categories/delete/{id}", name="admin.categories.delete")
     */


     public function delete(Categorie $categorie, ManagerRegistry $managerRegistry): Response
     {

        $formations = $categorie->getFormations();


         // Vérifier si la catégorie est rattachée à une formation
         if (!$formations->isEmpty()) {
             $this->addFlash('danger', 'La catégorie est rattachée à des formations et ne peut pas être supprimée.');
         
            } else {

            $manager = $managerRegistry->getManager();
            $manager->remove($categorie);
            $manager->flush();
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
         }
 
         return $this->redirectToRoute('admin.categories');
     }




    }
