<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Checks if combnation of code and HEI reference is unique.
 */
#[Constraint(
  id: 'NoCircularReference',
  label: new TranslatableMarkup('Two entities can not reference each other as parents.', [], ['context' => 'Validation'])
)]
final class NoCircularReferenceConstraint extends SymfonyConstraint {

  /**
   * The error message if there is a circular reference.
   *
   * @var string
   */
  public $message = 'Two entities can not reference each other as parents. %entity_label: %code_1 references %entity_label %code_2 and %code_2 references %code_1.';

  /**
   * Field name storing the code of the entity.
   *
   * @var string
   */
  public $code_field;

  /**
   * Field name storing the reference field name.
   *
   * @var string
   */
  public $parent_field;

  /**
   * Human readable name of the entity.
   *
   * @var string
   */
  public $entity_label;

}
