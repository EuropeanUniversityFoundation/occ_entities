<?php

declare(strict_types=1);

namespace Drupal\occ_entities;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list builder for the learning opportunity instance entity type.
 */
final class LearningOpportunityInstanceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['status'] = $this->t('Status');
    $header['uid'] = $this->t('Author');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\occ_entities\Entity\LearningOpportunityInstanceInterface $entity */
    $row['id'] = $entity->toLink();
    $row['status'] = $entity->get('status')->value ? $this->t('Enabled') : $this->t('Disabled');
    /** @var \Drupal\user\UserInterface|null $owner */
    $owner = $entity->getOwner();
    $username_options = [
      'label' => 'hidden',
      'settings' => ['link' => $owner?->isAuthenticated() ?? FALSE],
    ];
    $row['uid']['data'] = $entity->get('uid')->view($username_options);
    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

}
