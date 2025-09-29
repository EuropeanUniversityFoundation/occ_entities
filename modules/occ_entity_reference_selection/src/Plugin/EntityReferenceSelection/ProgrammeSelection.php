<?php

declare(strict_types=1);

namespace Drupal\occ_entity_reference_selection\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\Entity\User;

/**
 * Plugin implementation of the Own HEIs Programme selection plugin.
 */
#[EntityReferenceSelection(
  id: "occ_entity_reference_selection_programme_selection",
  label: new TranslatableMarkup("Own HEIs Programme selection"),
  group: "occ_entity_reference_selection_programme_selection",
  entity_types: ['occ_los'],
  weight: 0,
)]
final class ProgrammeSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    $user = User::load($this->currentUser->id());

    if ($user->hasPermission('select any programme')) {
      return $query;
    }

    $user_heis = $user->get('user_institution')->getValue();
    $user_has_access_to_heis = [];
    foreach ($user_heis as $user_hei) {
      $user_has_access_to_heis[] = $user_hei['target_id'];
    }

    $query->condition('hei', $user_has_access_to_heis, 'IN');

    return $query;
  }

}
