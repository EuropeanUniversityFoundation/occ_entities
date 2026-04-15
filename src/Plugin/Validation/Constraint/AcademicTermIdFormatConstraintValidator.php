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

    if ($value === NULL || $value === '') {
      return;
    }

    $item_value = $value->first()->value;

    if (!preg_match('/^\d{4}\/\d{4}-\d\/\d$/', $item_value)) {
      $this->context->addViolation($constraint->message);
    }
  }

}
