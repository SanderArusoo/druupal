<?php



namespace Drupal\available_times\Controller;

use Drupal\available_times\Services\AvailableTimesService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\available_times\Form\ReservationForm;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Reservation routes.
 */
class ReservationController extends ControllerBase
{

  public function showAvailableTimes()
  {
    /*** @var AvailableTimesService $AvailableTimesService */

    // TODO: use dependency injection
    $AvailableTimesService = \Drupal::service(AvailableTimesService::SERVICE_ID);
    $times = $AvailableTimesService->availTimes();
//    foreach ($availTimes as $key => $value) {
//      if ($value) {
//        $result = 'TRUE';
//      }else{
//        $result = 'FALSE';
//      }
//      $times[$key] = ['time'=>$key,'available'=>$result];
//    }


    return [
      '#theme' => 'reservation_list',
      '#items' => $times,
      '#attached'=>['library'=>['available_times/reservation_list']]
    ];

  }
//  public function content() {
//    /*** @var AvailableTimesService $AvailableTimesService */
//    $AvailableTimesService = \Drupal::service(AvailableTimesService::SERVICE_ID);
//    $tests = $AvailableTimesService->availTimes();
//    foreach ($tests as $key => $value) {
//      if ($value) {
//        $result = 'TRUE';
//      }else{
//        $result = 'FALSE';
//      }
//      $times[$key] = ['key'=>$key,'value'=>$result];
//    }
//
//    return [
//      '#theme' => 'available_times_theme',
//      '#items' => $times,
//    ];
//  }
  public function reservationForm() {

    // TODO: use dependency injection
    $form = \Drupal::formBuilder()
     ->getForm('\Drupal\available_times\Form\ReservationForm');

    return ['form'=>$form];
  }




}

