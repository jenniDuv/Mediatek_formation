<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindAllForOnePlaylist()
    {
        // Créer une BDD de test avec des données de test

        // Récupérer le repository
        $repository = $this->entityManager->getRepository(Categorie::class);

        // Appeler la méthode à tester
        $categories = $repository->findAllForOnePlaylist($idPlaylist);

        // Assurer que la méthode retourne un tableau
        $this->assertIsArray($categories);

        // Assurer que les catégories sont bien chargées pour la playlist donnée
        // Ici, vous pouvez ajouter des assertions spécifiques en fonction de vos besoins
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyer les données de test et fermer l'EntityManager
        $this->entityManager->close();
        $this->entityManager = null;
    }



    
}