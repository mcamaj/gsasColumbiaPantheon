<?php

namespace Drupal\gsas_bedeworks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class GSASBedeworksConfig.
 *
 * @package Drupal\gsas_bedeworks\Form
 */
class GSASBedeworksConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gsas_bedeworks_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config($this->getConfigName());

    $form['feed_url'] = array(
      '#type' => 'textarea',
      '#title' => t('Bedeworks feed url'),
      '#default_value' => $config->get('feed_url'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * Make available to overide from setings.php and display that here.
   */
  public function getEditableConfigNames() {
    return [];
  }

  /**
   * This form is only for single config.
   *
   * @return string
   *   Name of the config.
   */
  protected function getConfigName() {
    return 'gsas_bedeworks.settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable($this->getConfigName());
    $config->set('feed_url', $form_state->getValue('feed_url'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
