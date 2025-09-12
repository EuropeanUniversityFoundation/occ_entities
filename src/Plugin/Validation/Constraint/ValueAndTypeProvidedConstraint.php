<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'value_and_type_provided',
  label: new TranslatableMarkup('Value and type must both be provided.', [], ['context' => 'Validation']),
)]
class ValueAndTypeProvidedConstraint extends SymfonyConstraint
{

  public $message = 'Both a value and a type must be provided.';

}
