<?php

declare(strict_types=1);

namespace Drupal\occ_entity_reference_selection\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\Entity\User;

/**
 * Plugin implementation of the Own HEIs Course selection plugin.
 */
#[EntityReferenceSelection(
  id: "occ_entity_reference_selection_course_selection",
  label: new TranslatableMarkup("Own HEIs Course selection"),
  group: "occ_entity_reference_selection_course_selection",
  entity_types: ['occ_los'],
  weight: 0,
)]
final class CourseSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS'): QueryInterface {
    $query = parent::buildEntityQuery($match, $match_operator);

    // @phpstan-ignore class.notFound
    $user = User::load($this->currentUser->id());

    if ($user->hasPermission('select any course')) {
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
