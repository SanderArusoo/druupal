<?php
/**
 * @file
 * Contains \Drupal\available_times\Form\ReservationForm.
 */
namespace Drupal\available_times\Form;
use Drupal\available_times\Services\AvailableTimesService;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;

class ReservationForm extends FormBase
{


  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'reservation_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    /*** @var AvailableTimesService $AvailableTimesService */

    // TODO: use dependency injection
    $AvailableTimesService = \Drupal::service(AvailableTimesService::SERVICE_ID);

    $availTimes = $AvailableTimesService->getAvailTimes();

    foreach ($availTimes as $time => $bool) {
      if (!$bool) {
        unset($availTimes[$time]);
      } else {

        $formattedTime = mktime($time, 00, 00);
        $formattedTime = date('Y-m-d H:i:s', $formattedTime);
        $formattedTimes[$formattedTime] = $formattedTime;

       //$availTimes[$time] = $time;

      }
    }

    $form['contact_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Nimi:'),
      '#required' => TRUE,
    );

    $form['field_contact_email'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
    );


//    $form['field_start_date'] = array(
//      '#type' => 'date',
//      '#title' => t('Start date'),
//      '#required' => FALSE,
//    );

    $form['start_time'] = array(

      '#type' => 'radios',
      '#title' => ('Open start time'),
      '#options' => $formattedTimes,
      '#required' => TRUE,
    );
    $form['field_confirmed'] = array (
      '#type' => 'checkbox',
     '#title' => t('Confirmed?'),

    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    $reservationTime = $form_state->getValue('start_time');
    $contactName = $form_state->getValue('contact_name');
    $contactEmail = $form_state->getValue('field_contact_email');

    $dateTimeWithZone = new DrupalDateTime($form_state->getValue('start_time'));
    $dateTimeWithZone->setTimezone(new \DateTimeZone('UTC'));
    $dateTimeWithZone = $dateTimeWithZone->format('Y-m-d\TH:i:s');


    $this->messenger()->addStatus($this->t('Reservation created!'));

    $this->messenger()->addStatus(($this->t('Reservation time is: @date', [
      '@date' => $reservationTime,])));

    $this->messenger()->addStatus(($this->t('Contact name is:  @name', [
      '@name' => $contactName,])));


    $this->messenger()->addStatus(($this->t('Contact email is:  @email', [
      '@email' => $contactEmail,])));


    /**
     * add Node
     */
    $node = Node::create(['type' => 'reser']);
    //'reser' -machine name
    $node->setTitle($contactName);
    $node->set('field_contact_email',$contactEmail);



    $node->set('field_start_date', $dateTimeWithZone);
    $node->set('field_confirmed', 1);
    $node->save();

    $availableTimesService= new AvailableTimesService();
    $availableTimesService->sendEmail($reservationTime, $contactName, $contactEmail);

    $url = \Drupal\Core\Url::fromUri('https://drupal.ddev.site/');
    $form_state->setRedirectUrl($url);
    }

}
