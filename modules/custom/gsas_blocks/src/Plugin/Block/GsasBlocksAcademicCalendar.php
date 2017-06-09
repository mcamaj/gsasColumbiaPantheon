<?php

namespace Drupal\gsas_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a Academic Calendar block.
 *
 * @Block(
 *   id = "gsas_blocks_academic_calendar",
 *   admin_label = @Translation("GSAS Blocks - Academic Calendar"),
 * )
 */
class GsasBlocksAcademicCalendar extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Reading Upcoming events entity.
    $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load('academic_calendar');
    $queue_items = $entity_subqueue->get('items')->getValue();
    $eids = array();
    foreach ($queue_items as $item) {
      $eids[] = $item['target_id'];
    }

    // Geting event items, connected to the Upcoming events queue.
    $events = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($eids);
    foreach ($events as $event) {
      $items[] = \Drupal::entityManager()
        ->getViewBuilder($event->getEntityTypeId())
        ->view($event, 'academic_calendar');
    }

    $url = Url::fromUri('internal:/events');
    return array(
      '#class_name' => 'academic-calendar',
      '#title' => t('Academic Calendar'),
      '#theme' => 'gsas_blocks_upcoming_events',
      '#items' => $items,
      '#all_link' => \Drupal::l(t('See more'), $url),
    );
  }

}
