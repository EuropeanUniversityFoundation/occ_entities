<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Util\PropertyPath;
use Symfony\Component\Validator\ConstraintValidator;

class YearFormatConstraintValidator extends ConstraintValidator
{

  public const SUBFIELD_NAME = 'year';

  public function validate($value, Constraint $constraint)
  {
    foreach ($value as $delta => $item) {
      $year_value = $item->{self::SUBFIELD_NAME};
      if (!preg_match('~^\d+/\d+$~', $year_value)) {
        /** @var YearFormatConstraint $constraint */
        $this->context->buildViolation($constraint->noRegexMatchMessage)
          ->setParameter('%value', $year_value)
          ->atPath($delta)
          ->addViolation();
      }

      if (str_contains($year_value, '/')) {
        $year_values = explode('/', $year_value);
        if ($year_values[0] > $year_values[1]) {
          /** @var YearFormatConstraint $constraint */
          $this->context->buildViolation($constraint->numberValueMismatchMessage)
            ->setParameter('%value', $year_value)
            ->atPath($delta)
            ->addViolation();
        }
      }
    }
  }
}
