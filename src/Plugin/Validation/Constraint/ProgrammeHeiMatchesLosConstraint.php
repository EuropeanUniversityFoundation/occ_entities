<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a ProgrammeHeiMatches constraint.
 *
 * @Constraint(
 *   id = "programme_hei_matches_los",
 *   label = @Translation("ProgrammeHeiMatches", context = "Validation"),
 * )
 *
 * @DCG
 * To apply this constraint on third party entity types implement either
 * hook_entity_base_field_info_alter() or hook_entity_bundle_field_info_alter().
 *
 * @see https://www.drupal.org/node/2015723
 */
final class ProgrammeHeiMatchesLosConstraint extends Constraint
{

  public string $message = 'Programme %programme must reference the same HEI as this Learning Opportunity Specification.';
}
