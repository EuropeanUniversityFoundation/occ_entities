<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValueAndTypeProvidedConstraintValidator extends ConstraintValidator
{

  public function validate($value, Constraint $constraint)
  {
    foreach ($value as $delta => $item) {

      $filled = 0;
      foreach ($item as $subfield => $property_value) {
        if ($property_value->getValue() !== NULL && $property_value->getValue() !== '') {
          $filled++;
        }
      }

      if ($filled === 1) {
        /** @var ValueAndTypeProvidedConstraint $constraint */
        $this->context->buildViolation($constraint->message)
          ->atPath($delta)
          ->addViolation();
      }
    }
  }
}
