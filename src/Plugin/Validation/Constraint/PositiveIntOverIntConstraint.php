<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Drupal\Core\Validation\Attribute\Constraint;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Checks if a value complies with the format: n/N; n > 0; N > 0.
 */
#[Constraint(
  id: 'PositiveIntOverInt',
  label: new TranslatableMarkup('PositiveIntOverInt', [], ['context' => 'Validation']),
)]
class PositiveIntOverIntConstraint extends SymfonyConstraint {
  /**
   * The error message if there is no Regex match.
   *
   * @var string
   */
  public $noRegexMatchMessage = 'Value %value must be in an n/N format, where n and N are integers (numbers).';

  /**
   * The error message if n <= 0 or N <= 0.
   *
   * @var string
   */
  public $termsNotPositiveMessage = 'Value %value must be in an n/N format, where n and N are greater than zero.';

}
