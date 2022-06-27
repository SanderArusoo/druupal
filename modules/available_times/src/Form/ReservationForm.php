<?php
/**
 * @file
 * Contains \Drupal\available_times\Form\ReservationForm.
 */
namespace Drupal\available_times\Form;
use Drupal\available_times\Services\AvailableTimesService;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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
    $AvailableTimesService = \Drupal::service(AvailableTimesService::SERVICE_ID);
    $availTimes = $AvailableTimesService->availTimes();

    foreach ($availTimes as $time => $bool) {
      if (!$bool) {
        unset($availTimes[$time]);
      } else {

        $availTimes[$time] = $time;

      }
    }
    $form['contact_name'] = array(
      '#type' => 'textfield',
      '#title' => t('contact_name:'),
      '#required' => TRUE,
    );

    $form['body'] = array(
      '#type' => 'textfield',
      '#title' => t('body:'),
      '#required' => FALSE,
    );
    $form['field_mail'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
    );


    $form['start_date'] = array(
      '#type' => 'date',
      '#title' => t('Start date'),
      '#required' => TRUE,
    );

    $form['start_time'] = array(

      '#type' => 'radios',
      '#title' => ('Open start time'),
      '#options' => $availTimes,
    );
//    $form['field_confirmed'] = array (
//      '#type' => 'checkbox',
//      '#title' => t('Confirmed?'),
//
//    );

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


    }


}
//drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
//foreach ($form_state->getValues() as $key => $value) {
//  drupal_set_message($key . ': ' . $value);
