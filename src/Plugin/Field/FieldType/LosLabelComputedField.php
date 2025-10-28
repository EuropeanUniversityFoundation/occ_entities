<?php

namespace Drupal\occ_entities\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Learning Opportunity Specification label computed field class.
 */

class LosLabelComputedField extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * Computes the values for an item list.
   */
  protected function computeValue() {
    $entity = $this->getEntity();
    $label = $entity->get('title')[0]->string;
    /** @var \Drupal\Core\Field\FieldItemInterface $item */
    $item = $this->createItem(0, $label);
    $this->list[0] = $item;
  }

}
