<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if combnation of code and HEI reference is unique.
 *
 * @Constraint(
 *   id = "no_circular_reference",
 *   label = @Translation("Two entities can not reference each other as parents", context = "Validation"),
 * )
 */
class NoCircularReferenceConstraint extends Constraint
{

  public $message = 'Two entities can not reference each other as parents. %entity_label: %code_1 references %entity_label %code_2 and %code_2 references %code_1.';

  /**
   * Field name storing the code of the entity.
   *
   * @var string
   */
  public $code_field;

  public $parent_field;

  /**
   * Human readable name of the entity.
   *
   * @var string
   */
  public $entity_label;
}
