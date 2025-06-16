<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if combination of code and HEI reference is unique.
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
  public $code_field = 'code';

  /**
   * Field name storing the assosiated HEI.
   *
   * @var mixed
   */
  public $hei_field = 'hei';

  /**
   * Human readable name of the entity.
   *
   * @var string
   */
  public $entity_label = 'Learning Opportunity Specification';

  public function getRequiredOptions()
  {
    return ['code_field', 'hei_field', 'entity_label'];
  }
}
