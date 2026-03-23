<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides an End date greater or equal to start date constraint.
 *
 * @Constraint(
 *   id = "EndDateGreaterOrEqualStartDate",
 *   label = @Translation("End date greater or equal to start date", context = "Validation"),
 * )
 *
 * @see https://www.drupal.org/node/2015723.
 */
final class EndDateGreaterOrEqualStartDateConstraint extends Constraint {

  public $message = 'End date (%end) can not be sooner, than start date (%start).';

}
