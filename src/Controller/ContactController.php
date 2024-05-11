<?php

namespace App\Controller;


//importer les classe nécessaire
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
   public function index(Request $request, ManagerRegistry $managerRegistry) //Il va verifier la route et genere une réponse
   {


      $contact = new Contact();

      $form = $this->createForm(ContactType::class, $contact);//creation d'un formulaire en utilisant le formulaire de contact

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){


      $manager = $managerRegistry->getManager();

      $manager->persist($contact); //permet d'ajouter un nouvelle élement du contact

      $manager->flush();

      $this->addFlash("success", "Votre message a été enregistrer");

      return $this->redirectToRoute('formations');

    }
          return $this->render('contact/index.html.twig', [

            'form'=>$form->createView()
          ]//afficher le rendu de la page
        );

           

    }

     /**
     * @Route("/contacts", name="app_contacts")
    */

   public function contacts(ContactRepository $contactRepository){

  

    $contacts = $contactRepository->findAll();


     return $this->render('contact/all.html.twig', [
      'contacts' =>  $contacts
     ]);
    }





    //public fonction edit(){

      //$form = $this->createForm(ContactType::class, new Contact());


     // if{$form->su}

     // return $this->render("contact/edit.html.twig", );}


    /**
     * @Route("/contact/delete/{id}", name="app_contact_delete")
    */


     public function delete(ManagerRegistry $managerRegistry, Request $request, ContactRepository $contactRepository){


      $id = $request->attributes->get("id");

      $contact = $contactRepository->find($id);
      $manager = $managerRegistry->getManager();

      if (!$contact){
        throw new NotFoundHttpException("Le contact n'existe pas");
      }

      $manager->remove($contact);
      $manager->flush();

      $this->addFlash("success","la contact à été supprimer");

      return $this->redirectToRoute("app_contacts");
    }



}
