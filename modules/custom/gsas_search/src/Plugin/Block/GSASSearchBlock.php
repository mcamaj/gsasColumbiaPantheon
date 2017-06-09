<?php

namespace Drupal\gsas_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'GSASSearchBlock' block.
 *
 * @Block(
 *  id = "gsas_search_block",
 *  admin_label = @Translation("GSAS Search Block"),
 * )
 */
class GSASSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $search = [
      '#type' => 'form',
      '#action' => '/search',
      '#method' => 'GET',
    ];

    $search['form-item'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-item',
        ],
      ],
      'type' => [
        '#type' => 'hidden',
        '#value' => 'All',
        '#attributes' => [
          'name' => 'type',
        ],
      ],
      0 => [
        '#type' => 'search',
        '#attributes' => array(
          'name' => 'query',
          'class' => array(
            'form-search',
          ),
          'placeholder' => $this->t('Search Columbia GSAS'),
        ),
      ],
    ];

    $search['form-actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-actions',
        ],
      ],
      'go' => [
        '#type' => 'submit',
        '#value' => 'Go',
        '#attributes' => [
          'class' => [
            'form-submit',
          ],
        ],
      ],
    ];

    return $search;
  }

}
