<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a learning opportunity specification entity type.
 */
interface LearningOpportunitySpecificationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
