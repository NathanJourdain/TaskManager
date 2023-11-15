<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\WorkSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected TaskRepository $taskRepository;
    protected WorkSessionRepository $workSessionRepository;
    
    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository, WorkSessionRepository $workSessionRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->workSessionRepository = $workSessionRepository;
    }
    
    #[Route('/create', name: 'app_task_create')]
    #[Route('/update/{id}', name: 'app_task_update', requirements: ['id' => '\d+'])]
    public function createTask(Task $task = null, Request $request): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if(!$task) {
            $task = new Task;
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
 
    }

    #[Route('/delete/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'])]
    public function deleteTask(Task $task): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');
    }


    #[Route('/{id}/complete', name: 'app_task_complete', requirements: ['id' => '\d+'])]
    public function completeTask(Task $task): Response
    {
        if(!$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }
        
        // Si c'est un employee et que la tâche est assigné à quelqu'un d'autre alors on refuse
        if($task->getAssignedTo() !== null && $task->getAssignedTo() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Réccupération de la session de travail en cours
        $currentSession = $this->workSessionRepository->getCurrentWorkSession($this->getUser());
        if($currentSession === null) {
            throw $this->createAccessDeniedException();
        }

        $task->setWorkSession($currentSession);
        $task->setCompleted(true);

        $this->entityManager->flush();

        $currentSession = $this->workSessionRepository->getCurrentWorkSession($this->getUser());
        return $this->redirectToRoute('app_work_session_show', ['id' => $currentSession->getId()]);
    }
}
