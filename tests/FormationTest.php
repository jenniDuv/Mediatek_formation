<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Formation;

class FormationTest extends TestCase
{
    public function testGetPublishedAtString()
    {
        // Créer un objet Formation
        $formation = new Formation();

        // Définir une date de parution
        $publishedAt = new \DateTime('2024-05-09');
        $formation->setPublishedAt($publishedAt);

        // Appeler la méthode getPublishedAtString()
        $result = $formation->getPublishedAtString();

        // Vérifier que la date retournée est correcte
        $this->assertEquals('09/05/2024', $result);
    }

    public function testGetPublishedAtStringWithNull()
    {
        // Créer un objet Formation sans date de parution
        $formation = new Formation();

        // Appeler la méthode getPublishedAtString()
        $result = $formation->getPublishedAtString();

        // Vérifier que la méthode retourne une chaîne vide si la date de parution est nulle
        $this->assertEquals('', $result);
    }
}