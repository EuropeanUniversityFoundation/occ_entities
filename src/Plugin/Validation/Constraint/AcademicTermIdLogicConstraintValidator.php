<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks Academic term id value logic.
 */
final class AcademicTermIdLogicConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    // If field is empty, return.
    if (!isset($value) || $value->isEmpty()) {
      return;
    }

    $item_value = (string) $value->first()->value;

    // If REGEX fails, return.
    if (!preg_match('/\A(\d{4})\/(\d{4})-(\d)\/(\d)\z/', $item_value, $matches)) {
      return;
    }

    // $matches[1] = YYYY, [2] = XXXX, [3] = t, [4] = T.
    $year1 = (int) $matches[1];
    $year2 = (int) $matches[2];
    $term1 = (int) $matches[3];
    $term2 = (int) $matches[4];

    /** @var \Drupal\occ_entities\Plugin\Validation\Constraint\AcademicTermIdLogicConstraint $constraint */
    // Year logic: YYYY = XXXX - 1.
    if ($year1 !== ($year2 - 1)) {
      $this->context->buildViolation($constraint->yearMessage)
        ->setParameter('%year1', (string) $year1)
        ->setParameter('%year2', (string) $year2)
        ->addViolation();
    }

    if ($term1 === 0 || $term2 === 0) {
      $this->context->addViolation($constraint->positiveTermMessage);
      return;
    }

    // Term logic: t <= T.
    if ($term1 > $term2) {
      $this->context->buildViolation($constraint->termMessage)
        ->setParameter('%term1', (string) $term1)
        ->setParameter('%term2', (string) $term2)
        ->addViolation();
    }
  }

}
