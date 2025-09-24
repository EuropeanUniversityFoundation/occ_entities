<?php

declare(strict_types=1);

namespace Drupal\occ_entity_reference_selection\Plugin\EntityReferenceSelection;

use Drupal\user\Entity\User;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * @todo Add plugin description here.
 *
 * @EntityReferenceSelection(
 *   id = "occ_entity_reference_selection_ounit_selection",
 *   label = @Translation("Own HEIs OUnit selection"),
 *   group = "occ_entity_reference_selection_ounit_selection",
 *   entity_types = {"ounit"},
 * )
 */
final class OunitSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    // @phpstan-ignore class.notFound
    $user = User::load($this->currentUser->id());

    if ($user->hasPermission('select any ounit')) {
      return $query;
    }

    $user_heis = $user->get('user_institution')->getValue();
    $user_has_access_to_heis = [];
    foreach ($user_heis as $user_hei) {
      $user_has_access_to_heis[] = $user_hei['target_id'];
    }

    $query->condition('parent_hei', $user_has_access_to_heis, 'IN');

    return $query;
  }

}
