<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the YearFormat constraint.
 */
class YearFormatConstraintValidator extends ConstraintValidator {

  public const SUBFIELD_NAME = 'year';

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    foreach ($value as $delta => $item) {
      $year_value = $item->{self::SUBFIELD_NAME};
      if ($year_value === NULL) {
        continue;
      }

      if (!preg_match('~\A\d+/\d+\z~', $year_value)) {
        /** @var YearFormatConstraint $constraint */
        $this->context->buildViolation($constraint->noRegexMatchMessage)
          ->setParameter('%value', $year_value)
          ->atPath((string) $delta)
          ->addViolation();
        continue;
      }

      $year_values = explode('/', $year_value);
      // A zero denominator means the course can be taken in any year.
      if ((int) $year_values[1] > 0 && (int) $year_values[0] > (int) $year_values[1]) {
        /** @var YearFormatConstraint $constraint */
        $this->context->buildViolation($constraint->numberValueMismatchMessage)
          ->setParameter('%value', $year_value)
          ->atPath((string) $delta)
          ->addViolation();
      }
    }
  }

}
