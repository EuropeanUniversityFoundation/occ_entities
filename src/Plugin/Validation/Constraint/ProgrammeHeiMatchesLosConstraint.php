<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Provides a ProgrammeHeiMatchesLos constraint.
 */
#[Constraint(
  id: 'ProgrammeHeiMatchesLos',
  label: new TranslatableMarkup('Programme references the same Institution.', [], ['context' => 'Validation'])
)]
final class ProgrammeHeiMatchesLosConstraint extends SymfonyConstraint {

  /**
   * The error message if the Programme does not reference the same Institution.
   *
   * @var string
   */
  public string $message = 'Programme %programme must reference the same HEI as this Learning Opportunity Specification.';

}
