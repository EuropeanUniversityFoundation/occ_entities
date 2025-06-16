<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\user\EntityOwnerTrait;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\occ_entities\Entity\LearningOpportunitySpecificationInterface;

/**
 * Defines the learning opportunity specification entity class.
 *
 * @ContentEntityType(
 *   id = "occ_los",
 *   label = @Translation("Learning Opportunity Specification"),
 *   label_collection = @Translation("Learning Opportunity Specifications"),
 *   label_singular = @Translation("Learning Opportunity Specification"),
 *   label_plural = @Translation("Learning Opportunity Specifications"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Learning Opportunity Specifications",
 *     plural = "@count Learning Opportunity Specifications",
 *   ),
 *   bundle_label = @Translation("Learning Opportunity Specification type"),
 *   handlers = {
 *     "list_builder" = "Drupal\occ_entities\LearningOpportunitySpecificationListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\occ_entities\LearningOpportunitySpecificationAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\occ_entities\Form\LearningOpportunitySpecificationForm",
 *       "edit" = "Drupal\occ_entities\Form\LearningOpportunitySpecificationForm",
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
 *   base_table = "occ_los",
 *   revision_table = "occ_los_revision",
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer occ_los types",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "bundle",
 *     "label" = "uuid",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/occ-los",
 *     "add-form" = "/occ/los/add/{occ_los_type}",
 *     "add-page" = "/occ/los/add",
 *     "canonical" = "/occ/los/{occ_los}",
 *     "edit-form" = "/occ/los/{occ_los}/edit",
 *     "delete-form" = "/occ/los/{occ_los}/delete",
 *     "delete-multiple-form" = "/admin/content/occ-los/delete-multiple",
 *     "revision" = "/occ/los/{occ_los}/revision/{occ_los_revision}/view",
 *     "revision-delete-form" = "/occ/los/{occ_los}/revision/{occ_los_revision}/delete",
 *     "revision-revert-form" = "/occ/los/{occ_los}/revision/{occ_los_revision}/revert",
 *     "version-history" = "/occ/los/{occ_los}/revisions",
 *   },
 *   bundle_entity_type = "occ_los_type",
 *   field_ui_base_route = "entity.occ_los_type.edit_form",
 *   common_reference_target = TRUE,
 *   revisionable = TRUE,
 *   translatable = FALSE,
 *   constraints = {
 *     "code_and_hei_unique" = {
 *       "code_field" = "code",
 *       "hei_field" = "hei",
 *       "entity_label" = "Learning Opportunity Specification",
 *       "errorPath" = "code",
 *     }
 *   },
 * )
 */
final class LearningOpportunitySpecification extends RevisionableContentEntityBase implements LearningOpportunitySpecificationInterface
{

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  // TODO: review
  public function preSave(EntityStorageInterface $storage): void
  {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  public function label(): string
  {
    return $this->get('title')->getValue()[0]['string'];
  }

  public function getLabel(): string
  {
    return $this->get('title')->getValue()[0]['string'];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Status'))
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
      ->setLabel(new TranslatableMarkup('Author'))
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
      ->setLabel(new TranslatableMarkup('Authored on'))
      ->setDescription(new TranslatableMarkup('The time that the learning opportunity specification was created.'))
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
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('The time that the learning opportunity specification was last edited.'));

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Code'))
      ->setDescription(new TranslatableMarkup('Code of the Learning Opportunity Specification.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE)
      ->setTranslatable(FALSE);

    $fields['abbreviation'] = BaseFieldDefinition::create('ewp_string_lang')
      ->setLabel(new TranslatableMarkup('Abbreviation'))
      ->setDescription(new TranslatableMarkup('Abbreviation of the Learning Opportunity Specification.'))
      ->setSettings([
        'max_length' => 255,
        //'text_processing' => 0,
      ])
      //->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'ewp_string_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_string_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE)
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['title'] = BaseFieldDefinition::create('ewp_string_lang')
      ->setLabel(new TranslatableMarkup('Title'))
      ->setDescription(new TranslatableMarkup('Title of the Learning Opportunity Specification.'))
      ->setSettings([
        'max_length' => 255,
        //'text_processing' => 0,
      ])
      //->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'ewp_string_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_string_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE)
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['description'] = BaseFieldDefinition::create('ewp_multiline_lang')
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDescription(new TranslatableMarkup('Description of the Learning Opportunity Specification.'))
      //->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'ewp_multiline_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_multiline_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE)
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['learning_outcomes'] = BaseFieldDefinition::create('ewp_multiline_lang')
      ->setLabel(new TranslatableMarkup('Learning outcomes'))
      ->setDescription(new TranslatableMarkup('Learning outcomes of the Learning Opportunity Specification.'))
      //->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'ewp_multiline_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_multiline_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE)
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['url'] = BaseFieldDefinition::create('ewp_http_lang')
      ->setLabel(new TranslatableMarkup('URL'))
      ->setDescription(new TranslatableMarkup('External URLs of the Learning Opportunity Specification.'))
      //->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'ewp_http_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_http_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE)
      ->setTranslatable(FALSE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['hei'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Institution'))
      ->setSetting('target_type', 'hei')
      ->setSetting('handler', 'default:hei')
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
        'type' => 'string',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['ounit'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Organizational Unit'))
      ->setSetting('target_type', 'ounit')
      ->setSetting('handler', 'default:ounit')
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
        'type' => 'string',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
