<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides an Academic term id semantic constraint constraint.
 *
 * @Constraint(
 *   id = "AcademicTermIdLogic",
 *   label = @Translation("Academic term id logic constraint", context = "Validation"),
 * )
 */
class AcademicTermIdLogicConstraint extends Constraint {

  /**
   * Year logic error message.
   *
   * @var string
   */
  public string $yearMessage = 'The first (%year1) and second year (%year2) must be conscecutive.';

  /**
   * Term logic error message.
   *
   * @var string
   */
  public string $termMessage = 'The first term (%term1) can not be greater than the second (%term2).';

}
