<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventController extends AbstractController
{

    #[Route('/evenement/ajouter', name: 'app_event_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $event = new Event();

        $event->setIsValidated(false);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {


                $extension = $imageFile->guessExtension() ?? 'bin';

                $filename = bin2hex(random_bytes(16))
                    . '.' . $extension;

                $destination = $this->getParameter('kernel.project_dir')
                    . '/public/uploads/events';

                if (!is_dir($destination)) {
                    mkdir($destination, 0775, true);
                }

                try {
                    $imageFile->move($destination, $filename);

                    $event->setImage($filename);

                } catch (FileException $e) {

                    $this->addFlash(
                        'error',
                        "Erreur lors de l'envoi de l'image."
                    );

                    return $this->render(
                        'event/new.html.twig',
                        [
                            'form' => $form->createView(),
                        ],
                        new Response(status: 422)
                    );
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Événement créé ! Il attend la validation.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route(
        '/evenement/{id}',
        name: 'app_event_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(
        int $id,
        EventRepository $eventRepository
    ): Response {

        $event = $eventRepository->find($id);

        // Afficher une erreur 404 s'il n'existe pas
        if (!$event) {
            throw $this->createNotFoundException(
                "Cet événement n'existe pas."
            );
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }


    #[Route(
        '/evenement/{id}/inscription',
        name: 'app_event_register',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function registerEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid(
            'register_event_' . $event->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        if (!$event->isValidated()) {

            $this->addFlash(
                'error',
                "Cet événement n'est pas encore validé."
            );

            return $this->redirectToRoute('app_event_show', [
                'id' => $event->getId(),
            ]);
        }

        if ($event->getDate() < new \DateTime()) {

            $this->addFlash(
                'error',
                'Cet événement est déjà passé.'
            );

            return $this->redirectToRoute('app_event_show', [
                'id' => $event->getId(),
            ]);
        }

        if ($event->getParticipants()->contains($user)) {

            $this->addFlash(
                'error',
                'Vous êtes déjà inscrit à cet événement.'
            );

        } elseif ($event->getRemainingPlaces() <= 0) {

            $this->addFlash(
                'error',
                'Cet événement est complet.'
            );

        } else {

            $event->addParticipant($user);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vous êtes inscrit à cet événement !'
            );
        }

        return $this->redirectToRoute('app_event_show', [
            'id' => $event->getId(),
        ]);
    }

    #[Route(
        '/evenement/{id}/desinscription',
        name: 'app_event_unregister',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function unregisterEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid(
            'unregister_event_' . $event->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        if ($event->getParticipants()->contains($user)) {

            $event->removeParticipant($user);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vous êtes désinscrit de cet événement.'
            );

        } else {

            $this->addFlash(
                'error',
                "Vous n'êtes pas inscrit à cet événement."
            );
        }

        return $this->redirectToRoute('app_event_show', [
            'id' => $event->getId(),
        ]);
    }
}
