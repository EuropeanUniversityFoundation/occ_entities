<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'positive_int_over_int',
  label: new TranslatableMarkup('Invalid format.', [], ['context' => 'Validation']),
)]
class PositiveIntOverIntConstraint extends SymfonyConstraint
{

  public $noRegexMatchMessage = 'Value %value must be in an n/N format, where n and N are integers (numbers).';

  public $termsNotPositiveMessage = 'Value %value must be in an n/N format, where n and N are greater than zero.';

}
