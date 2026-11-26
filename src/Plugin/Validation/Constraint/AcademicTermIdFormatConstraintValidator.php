<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Academic term REGEX constraint constraint.
 */
final class AcademicTermIdFormatConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {

    if ($value === NULL || $value->isEmpty()) {
      return;
    }

    $item_value = $value->first()->value;

    if (!preg_match('/\A\d{4}\/\d{4}-\d\/\d\z/', $item_value)) {
      /** @var \Drupal\occ_entities\Plugin\Validation\Constraint\AcademicTermIdFormatConstraint $constraint */
      $this->context->addViolation($constraint->message);
    }
  }

}
