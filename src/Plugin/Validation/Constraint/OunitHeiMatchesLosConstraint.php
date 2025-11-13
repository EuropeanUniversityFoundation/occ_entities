<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Provides a OunitHeiMatchesLos constraint.
 */
#[Constraint(
  id: 'OunitHeiMatchesLos',
  label: new TranslatableMarkup('OUnit and LOS reference the same Institution.', [], ['context' => 'Validation'])
)]
final class OunitHeiMatchesLosConstraint extends SymfonyConstraint {

  /**
   * The error message.
   *
   * The error message if the Organizational Unit and the Learning Opportunity
   * Specification do not reference the same Institution.
   *
   * @var string
   */
  public string $message = 'Organizational Unit %ounit must reference the same HEI as this Learning Opportunity Specification.';

}
