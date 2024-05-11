<?php

namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//use Symfony\Bridge\Doctrine\ManagerRegistry;



/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController {


    const PLAYLISTS_VIEW = "admin/playlists.html.twig";
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');

        $categories = $this->categorieRepository->findAll();

        // Récupérez le nombre de formations pour chaque playlist
        $formationCount = [];
        foreach ($playlists as $playlist) {
            $formationCount[$playlist->getId()] = $this->formationRepository->countByPlaylist($playlist->getId());

        }

        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $playlists,
            'categories' => $categories,
            'formationCount' => $formationCount,

        ]);
    }

    /**
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{

        switch($champ){
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;

            case "formation_count":
                $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
                break;

        }

        $categories = $this->categorieRepository->findAll();
        $formationCount = [];
        foreach ($playlists as $playlist) {
            $formationCount[$playlist->getId()] = $this->formationRepository->countByPlaylist($playlist->getId());
        }


        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $playlists,
            'categories' => $categories,
            'formationCount' => $formationCount,

        ]);
    }
	
    /**
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,

        ]);
    }
    
    /**
     * @Route("/admin/playlists/playlist/{id}", name="admin.playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);

        // Appel de la méthode countFormationsByPlaylist pour obtenir le nombre de formations
        $formationCount = $this->playlistRepository->countFormationsByPlaylist($id);


        return $this->render("admin/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations,
            'formationCount' => $formationCount,

        ]);
    }




    //Supprimer une playlist

     /**
     * @Route("/admin/playlists/delete/{id}", name="admin.playlists.delete")
     */

     public function delete(Playlist $playlist, ManagerRegistry $managerRegistry): Response{



        $formations = $playlist->getFormations();


        // Vérifie si des formations sont rattachées à la playlist
        if (!$formations->isEmpty()) {
            $this->addFlash('danger', 'Impossible de supprimer cette playlist car des formations y sont rattachées.');
            return $this->redirectToRoute('admin.playlists');
        }

        // Supprime la playlist
        $manager = $managerRegistry->getManager();
        $manager->remove($playlist);
        $manager->flush();

        $this->addFlash("success","La playlist à été supprimer");

        return $this->redirectToRoute('admin.playlists');

     }



    //Bouton d'ajout de playlist


     /**
     * @Route("/admin/playlists/add", name="admin.playlists.add")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        // Création d'une nouvelle formation
        $playlist = new Playlist();

        // Création du formulaire à partir de FormationType et de l'instance Formation
        $form = $this->createForm(PlaylistType::class, $playlist);
        
        // Traitement de la soumission du formulaire
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            // Enregistrement de la playlist dans la base de données
            $manager = $managerRegistry->getManager();

            $manager->persist($playlist); //permet d'ajouter un nouvelle élement dans la playlist
      
            $manager->flush();
      
            $this->addFlash("success", "La playlist a été ajoutée avec succès");
      
            return $this->redirectToRoute('admin.playlists.showone',[
            "id" => $playlist->getId()]);
        }

        // Rendu du formulaire d'ajout de formation
        return $this->render('admin/playlist_edit.html.twig', [
            'form' => $form->createView(),

        ]);
    }

     //Formulaire pré-rempli

    /**
    * @Route("/admin/playlists/edit/{id}", name="admin.playlists.edit")
     */


     public function edit(Playlist $playlist, Request $request, ManagerRegistry $managerRegistry ){
        

        // Récupérer les formations associées à cette playlist
        $formations = $playlist->getFormations();


        // Créer le formulaire de modification de la playlist
        $form = $this->createForm(PlaylistType::class, $playlist);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

    
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Enregistrer les modifications de la playlist
            $manager = $managerRegistry->getManager();
            $manager->persist($playlist);
            $manager->flush();
            $this->addFlash("success", "La playlist a été ajoutée avec succès");

            return $this->redirectToRoute('admin.playlists');

        }


        // Rendu du formulaire de modification avec les données préremplies et la liste des formations
        return $this->render('admin/playlist_edit.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
        ]);


     }




    
}
