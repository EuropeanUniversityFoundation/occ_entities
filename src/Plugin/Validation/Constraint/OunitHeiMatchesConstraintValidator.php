<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OunitHeiMatches constraint.
 */
final class OunitHeiMatchesConstraintValidator extends ConstraintValidator
{

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $item, Constraint $constraint): void
  {
    if (!$item instanceof EntityReferenceFieldItemList) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Field\EntityReferenceFieldItemList, %s was given.', get_debug_type($item))
      );
    }

    $referenced_hei_target_ids = $item->getEntity()->get('hei')->getValue();
    // @phpstan-ignore property.notFound
    $referenced_entity_hei_target_ids = $item->entity->parent_hei->getValue();

    if ($referenced_hei_target_ids !== $referenced_entity_hei_target_ids) {
      /** @var OunitHeiMatchesConstraint $constraint */
      $this->context->buildViolation($constraint->message)
        // @phpstan-ignore property.notFound
        ->setParameter('%ounit', $item->entity->label())
        ->addViolation();
    }
  }
}
