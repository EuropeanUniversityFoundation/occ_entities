<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the CourseHeiMatchesLos constraint.
 */
final class CourseHeiMatchesLosConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $items, Constraint $constraint): void {
    if (!$items instanceof EntityReferenceFieldItemList) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Field\EntityReferenceFieldItemList, %s was given.', get_debug_type($items))
      );
    }

    if ($items->isEmpty()) {
      return;
    }

    foreach ($items as $delta => $item) {
      $referenced_hei_target_ids = $item->getEntity()->get('hei')->getValue();
      $course = $item->entity;
      if (!$course || !$referenced_hei_target_ids) {
        continue;
      }
      $referenced_entity_hei_target_ids = $course->get('hei')->getValue();

      if ($referenced_hei_target_ids !== $referenced_entity_hei_target_ids) {
        /** @var \Drupal\occ_entities\Plugin\Validation\Constraint\CourseHeiMatchesLosConstraint $constraint */
        $this->context->buildViolation($constraint->message)
          ->setParameter('%course', $course->label())
          ->atPath((string) $delta)
          ->addViolation();
      }
    }
  }

}
