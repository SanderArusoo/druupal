<?php

namespace Drupal\available_times\Controller;

use Drupal\available_times\Services\AvailableTimesService;
use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Reservation routes.
 */
class ReservationController extends ControllerBase
{

  public function showAvailableTimes()
  {
    /*** @var AvailableTimesService $reservationService */
    $reservationService = \Drupal::service(AvailableTimesService::SERVICE_ID);
    return new JsonResponse(['data' => $reservationService->availTimes(), 'method' => 'GET', 'status' => 200]);
  }
}
