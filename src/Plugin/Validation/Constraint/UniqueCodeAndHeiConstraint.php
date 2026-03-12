<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Checks if combination of code and HEI reference is unique.
 */
#[Constraint(
  id: 'UniqueCodeAndHei',
  label: new TranslatableMarkup('Unique code within an Institution.', [], ['context' => 'Validation'])
)]
class UniqueCodeAndHeiConstraint extends SymfonyConstraint {

  /**
   * The error message if a code is already in use within an Institution.
   *
   * @var string
   */
  public $message = 'The code: %code is already in use for this Institution: %hei.';

  /**
   * Field name storing the code of the entity.
   *
   * @var string
   */
  public $codeField = 'code';

  /**
   * Field name storing the assosiated HEI.
   *
   * @var mixed
   */
  public $heiField = 'hei';

  /**
   * Human readable name of the entity.
   *
   * @var string
   */
  public $entityLabel = 'Learning Opportunity Specification';

  /**
   * {@inheritdoc}
   */
  public function getRequiredOptions() {
    return ['codeField', 'heiField'];
  }

}
