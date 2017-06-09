<?php

namespace Drupal\gsas_field_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;

/**
 * Plugin implementation of the 'no_image' formatter.
 *
 * @FieldFormatter(
 *   id = "no_image",
 *   label = @Translation("No Image"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class NoImageFormatter extends ImageFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    foreach ($files as $delta => $file) {
      $elements[$delta] = array(
        '#markup' => '&nbsp;',
      );
    }

    return $elements;
  }

}
