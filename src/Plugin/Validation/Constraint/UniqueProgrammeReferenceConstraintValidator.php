<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueProgrammeReferenceConstraintValidator extends ConstraintValidator {

  public const SUBFIELD_NAME = 'target_id';

  public function validate($value, Constraint $constraint) {

    $referenced_ids = [];
    foreach ($value as $item) {
      $target_id = $item->{self::SUBFIELD_NAME};
      if (!in_array($target_id, $referenced_ids)) {
        $referenced_ids[] = $target_id;
      } else {
        /** @var UniqueProgrammeReferenceConstraint $constraint */
        $this->context->addViolation($constraint->message);
      }
    }
  }

}