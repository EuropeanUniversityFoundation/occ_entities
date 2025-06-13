<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if combnation of code and HEI reference is unique.
 *
 * @Constraint(
 *   id = "code_and_hei_unique",
 *   label = @Translation("The combination of the code and the Institution must be unique.", context = "Validation"),
 * )
 */
class UniqueCodeAndHeiConstraint extends Constraint
{

  public $message = 'The %entity_label code: %code is already in use for this Institution: %hei.';

  /**
   * Field name storing the code of the entity.
   *
   * @var string
   */
  public $code_field;

  /**
   * Field name storing the assosiated HEI.
   *
   * @var mixed
   */
  public $hei_field;

  /**
   * Human readable name of the entity.
   *
   * @var string
   */
  public $entity_label;
}
