<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OunitHeiMatchesLos constraint.
 */
final class OunitHeiMatchesLosConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $item, Constraint $constraint): void {
    if (!$item instanceof EntityReferenceFieldItemList) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Field\EntityReferenceFieldItemList, %s was given.', get_debug_type($item))
      );
    }

    if ($item->isEmpty()) {
      return;
    }

    $referenced_hei_target_ids = $item->getEntity()->get('hei')->getValue();
    $ounit = $item->entity;
    if (!$ounit instanceof ContentEntityInterface || !$referenced_hei_target_ids) {
      return;
    }
    $referenced_entity_hei_target_ids = $ounit->get('parent_hei')->getValue();

    if ($referenced_hei_target_ids !== $referenced_entity_hei_target_ids) {
      /** @var OunitHeiMatchesLosConstraint $constraint */
      $this->context->buildViolation($constraint->message)
        ->setParameter('%ounit', $ounit->label())
        ->addViolation();
    }
  }

}
