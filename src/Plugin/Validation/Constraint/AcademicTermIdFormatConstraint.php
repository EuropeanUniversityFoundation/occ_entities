<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides an Academic term REGEX constraint constraint.
 *
 * @Constraint(
 *   id = "AcademicTermIdFormat",
 *   label = @Translation("Academic term format constraint", context = "Validation"),
 * )
 */
class AcademicTermIdFormatConstraint extends Constraint {

  /**
   * Regex match failed error.
   *
   * @var string
   */
  public string $message = 'The Academic Term Id must be in the format YYYY/XXXX-t/T';

}
