<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a year value is within the Programme length.
 */
#[Constraint(
  id: 'YearMatchesProgramme',
  label: new TranslatableMarkup('Year within Programme length', [], ['context' => 'Validation']),
)]
class YearMatchesProgrammeConstraint extends SymfonyConstraint {
  /**
   * The error message if the year is not within the Programme length.
   *
   * @var string
   */
  public $message = 'The related programme %programme_label is %programme_length years long. The corresponding year value must be formatted: X/%programme_length where X is less than or equal to %programme_length.';

}
