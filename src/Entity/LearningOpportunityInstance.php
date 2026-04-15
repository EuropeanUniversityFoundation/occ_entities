<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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
 *     "label" = "label",
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
 *   constraints = {
 *     "EndDateGreaterOrEqualStartDate" = {}
 *   },
 * )
 */
final class LearningOpportunityInstance extends RevisionableContentEntityBase implements LearningOpportunityInstanceInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * Returns the mapping of LOI fields to Course fields for default values.
   */
  public static function getCourseDefaultFieldCopyMapping(): array {
    return [
      'ects' => 'course__ects',
      'language_of_instruction' => 'language_of_instruction',
      'course__elm_assessment_type' => 'course__elm_assessment_type',
      'course__elm_activity_type' => 'course__elm_activity_type',
      'course__elm_lo_type' => 'course__elm_lo_type',
      'course__elm_mode_of_learning' => 'course__elm_mode_of_learning',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    if (!$this->getOwnerId()) {
      $this->setOwnerId(0);
    }

    if (!$this->get('course')->isEmpty() && !$this->get('academic_term_id')->isEmpty()) {
      $course = $this->get('course')->entity;
      $label = $course->get('label')->value . ' | ' . $this->get('academic_term_id')->value;
      $this->set('label', $label);
    }

    if ($this->isNew()) {
      $this->fillDefaultsFromCourse();
    }
  }

  /**
   * Fills empty LOI fields with values from the related Course entity.
   *
   * Respects user intent when called from a form submission:
   * - If _from_form = TRUE, fields listed in _user_modified_fields are skipped.
   * - If called from API (no _from_form flag), all empty fields are filled.
   */
  private function fillDefaultsFromCourse(): void {
    $course = $this->get('course')->entity;
    if (!$course) {
      return;
    }

    $from_form = $this->_from_form ?? FALSE;
    $user_modified = $this->_user_modified_fields ?? [];

    foreach (self::getCourseDefaultFieldCopyMapping() as $loi_field => $course_field) {
      if (!$this->get($loi_field)->isEmpty()) {
        continue;
      }

      if ($from_form && in_array($loi_field, $user_modified)) {
        continue;
      }

      $this->set($loi_field, $course->get($course_field)->getValue());
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

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
      ->setDescription(new TranslatableMarkup('The time that the learning opportunity instance was created.'))
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
      ->setDescription(new TranslatableMarkup('The time that the learning opportunity instance was last edited.'));

    $fields['start_date'] = BaseFieldDefinition::create('datetime')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Start date'))
      ->setDescription(new TranslatableMarkup('The date the Learning Opportunity Instance starts'))
      ->setCardinality(1)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => -20,
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['end_date'] = BaseFieldDefinition::create('datetime')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('End date'))
      ->setDescription(new TranslatableMarkup('The date the Learning Opportunity Instance ends'))
      ->setCardinality(1)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => -20,
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['academic_term_id'] = BaseFieldDefinition::create('string')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Academic Term Identifier'))
      ->setDescription(new TranslatableMarkup('Academic Term Identifier in the format: YYYY-XXXX-t/T (e.g: 1900/1901-1/2)'))
      ->addConstraint('AcademicTermIdFormat', [])
      ->addConstraint('AcademicTermIdLogic', [])
      ->setSettings([
        'max_length' => 100,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setCardinality(1);

    $fields['ects'] = BaseFieldDefinition::create('decimal')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setLabel('ECTS')
      ->setDescription('ECTS credits obtained by completing the course.')
      ->setCardinality(1)
      ->setSettings([
        'precision' => 10,
        'scale' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'number_decimal',
        'weight' => -20,
        'settings' => [
          'thousand_separator' => '',
          'decimal_separator' => '.',
          'scale' => '2',
          'prefix_suffix' => TRUE,
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['language_of_instruction'] = BaseFieldDefinition::create('ewp_lang')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Language of Instruction'))
      ->setDescription(new TranslatableMarkup('Language(s) of Instruction for the Learning Opportunity Specification.'))
      ->setSettings([
        'max_length' => 100,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'ewp_lang_default',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ewp_lang_default',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['course'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Course Specification'))
      ->setSettings([
        'target_type' => 'occ_los',
        'handler' => 'default:occ_los',
        'handler_settings' => [
          'target_budles' => [
            'course' => 'course',
          ],
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => -80,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -80,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setLabel(new TranslatableMarkup('Label'))
      ->setDescription(new TranslatableMarkup('Computed label of the Learning Opportunity Specification.'))
      ->setReadOnly(TRUE)
      ->setTranslatable(FALSE);

    return $fields;
  }

}
