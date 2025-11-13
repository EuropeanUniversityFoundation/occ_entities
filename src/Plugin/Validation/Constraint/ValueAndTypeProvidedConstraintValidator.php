<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ValueAndTypeProvided constraint.
 */
class ValueAndTypeProvidedConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    foreach ($value as $delta => $item) {

      $filled = 0;
      foreach ($item as $property_value) {
        if ($property_value->getValue() !== NULL && $property_value->getValue() !== '') {
          $filled++;
        }
      }

      if ($filled === 1) {
        /** @var ValueAndTypeProvidedConstraint $constraint */
        $this->context->buildViolation($constraint->message)
          ->atPath((string) $delta)
          ->addViolation();
      }
    }
  }

}
