<?php
//Ce code définit un contrôleur qui gère les différentes vues
//et les actions associées aux formations d'un site web.

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController {

    const FORMATIONS_VIEW = "admin/formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin/formations", name="admin.formations")
     * @return Response
     */
    public function index(): Response{ //permet à l'utilisateur de pouvoir accéder à l'URL /formations
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_VIEW, [
            'formations' => $formations,
            'categories' => $categories,

        ]);
    }

    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_VIEW, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_VIEW, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("/admin/formations/formation/{id}", name="admin.formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("admin/formation.html.twig", [
            'formation' => $formation
        ]);
    }



     
    /**
     * @Route("/admin/formations/delete/{id}", name="admin.formations.delete")
     */

     public function delete(Formation $formation, ManagerRegistry $managerRegistry): Response{


        $playlist = $formation->getPlaylist();
        $playlist->removeFormation($formation);
        
        $manager = $managerRegistry->getManager();
        $manager->persist($playlist);

        $manager->remove($formation);

        $manager->flush();

        $this->addFlash("success","La formation à été supprimer");

        return $this->redirectToRoute('admin.formations');


     }

     /**
     * @Route("/admin/formations/add/{id}", name="admin.formations.add")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        // Création d'une nouvelle formation
        $formation = new Formation();

        // Création du formulaire à partir de FormationType et de l'instance Formation
        $form = $this->createForm(FormationType::class, $formation);
        
        // Traitement de la soumission du formulaire
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            // Enregistrement de la formation dans la base de données
            $manager = $managerRegistry->getManager();

            $manager->persist($formation); //permet d'ajouter un nouvelle élement du contact
      
            $manager->flush();
      
            $this->addFlash("success", "La formation a été ajoutée avec succès");
      
            return $this->redirectToRoute('admin.formations.showone', [
                "id" => $formation->getId()]);
        }

        // Rendu du formulaire d'ajout de formation
        return $this->render('admin/formation_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //Formulaire pré-rempli

    /**
    * @Route("/admin/formations/edit/{id}", name="admin.formations.edit")
     */

     public function edit(Formation $formation, Request $request, FormationRepository $formationRepository, ManagerRegistry $managerRegistry): Response
     {

        $id = $request->query->get('id');

        if($request->query->get('id')) {
            $formation = $formationRepository->find($id);
        }

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $manager = $managerRegistry->getManager();
            $manager->flush();
            $this->addFlash("success", "La formation a été ajoutée avec succès");

        return $this->redirectToRoute('admin.formations.showone', [
            "id" => $formation->getId()
        ]);

    }

    return $this->render('admin/formation_edit.html.twig', [
        'form' => $form->createView(),
    ]);



}
}

