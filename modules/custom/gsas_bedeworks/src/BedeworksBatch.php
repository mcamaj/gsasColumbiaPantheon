<?php

namespace Drupal\gsas_bedeworks;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class BedeworksBatch.
 *
 * @package Drupal\gsas_bedeworks
 */
class BedeworksBatch {

  /**
   * Custom function that gets feed data from the external URL.
   *
   * @return object
   *   List of events object.
   */
  static public function getFeedEvents() {
    $client = \Drupal::httpClient();
    $feed_url = \Drupal::config('gsas_bedeworks.settings')
      ->get('feed_url');
    $response = $client->get($feed_url);
    $data = $response->getBody()->getContents();
    $document = json_decode($data);
    $events = $document->bwEventList->events;

    return $events;
  }

  /**
   * Batch step processing method.
   *
   * @inheritdoc
   */
  static public function batchProcess(array &$context = NULL) {
    if (!isset($context['sandbox']['max'])) {
      $events = self::getFeedEvents();
      $context['sandbox']['max'] = count($events);
      $context['sandbox']['events'] = $events;
      $context['sandbox']['progress'] = 0;
    }
    else {
      $event = $context['sandbox']['events'][$context['sandbox']['progress']];
      self::processEventItem($event);
      $context['sandbox']['progress']++;
    }

    if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
  }

  /**
   * Methos that importing events node from the object from feed.
   *
   * @param object $event
   *   An event serialized from Bedeworks json.
   */
  static public function processEventItem($event) {

    $guid = $event->guid;
    $existed_event = \Drupal::entityQuery('node')
      ->condition('field_guid', $guid)
      ->condition('type', 'events')
      ->execute();

    if (empty($existed_event)) {
      // Formatting event dates to time.
      $start_date = strtotime($event->start->longdate);
      $end_date = strtotime($event->end->longdate);
      // Formatting event_contact field value.
      $event_contact = $event->contact->name;

      if (!empty($event->contact->phone)) {
        $event_contact .= ', ' . $event->contact->phone;
      }

      if (!empty($event->contact->email)) {
        $event_contact .= ', ' . $event->contact->email;
      }

      $event_location = $event->location->address;

      if (!empty($event->location->link)) {
        $event_location .= ', ' . $event->location->link;
      }

      $field_image_arr = array();

      if (!empty($event->xproperties)) {
        // Analyse Bedeworks x-properties one by one.
        $field_event_access = array();
        $tids = array();
        foreach ($event->xproperties as $property) {

          // Get EventAccess field data from the feed data.
          $field_name = 'X-BEDEWORK-ALIAS';
          if (property_exists($property, $field_name)) {
            $alias = $property->{$field_name}->values->text;
            if (strpos($alias, 'Events open to') !== FALSE) {
              $arr = explode('/', $alias);
              $access_value = $arr[count($arr) - 1];
              // Check if this taxonomy term already exists:
              $taxonomy_check = \Drupal::entityQuery('taxonomy_term')
                ->condition('name', $access_value)
                ->execute();
              if (!empty($taxonomy_check)) {
                $tids[] = array_keys($taxonomy_check)[0];
              }
              else {
                /** @var Term $term */
                $term = Term::create(array(
                  'parent' => array(),
                  'name' => $access_value,
                  'vid' => 'event_access',
                ));
                $term->save();
                $tids[] = $term->tid->value;
              }
            }
          }

          // Get Image field from the feed data.
          $field_name = 'X-BEDEWORK-IMAGE';
          if (property_exists($property, $field_name)) {
            $field = $property->{$field_name};
            $field_img_url = $field->values->text;

            // Format image url before file download.
            if (strpos($field_img_url, 'http') === FALSE) {
              $field_img_url = 'https://events.columbia.edu/pubcaldav' . $field_img_url;
            }

            // Download file and create file object.
            $data = file_get_contents($field_img_url);
            if (!empty($data)) {
              /** @var Drupal\file\Entity\File $file */
              $file = file_save_data($data, 'public://', FILE_EXISTS_REPLACE);
              $file->set('status', 1);
              $file->save();
              $field_image_arr = [
                'target_id' => $file->id(),
              ];
            }
            else {
              \Drupal::logger('gsas_bedeworks')->warning('Sync process: cant download image: ' . $field_img_url);
            }
          }
        }
        // Compile event access multivalue field.
        if (!empty($tids)) {
          $tids = array_unique($tids);
          // Configure field value based on the $tid we got earlier.
          foreach ($tids as $tid) {
            $field_event_access[] = [
              'target_id' => $tid,
            ];
          }
        }
      }

      // Compiling the Node object.
      $node = Node::create(array(
        'type' => 'events',
        'title' => html_entity_decode($event->summary),
        'langcode' => 'en',
        'uid' => '1',
        'status' => 1,
        'field_guid' => [
          'value' => $guid,
        ],
        'field_event_access' => $field_event_access,
        'field_image' => $field_image_arr,
        'field_event_description' => [
          'value' => check_markup($event->description, 'basic_html'),
        ],
        'field_event_location' => [
          'value' => $event_location,
        ],
        'field_event_start_date' => [
          'value' => date('Y-m-d', $start_date),
        ],
        'field_event_end_date' => [
          'value' => date('Y-m-d', $end_date),
        ],
        'field_event_start_time' => [
          'value' => $event->start->time,
        ],
        'field_event_end_time' => [
          'value' => $event->end->time,
        ],
        'field_event_rsvp' => [
          'uri' => $event->eventlink,
          'title' => 'RSVP',
        ],
        'field_event_url' => [
          'uri' => $event->eventlink,
        ],
        'field_event_contact' => [
          'value' => $event_contact,
        ],
        'field_featured_text' => [
          'value' => check_markup($event->description, 'basic_html'),
        ],
      ));
      $node->save();
    }
  }

  /**
   * Success callback of the batch operations.
   */
  public function batchFinished($success, $results, $operations) {
    drupal_set_message(t('Import of the events has been finished.'));
  }

}
