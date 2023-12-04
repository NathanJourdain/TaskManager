<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de 5 utilisateurs
        for ($u=0; $u < 5; $u++) { 
            // Création d'un nouvel objet User
            $user = new User();
            
            // Si premier utilisateur créer on lui donne le rôle admin
            if($u === 0) {
                $user->setRoles(['ROLE_ADMIN'])
                    ->setEmail('test@test.com');
            } else {
                $user->setRoles(['ROLE_EMPLOYEE'])
                    ->setEmail($faker->safeEmail());
            }

            $user->setFirstname($faker->firstName())
                ->setLastname($faker->lastName());

            // Hashage du mot de passe avec les paramètres de sécurité de notre $user
            $hash = $this->hasher->hashPassword($user, 'password');

            // On affecte le meme password à tous les utilisateurs
            $user->setPassword($hash);

            // On fait persister les données
            $manager->persist($user);
        }

        $manager->flush();

        $users = $manager->getRepository(User::class)->findAll();

        // Création de 50 tâches 
        for ($t=0; $t < 50; $t++) { 
            // Création d'un nouvel objet Task
            $task = new Task;

            // On lui donne un titre
            $task->setTitle($faker->sentence(3, true));

            if($t % 2 == 0) {
                // On lui donne un utilisateur 1 fois sur 2
                $task->setAssignedTo($faker->randomElement($users));
            }

            $task->setEnable($faker->boolean(70));

            // On fait persister les données
            $manager->persist($task);
        }

        $manager->flush();
    }
}
