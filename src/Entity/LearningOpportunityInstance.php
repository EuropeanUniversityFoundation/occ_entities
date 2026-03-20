<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\occ_entities\Entity\LearningOpportunityInstanceInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the learning opportunity instance entity class.
 *
 * @ContentEntityType(
 *   id = "occ_loi",
 *   label = @Translation("Learning Opportunity Instance"),
 *   label_collection = @Translation("Learning Opportunity Instances"),
 *   label_singular = @Translation("learning opportunity instance"),
 *   label_plural = @Translation("learning opportunity instances"),
 *   label_count = @PluralTranslation(
 *     singular = "@count learning opportunity instances",
 *     plural = "@count learning opportunity instances",
 *   ),
 *   bundle_label = @Translation("Learning Opportunity Instance type"),
 *   handlers = {
 *     "list_builder" = "Drupal\occ_entities\LearningOpportunityInstanceListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\occ_entities\LearningOpportunityInstanceAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\occ_entities\Form\LearningOpportunityInstanceForm",
 *       "edit" = "Drupal\occ_entities\Form\LearningOpportunityInstanceForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *   },
 *   base_table = "occ_loi",
 *   revision_table = "occ_loi_revision",
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer occ_loi types",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "bundle",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/occ-loi",
 *     "add-form" = "/occ-loi/add/{occ_loi_type}",
 *     "add-page" = "/occ-loi/add",
 *     "canonical" = "/occ-loi/{occ_loi}",
 *     "edit-form" = "/occ-loi/{occ_loi}/edit",
 *     "delete-form" = "/occ-loi/{occ_loi}/delete",
 *     "delete-multiple-form" = "/admin/content/occ-loi/delete-multiple",
 *     "revision" = "/occ-loi/{occ_loi}/revision/{occ_loi_revision}/view",
 *     "revision-delete-form" = "/occ-loi/{occ_loi}/revision/{occ_loi_revision}/delete",
 *     "revision-revert-form" = "/occ-loi/{occ_loi}/revision/{occ_loi_revision}/revert",
 *     "version-history" = "/occ-loi/{occ_loi}/revisions",
 *   },
 *   bundle_entity_type = "occ_loi_type",
 *   field_ui_base_route = "entity.occ_loi_type.edit_form",
 * )
 */
final class LearningOpportunityInstance extends RevisionableContentEntityBase implements LearningOpportunityInstanceInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the learning opportunity instance was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the learning opportunity instance was last edited.'));

    return $fields;
  }

}
