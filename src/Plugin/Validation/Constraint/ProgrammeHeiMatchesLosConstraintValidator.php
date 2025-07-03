<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\Field\FieldItemListInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ProgrammeHeiMatches constraint.
 */
final class ProgrammeHeiMatchesLosConstraintValidator extends ConstraintValidator
{

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $items, Constraint $constraint): void
  {
    if (!$items instanceof FieldItemListInterface) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Field\FieldItemListInterface, %s was given.', get_debug_type($items))
      );
    }

    $referenced_hei_target_ids = $items->getEntity()->get('hei')->getValue();
    foreach ($items as $delta => $item) {
      $referenced_entity_hei_target_ids = $item->entity->hei->getValue();

      if ($referenced_hei_target_ids !== $referenced_entity_hei_target_ids) {
        /** @var ProgrammeHeiMatchesLosConstraint $constraint */
        $this->context->buildViolation($constraint->message)
          ->setParameter('%programme', $item->entity->label())
          ->atPath($delta)
          ->addViolation();
      }
    }
  }
}
