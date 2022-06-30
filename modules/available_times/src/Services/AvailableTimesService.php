<?php

namespace Drupal\available_times\Services;

use Drupal\Core\Datetime\DrupalDateTime;

//display_times
class AvailableTimesService{
  const SERVICE_ID = 'available_times.display_times';
  const AVAILABLE_TIMES = [
    8 => true,
    9 => true,
    10 => true,
    11 => true,
    12 => true,
    13 => true,
    14 => true,
    15 => true,
    16 => true,
    17 => true,
    18 => true,
    19 => true,
    20 => true,
    21 => true,
  ];


  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function availTimes(): array
  {

    $availTimes = $this->getAvailTimes();
    foreach ($availTimes as $key => $value) {
      if ($value) {
        $result = 'TRUE';
      } else {
        $result = 'FALSE';
      }
      $times[$key] = ['time' => $key, 'available' => $result];
    }
    return $availTimes;
  }

  public function getAvailTimes(): array {

    // TODO: use dependency injection
    $nodeStorage = \Drupal::entityTypeManager()->getStorage('node');
    $reservationIds = $nodeStorage->getQuery()
      ->condition('type', 'reser')
      ->condition('field_start_date', date('Y-m-d') . 'T00:00:00', '>')
      ->condition('field_start_date', date('Y-m-d') . 'T23:59:59', '<')
      ->condition('field_confirmed', 1)
      ->execute();
    $availTimes = self::AVAILABLE_TIMES;

    foreach ($reservationIds as $reservationId) {

      // TODO: use dependency injection
      /*** @var \Drupal\node\NodeInterface $reservation */
      $reservation = $nodeStorage->load($reservationId);

      $date_original = new DrupalDateTime($reservation->field_start_date->value, 'UTC');

      // TODO: use dependency injection
      $dateTime = \Drupal::service('date.formatter')
        ->format($date_original->getTimestamp(), 'custom', 'Y-m-d H:i:s');
      $reservationHour = (new \DateTime($dateTime))->format('G');
      $availTimes[$reservationHour] = FALSE;
    }
    return $availTimes;
  }
  public function sendEmail($reservationTime,$contactName,$contactEmail) {

// TODO: use dependency injection
    $mailManager = \Drupal::service('plugin.manager.mail');

    $module = 'reservation';
    $key = 'reservationId';
    $to = $contactEmail;
    $langcode = 'en';
    $params['contact_name'] = $contactName;
    $params['reservation_time'] = $reservationTime;
    $send = TRUE;

    $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    return [

    ];
  }


}
