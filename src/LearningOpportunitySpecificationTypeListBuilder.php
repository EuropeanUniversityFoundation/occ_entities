<?php

declare(strict_types=1);

namespace Drupal\occ_entities;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of learning opportunity specification type entities.
 *
 * @see \Drupal\occ_entities\Entity\LearningOpportunitySpecificationType
 */
final class LearningOpportunitySpecificationTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Label');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();

    $build['table']['#empty'] = $this->t(
      'No learning opportunity specification types available. <a href=":link">Add learning opportunity specification type</a>.',
      [':link' => Url::fromRoute('entity.occ_los_type.add_form')->toString()],
    );

    return $build;
  }

}
