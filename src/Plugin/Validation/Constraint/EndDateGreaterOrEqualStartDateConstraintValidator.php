<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the End date greater or equal to start date constraint.
 */
final class EndDateGreaterOrEqualStartDateConstraintValidator extends ConstraintValidator {

  public const START_DATE_FIELD = 'start_date';
  public const END_DATE_FIELD = 'end_date';

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $entity, Constraint $constraint): void {
    if (!$entity instanceof EntityInterface) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Entity\EntityInterface, %s was given.', get_debug_type($entity))
      );
    }
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $start_date = $entity->get(self::START_DATE_FIELD)->value;
    $end_date = $entity->get(self::END_DATE_FIELD)->value;

    if (!$start_date || !$end_date) {
      return;
    }

    if (strtotime($end_date) < strtotime($start_date)) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('%start', $start_date)
        ->setParameter('%end', $end_date)
        ->atPath(self::END_DATE_FIELD)
        ->addViolation();
    }
  }

}
