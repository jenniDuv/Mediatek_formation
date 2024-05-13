<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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

    protected function tearDown(): void
    {
        parent::tearDown();

        // clean up after each test
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddUser()
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = new User();
        $user->setUsername('testuser');
        $user->setPassword('testpassword');
        // Set other properties as needed

        $userRepository->add($user, true);

        $this->assertNotNull($user->getId());

        $retrievedUser = $userRepository->find($user->getId());
        $this->assertInstanceOf(User::class, $retrievedUser);
        $this->assertEquals('testuser', $retrievedUser->getUsername());
        // Add more assertions as needed
    }

    public function testRemoveUser()
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = new User();
        // set user properties

        $userRepository->add($user, true);

        $userId = $user->getId();
        $userRepository->remove($user, true);

        $removedUser = $userRepository->find($userId);
        $this->assertNull($removedUser);
    }
}