<?php

namespace Drupal\gsas_bedeworks\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BedeworksSyncForm.
 *
 * @package Drupal\gsas_bedeworks\Form
 */
class BedeworksSyncForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gsas_bedeworks_sync';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['button'] = array(
      '#type' => 'submit',
      '#value' => t('Start import'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $batch = array(
      'operations' => array(
        array(
          '\Drupal\gsas_bedeworks\BedeworksBatch::batchProcess',
          array(),
        ),
      ),
      'finished' => array('\Drupal\gsas_bedeworks\BedeworksBatch', 'batchFinished'),
      'title' => t('Processing import'),
      'init_message' => t('Starting of the import of the events'),
      'progress_message' => t('Processed @current out of @total.'),
      'error_message' => t('Import has been failed'),
    );

    batch_set($batch);
  }

}
