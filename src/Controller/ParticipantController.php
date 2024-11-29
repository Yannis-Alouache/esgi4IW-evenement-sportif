<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ParticipantType;

class ParticipantController extends AbstractController
{
    #[Route('/events/{eventId}/participants/new', name: 'app_participant_new')]
    public function addParticipant(
        Request $request,
        EntityManagerInterface $entityManager,
        int $eventId
    ): Response {
        // Récupérer l'événement
        $event = $entityManager->getRepository(Event::class)->find($eventId);
        
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Créer un nouveau participant
        $participant = new Participant();
        $participant->setEvent($event);

        // Créer le formulaire en utilisant ParticipantType
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si le participant existe déjà
            $existingParticipant = $entityManager
                ->getRepository(Participant::class)
                ->findOneBy([
                    'email' => $form->get('email')->getData(),
                    'event' => $event
                ]);

            if ($existingParticipant) {
                $this->addFlash(
                    'error',
                    'Vous êtes déjà inscrit à cet événement avec cette adresse email.'
                );
                return $this->redirectToRoute('app_event_view', ['id' => $eventId]);
            }

            $entityManager
                ->getRepository(Participant::class)
                ->save($participant, true);

            $this->addFlash('success', 'Votre inscription a été enregistrée avec succès !');
            return $this->redirectToRoute('app_event_view', ['id' => $eventId]);
        }

        return $this->render('participant/new.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }
} 