<?php

namespace App\Controller;

use App\Entity\WorkSession;
use App\Entity\WorkSessionComment;
use App\Form\WorkSessionCommentType;
use App\Repository\TaskRepository;
use App\Repository\WorkSessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work-sessions')]
class WorkSessionController extends AbstractController
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

    #[Route('/start', name: 'app_work_session_start')]
    public function startSession(): Response
    {
        // uniquement les employées peuvent démarrer une session de travail
        if(!$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Regarder si une session de travail est déjà en cours
        $currentSession = $this->workSessionRepository->getCurrentWorkSession($this->getUser());

        // Si déjà une session en cours on redirige vers la session
        if($currentSession !== null) {
            return $this->redirectToRoute('app_work_session_show', ['id' => $currentSession->getId()]);
        }
        
        // Sinon on crée une nouvelle session
        $workSession = new WorkSession;
        $workSession->setEmployee($this->getUser());
        $workSession->setStart(new DateTime);
        $this->entityManager->persist($workSession);
        $this->entityManager->flush();

        // On redirige vers la session
        return $this->redirectToRoute('app_work_session_show', ['id' => $workSession->getId()]);
    }

    #[Route('/finish', name: 'app_work_session_finish')]
    public function finishSession(): Response
    {
        // uniquement les employées peuvent arreter une session de travail
        if(!$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException();
        }

        // Regarder si une session de travail est en cours
        $currentSession = $this->workSessionRepository->getCurrentWorkSession($this->getUser());
        
        // Si aucune session en cours on fait une erreur
        if($currentSession === null) {
            throw $this->createNotFoundException();
        }
        
        // Sinon on termine la session
        $currentSession->setFinish(new DateTime);
        $this->entityManager->flush();

        // On redirige vers le détail de la session
        return $this->redirectToRoute('app_work_session_show', ['id' => $currentSession->getId()]);
    }

    #[Route('/{id}', name: 'app_work_session_show', requirements: ['id' => '\d+'])]
    public function showSession(WorkSession $workSession, Request $request): Response
    {
        // Si c'est un employee et que la tâche est assigné à quelqu'un d'autre alors on refuse
        if($this->isGranted('ROLE_EMPLOYEE')) {
            if($workSession->getEmployee() !== $this->getUser()) {
                throw $this->createAccessDeniedException();
            }
        }

        $tasks = $this->taskRepository->findUserTasks($this->getUser());

        $comment = new WorkSessionComment;
        $form = $this->createForm(WorkSessionCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setWorkSession($workSession);
            $comment->setUser($this->getUser());
            $comment->setDate(new DateTime);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_work_session_show', ['id' => $workSession->getId()]);
        }

        return $this->render('work_session/show.html.twig', [
            'workSession' => $workSession,
            'tasks' => $tasks,
            'form' => $form->createView()
        ]);
    }
}
