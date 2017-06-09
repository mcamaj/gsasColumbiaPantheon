<?php

namespace Drupal\gsas_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a Upcoming events.
 *
 * @Block(
 *   id = "gsas_blocks_upcoming_events",
 *   admin_label = @Translation("GSAS Blocks - Upcoming events"),
 * )
 */
class GsasBlocksUpcomingEvents extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Reading Upcoming events entity.
    $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load('upcoming_events');
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
        ->view($event, 'upcoming_events');
    }

    $url = Url::fromUri('internal:/events');
    return array(
      '#class_name' => 'upcoming-events',
      '#title' => t('Upcoming Events'),
      '#theme' => 'gsas_blocks_upcoming_events',
      '#items' => $items,
      '#all_link' => \Drupal::l(t('See more'), $url),
    );
  }

}
