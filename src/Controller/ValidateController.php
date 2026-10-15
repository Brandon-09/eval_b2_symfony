<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ValidateController extends AbstractController
{
    #[Route('/admin/evenements', name: 'app_validate_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(
            ['isValidated' => false],
            ['date' => 'ASC']
        );

        return $this->render('validate/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route(
        '/admin/evenements/{id}/valider',
        name: 'app_validate_event',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function validate(
        Event $event,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'validate_event_' . $event->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        if (!$event->isValidated()) {
            $event->setIsValidated(true);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Événement validé avec succès !'
            );
        }

        return $this->redirectToRoute('app_validate_index');
    }
}
