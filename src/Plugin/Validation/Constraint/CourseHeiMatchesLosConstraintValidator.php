<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OunitHeiMatches constraint.
 */
final class CourseHeiMatchesLosConstraintValidator extends ConstraintValidator
{

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $items, Constraint $constraint): void
  {
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
      $referenced_entity_hei_target_ids = $item->entity->hei->getValue();

      if ($referenced_hei_target_ids !== $referenced_entity_hei_target_ids) {
        /** @var CourseHeiMatchesLosConstraint $constraint */
        $this->context->buildViolation($constraint->message)
          ->setParameter('%course', $item->entity->label())
          ->atPath($delta)
          ->addViolation();
      }
    }
  }
}
