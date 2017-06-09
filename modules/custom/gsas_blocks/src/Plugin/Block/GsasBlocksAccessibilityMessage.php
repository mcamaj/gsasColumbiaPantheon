<?php

namespace Drupal\gsas_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Accessibility message block.
 *
 * @Block(
 *   id = "gsas_blocks_accessibility_message",
 *   admin_label = @Translation("GSAS Blocks - Accessibility message"),
 * )
 */
class GsasBlocksAccessibilityMessage extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'gsas_blocks_accessibility_message',
    );
  }

}
