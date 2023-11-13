<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\WorkSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected WorkSessionRepository $workSessionRepository;
    protected TaskRepository $taskRepository;
    
    public function __construct(EntityManagerInterface $entityManager, WorkSessionRepository $workSessionRepository, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->workSessionRepository = $workSessionRepository;
        $this->taskRepository = $taskRepository;
    }

    #[Route('', name: 'app_home')]
    public function listSessions(): Response
    {

        // Récupération des données de l'utilisateur connecté suivant son rôle
        if($this->isGranted('ROLE_ADMIN')) {
            $workSessions = $this->workSessionRepository->findBy([], ['id' => 'DESC']);
            $tasks = $this->taskRepository->findBy(['completed' => false]);
        }
        else if($this->isGranted('ROLE_EMPLOYEE')) {
            $workSessions = $this->workSessionRepository->findBy(['employee' => $this->getUser()], ['id' => 'DESC']);
            $tasks = $this->taskRepository->findUserTasks($this->getUser());
            $currentSession = $this->workSessionRepository->getCurrentWorkSession($this->getUser());
        }

        return $this->render('index.html.twig', [
            'workSessions' => $workSessions,
            'currentSession' => $currentSession ?? null,
            'tasks' => $tasks
        ]);
    }
}
