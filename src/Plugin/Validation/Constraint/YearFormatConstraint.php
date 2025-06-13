<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'year_format',
  label: new TranslatableMarkup('Already have reference to Programme: ', [], ['context' => 'Validation']),
)]
class YearFormatConstraint extends SymfonyConstraint {

  public $noRegexMatchMessage = 'The year value %value must be in an n/N format, where n and N are integers (numbers).';

  public $numberValueMismatchMessage = 'Invalid value: %value. The number before the "/" must be less or equal to the number after the "/".';

}