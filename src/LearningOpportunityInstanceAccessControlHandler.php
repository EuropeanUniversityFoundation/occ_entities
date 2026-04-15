<?php

declare(strict_types=1);

namespace Drupal\occ_entities;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the learning opportunity instance entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class LearningOpportunityInstanceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view occ_loi'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit occ_loi'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete occ_loi'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete occ_loi revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view occ_loi revision', 'view occ_loi']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert occ_loi revision', 'edit occ_loi']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create occ_loi', 'administer occ_loi types'], 'OR');
  }

}
