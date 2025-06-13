<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value is an entity that has a specific field.
 */
#[Constraint(
  id: 'unique_programme_reference',
  label: new TranslatableMarkup('Already have reference to Programme: ', [], ['context' => 'Validation']),
)]
class UniqueProgrammeReferenceConstraint extends SymfonyConstraint {

  public $message = 'A course can not be part of the same programme twice.';

}