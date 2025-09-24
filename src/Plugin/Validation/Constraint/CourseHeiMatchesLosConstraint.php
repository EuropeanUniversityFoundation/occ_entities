<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Provides a CourseHeiMatchesLos constraint.
 */
#[Constraint(
  id: 'CourseHeiMatchesLos',
  label: new TranslatableMarkup('Courses reference the same Institution.', [], ['context' => 'Validation'])
)]
final class CourseHeiMatchesLosConstraint extends SymfonyConstraint {

  /**
   * The error message if Courses do not reference the same Institution.
   *
   * @var string
   */
  public string $message = 'Course %course must reference the same Institution as this Course.';

}
