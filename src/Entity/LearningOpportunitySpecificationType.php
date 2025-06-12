<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Learning Opportunity Specification type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "occ_los_type",
 *   label = @Translation("Learning Opportunity Specification type"),
 *   label_collection = @Translation("Learning Opportunity Specification types"),
 *   label_singular = @Translation("learning opportunity specification type"),
 *   label_plural = @Translation("learning opportunity specifications types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count learning opportunity specifications type",
 *     plural = "@count learning opportunity specifications types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\occ_entities\Form\LearningOpportunitySpecificationTypeForm",
 *       "edit" = "Drupal\occ_entities\Form\LearningOpportunitySpecificationTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\occ_entities\LearningOpportunitySpecificationTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer occ_los types",
 *   bundle_of = "occ_los",
 *   config_prefix = "occ_los_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/occ_los_types/add",
 *     "edit-form" = "/admin/structure/occ_los_types/manage/{occ_los_type}",
 *     "delete-form" = "/admin/structure/occ_los_types/manage/{occ_los_type}/delete",
 *     "collection" = "/admin/structure/occ_los_types",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   },
 * )
 */
final class LearningOpportunitySpecificationType extends ConfigEntityBundleBase {

  /**
   * The machine name of this learning opportunity specification type.
   */
  protected string $id;

  /**
   * The human-readable name of the learning opportunity specification type.
   */
  protected string $label;

}
