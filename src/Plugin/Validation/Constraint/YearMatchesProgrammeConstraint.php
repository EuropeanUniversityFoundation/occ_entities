<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'year_matches_programme',
  label: new TranslatableMarkup('Year does not match programme years: ', [], ['context' => 'Validation']),
)]
class YearMatchesProgrammeConstraint extends SymfonyConstraint {

  public $message = 'The related programme %programme_label is %programme_length years long. The corresponding year value must be formatted: X/%programme_length.';

}