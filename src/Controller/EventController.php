<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\DistanceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    private $distanceCalculator;

    public function __construct(DistanceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    #[Route('/events', name: 'app_event_list', methods: ['GET'])]
    public function listEvents(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'app_event_view', methods: ['GET'])]
    public function viewEvent(Event $event): Response
    {
        return $this->render('event/view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/distance', name: 'app_event_distance', methods: ['GET'])]
    public function calculateDistanceToEvent(Event $event, Request $request): JsonResponse
    {
        $userLat = $request->query->get('lat');
        $userLon = $request->query->get('lon');

        if (!$userLat || !$userLon) {
            return new JsonResponse(['error' => 'Les paramètres lat et lon sont requis'], Response::HTTP_BAD_REQUEST);
        }

        $distance = $this->distanceCalculator->calculateDistance(
            $userLat,
            $userLon,
            $event->getLatitude(),
            $event->getLongitude()
        );

        return new JsonResponse([
            'distance' => $distance,
        ]);
    }
} 