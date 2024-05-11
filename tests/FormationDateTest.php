<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationTest extends KernelTestCase
{
    public function testPublishedAtValidation()
    {
        self::bootKernel();
        $container = self::$container;

        // Créez une instance de Formation
        $formation = new Formation();

        // Définissez la date de publication sur une date ultérieure à aujourd'hui
        $futureDate = new DateTime('+1 day');
        $formation->setPublishedAt($futureDate);

        // Obtenez le validateur
        $validator = static::$container->get('validator');

        // Validez l'entité
        $errors = $validator->validate($formation);

        // Vérifiez que l'entité est invalide en raison de la date de publication
        $this->assertCount(1, $errors);
        $this->assertEquals('Cette valeur doit être antérieure ou égale à aujourd\'hui.', $errors[0]->getMessage());
    }
}