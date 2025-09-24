<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a reference to a Programme occurs only once.
 */
#[Constraint(
  id: 'UniqueProgrammeReference',
  label: new TranslatableMarkup('Unique Programme reference', [], ['context' => 'Validation']),
)]
class UniqueProgrammeReferenceConstraint extends SymfonyConstraint {
  /**
   * The error message if the reference to a Programme is not unique.
   *
   * @var string
   */
  public $message = 'A course can not be part of the same programme twice.';

}
