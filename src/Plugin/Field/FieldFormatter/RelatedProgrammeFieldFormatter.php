<?php

namespace Drupal\occ_entities\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

/**
 * @FieldFormatter(
 *   id = "related_programme_view",
 *   label = @Translation("Entity label and mandatory flag"),
 *   description = @Translation("Display the referenced entities’ label with their mandatory flag."),
 *   field_types = {
 *     "related_programme"
 *   }
 * )
 */
class RelatedProgrammeFieldFormatter extends EntityReferenceLabelFormatter {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $values = $items->getValue();

    foreach ($elements as $delta => $entity) {
      $elements[$delta] = [];
      $elements[$delta]['programme'] = $entity;

      $mandatory_value = $values[$delta]['mandatory'];
      if (is_null($mandatory_value)) {
        $mandatory_label = 'Not specified';
      } else {
        $mandatory_label = $mandatory_value ? 'Yes' : 'No';
      }

      $elements[$delta]['mandatory'] = [
        '#type' => 'container',
        '#markup' => 'Mandatory: ' . $mandatory_label,
      ];

      $elements[$delta]['year'] = [
        '#type' => 'container',
        '#markup' => 'Year: ' . $values[$delta]['year'],
      ];
    }

    return $elements;
  }
}