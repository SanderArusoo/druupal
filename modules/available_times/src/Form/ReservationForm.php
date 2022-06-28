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
    $AvailableTimesService = \Drupal::service(AvailableTimesService::SERVICE_ID);
    $availTimes = $AvailableTimesService->availTimes();

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
      '#title' => t('contact_name:'),
      '#required' => TRUE,
    );

//    $form['body'] = array(
//      '#type' => 'textfield',
//      '#title' => t('body:'),
//      '#required' => FALSE,
//    );
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
    $this->messenger()->addStatus($this->t('Reservation created!'));
    $this->messenger()->addStatus(($this->t('Reserveeringu aeg on @date', [
      '@date' => $form_state->getValue('start_time')])));
    $this->messenger()->addStatus(($this->t('Kontakti nimi on @name', [
      '@name' => $form_state->getValue('contact_name')])));
    $this->messenger()->addStatus(($this->t('Kontakti email on @email', [
      '@email' => $form_state->getValue('field_contact_email')])));
    /**
     * add Node
     */
    $node = Node::create(['type' => 'reser']);
    //'reser' -machine name
    $node->setTitle($form_state->getValue('contact_name'));
    $node->set('field_contact_email',$form_state->getValue('field_contact_email'));
    $dateTimeWithZone = new DrupalDateTime($form_state->getValue('start_time'));
    $dateTimeWithZone->setTimezone(new \DateTimeZone('UTC'));
    $dateTimeWithZone = $dateTimeWithZone->format('Y-m-d\TH:i:s');
    $node->set('field_start_date', $dateTimeWithZone);
    $node->set('field_confirmed', 1);
    $node->save();
    $url = \Drupal\Core\Url::fromUri('https://drupal.ddev.site/');
    $form_state->setRedirectUrl($url);
    }


}
