<?php

namespace Drupal\gsas_field_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\datetime\Plugin\Field\FieldFormatter\DateTimeFormatterBase;

/**
 * Plugin implementation of the 'Month/Day' formatter for 'datetime' fields.
 *
 * @FieldFormatter(
 *   id = "datetime_month_day",
 *   label = @Translation("Month/Day"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateTimeMonthDayFormatter extends DateTimeFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $output = '';
      if (!empty($item->date)) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $date */
        $date = $item->date;
        $this->setTimeZone($date);

        $output = $this->formatDate($date);
      }
      $elements[$delta] = [
        '#cache' => [
          'contexts' => [
            'timezone',
          ],
        ],
        '#markup' => $output,
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatDate($date) {
    $month = '<div class="month">' . $this->dateFormatter->format($date->getTimestamp(), 'custom', 'F') . '</div>';
    $monthShort = '<div class="month-short">' . $this->dateFormatter->format($date->getTimestamp(), 'custom', 'M') . '</div>';
    $day = '<div class="day">' . $this->dateFormatter->format($date->getTimestamp(), 'custom', 'j') . '</div>';
    return $month . $monthShort . $day;
  }

}
