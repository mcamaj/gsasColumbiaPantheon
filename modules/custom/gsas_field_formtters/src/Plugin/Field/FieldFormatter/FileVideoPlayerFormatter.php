<?php

namespace Drupal\gsas_field_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;

/**
 * Plugin implementation of the 'gsas_file_video_player' formatter.
 *
 * @FieldFormatter(
 *   id = "gsas_file_video_player",
 *   label = @Translation("GSAS Video Player"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class FileVideoPlayerFormatter extends FileFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'autoplay' => FALSE,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['autoplay'] = array(
      '#title' => t('Autoplay Video'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('autoplay'),
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = t('Autoplay: @autoplay', array('@autoplay' => $this->getSetting('autoplay') ? 'true' : 'false'));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $file) {
      $item = $file->_referringItem;

      $autoplay = $this->getSetting('autoplay');

      $elements[$delta] = array(
        '#theme' => 'gsas_file_video_player',
        '#file' => $file,
        '#autoplay' => $autoplay,
        '#cache' => array(
          'tags' => $file->getCacheTags(),
        ),
      );

      // Pass field item attributes to the theme function.
      if (isset($item->_attributes)) {
        $elements[$delta] += array('#attributes' => array());
        $elements[$delta]['#attributes'] += $item->_attributes;
        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }
    }

    return $elements;
  }

}
