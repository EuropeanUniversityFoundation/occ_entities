<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\Field\FieldItemList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the PositiveIntOverInt constraint.
 */
class PositiveIntOverIntConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (!$value instanceof FieldItemList) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Field\FieldItemList, %s was given.', get_debug_type($value))
      );
    }

    if ($value->isEmpty()) {
      return;
    }

    foreach ($value as $delta => $item) {
      $field_value = $item->get('value')->getValue();
      if (!preg_match('~^\d+/\d+$~', $field_value)) {
        /** @var PositiveIntOverIntConstraint $constraint */
        $this->context->buildViolation($constraint->noRegexMatchMessage)
          ->setParameter('%value', $field_value)
          ->atPath((string) $delta)
          ->addViolation();
      }

      if (str_contains($field_value, '/')) {
        $field_values = explode('/', $field_value);
        // Ensures n/N: N > 0 and n > 0.
        if ((int) $field_values[1] <= 0 || (int) $field_values[0] <= 0) {
          /** @var PositiveIntOverIntConstraint $constraint */
          $this->context->buildViolation($constraint->termsNotPositiveMessage)
            ->setParameter('%value', $field_value)
            ->atPath((string) $delta)
            ->addViolation();
        }
      }
    }
  }

}
