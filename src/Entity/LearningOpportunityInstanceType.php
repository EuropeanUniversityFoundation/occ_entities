<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Learning Opportunity Instance type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "occ_loi_type",
 *   label = @Translation("Learning Opportunity Instance type"),
 *   label_collection = @Translation("Learning Opportunity Instance types"),
 *   label_singular = @Translation("learning opportunity instance type"),
 *   label_plural = @Translation("learning opportunity instances types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count learning opportunity instances type",
 *     plural = "@count learning opportunity instances types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\occ_entities\Form\LearningOpportunityInstanceTypeForm",
 *       "edit" = "Drupal\occ_entities\Form\LearningOpportunityInstanceTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\occ_entities\LearningOpportunityInstanceTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer occ_loi types",
 *   bundle_of = "occ_loi",
 *   config_prefix = "occ_loi_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/occ_loi_types/add",
 *     "edit-form" = "/admin/structure/occ_loi_types/manage/{occ_loi_type}",
 *     "delete-form" = "/admin/structure/occ_loi_types/manage/{occ_loi_type}/delete",
 *     "collection" = "/admin/structure/occ_loi_types",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   },
 * )
 */
final class LearningOpportunityInstanceType extends ConfigEntityBundleBase {

  /**
   * The machine name of this learning opportunity instance type.
   */
  protected string $id;

  /**
   * The human-readable name of the learning opportunity instance type.
   */
  protected string $label;

}
