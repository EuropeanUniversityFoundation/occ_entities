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

  /**
   * {@inheritdoc}
   */
  public function getRequiredOptions() {
    return ['code_field', 'hei_field', 'entity_label'];
  }

}
