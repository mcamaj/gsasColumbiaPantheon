<?php

namespace Drupal\gsas_twitter\Plugin\Field\FieldFormatter;

/**
 * Resolve composer.json dependencies.
 */
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Field\FormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'GSASHeroFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "gsas_twitter_formatter",
 *   label = @Translation("Twitter formatter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class GSASTwitterFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a StringFormatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityManagerInterface $entity_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();

    $options['count'] = 1;

    $options['oauth_access_token'] = '17155367-vU1qPVmRkFRGjKsuIpqkCrmvMn0G0bKWvEgw3ozZU';
    $options['oauth_access_token_secret'] = 'fjs0kW3Xd7cjdJeKO7d2U58Ac7dmfPUXc5FMJkloCNCZQ';
    $options['consumer_key'] = 'PMBWpJdGcwwPLO82sWQ6XI4EI';
    $options['consumer_secret'] = 'DmPGJto5kkSASear2F7G1lIG9wD62FRELbcqK6wAMiST4Rj0rT';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['count'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Count of tweets'),
      '#default_value' => $this->getSetting('count'),
    ];

    $form['oauth_access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OAuth access token'),
      '#default_value' => $this->getSetting('oauth_access_token'),
    ];

    $form['oauth_access_token_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OAuth token secret'),
      '#default_value' => $this->getSetting('oauth_access_token_secret'),
    ];

    $form['consumer_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Consumer key'),
      '#default_value' => $this->getSetting('consumer_key'),
    ];

    $form['consumer_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Consumer secret'),
      '#default_value' => $this->getSetting('consumer_secret'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $count = $this->getSetting('count');
    return [$this->t('Count of tweets: @count', ['@count' => $count])];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = array();
    $render_tweets = array();

    $count = $this->getSetting('count');
    $credentials = array();
    $credentials['oauth_access_token'] = $this->getSetting('oauth_access_token');
    $credentials['oauth_access_token_secret'] = $this->getSetting('oauth_access_token_secret');
    $credentials['consumer_key'] = $this->getSetting('consumer_key');
    $credentials['consumer_secret'] = $this->getSetting('consumer_secret');

    foreach ($items as $delta => $item) {
      $field_value = $item->value;
      $tweets = _gsas_twitter_get_tweets($credentials, $field_value, $count);
      foreach ($tweets as $tweet) {
        $render_tweets[] = array(
          'text' => _gsas_twitter_highlight_terms($tweet['text']),
          'created_at' => date('h:i a - F, d', $tweet['created_at']),
        );
      }
      $elements[$delta] = array(
        '#theme' => 'gsas_twitter',
        '#handle' => $field_value,
        '#tweets' => $render_tweets,
      );
    }
    return $elements;
  }

}
