<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if both type and value are provided.
 */
#[Constraint(
  id: 'ValueAndTypeProvided',
  label: new TranslatableMarkup('Value and type must both be provided.', [], ['context' => 'Validation']),
)]
class ValueAndTypeProvidedConstraint extends SymfonyConstraint {
  /**
   * The error message if either type or value are not provided.
   *
   * @var string
   */
  public $message = 'Both a value and a type must be provided.';

}
