<?php

namespace Drupal\gsas_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Menu\MenuLinkBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Provides a 'SectionHeader' block.
 *
 * @Block(
 *  id = "section_header_block",
 *  admin_label = @Translation("Section Header Block")
 * )
 */
class GsasBlocksSectionHeader extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $node = \Drupal::routeMatch()->getParameter('node');

    if (!empty($node)) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    }
    else {
      return parent::getCacheTags();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = array();

    $renderer = \Drupal::service('renderer');

    // Get root link.
    $root_link = _gsas_menu_root_parent_link();

    if (!empty($root_link)) {
      $node = $this->getLinkNode($root_link);

      // Is this node valid for section header?
      if ($this->isValidSectionHeaderNode($node)) {
        // Add output of node 'Section Header' display to block output:
        $output['section_header'] = node_view($node, 'section_header');

        // Get children of root link.
        $output['links'] = _gsas_menu_menu_link_children($root_link);
      }
    }

    $renderer->addCacheableDependency($output, \Drupal::routeMatch()->getParameter('node'));

    return $output;
  }

  /**
   * Retrieves the node (if applicable) from a menu link.
   *
   * @param MenuLinkBase $menu_link
   *   Menu link.
   *
   * @return NodeInterface
   *   Return node.
   */
  private function getLinkNode(MenuLinkBase $menu_link) {
    $node = NULL;

    $url = $menu_link->getUrlObject();

    if (!empty($url->getRouteParameters()['node'])) {
      $nid = $url->getRouteParameters()['node'];

      $node = Node::load($nid);
    }

    return $node;
  }

  /**
   * Determines that node is an appropriate section header.
   *
   * @param NodeInterface $node
   *   Node to inspect.
   *
   * @return bool
   *   Is valid section header node.
   */
  private function isValidSectionHeaderNode(NodeInterface $node) {
    $valid = FALSE;

    if (!empty($node->bundle())) {
      $types = array('features_page', 'resource_page', 'story_menu');
      $valid = in_array($node->bundle(), $types);
    }

    return $valid;
  }

}
