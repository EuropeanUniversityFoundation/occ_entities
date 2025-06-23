<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'int_over_int',
  label: new TranslatableMarkup('Invalid format.', [], ['context' => 'Validation']),
)]
class IntOverIntConstraint extends SymfonyConstraint
{

  public $noRegexMatchMessage = 'Value %value must be in an n/N format, where n and N are integers (numbers).';

  public $numberValueMismatchMessage = 'Invalid value: %value. The number before the "/" must be less or equal to the number after the "/".';
}
