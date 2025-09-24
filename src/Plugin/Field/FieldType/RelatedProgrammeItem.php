<?php

namespace Drupal\occ_entities\Plugin\Field\FieldType;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'related_programme' field type.
 *
 * @FieldType(
 *   id = "related_programme",
 *   label = @Translation("Related Programme"),
 *   description = {@Translation("A field containing a reference to a programme, mandatory flag and year.")},
 *   category = "occ_entities",
 *   default_widget = "related_programme_autocomplete",
 *   default_formatter = "related_programme_view",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class RelatedProgrammeItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    $settings = parent::defaultStorageSettings();
    $settings['target_type'] = 'occ_los';
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element['target_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of item to reference'),
      '#default_value' => $this->getSetting('target_type'),
      '#required' => TRUE,
      '#disabled' => $has_data,
      '#size' => 1,
    ];

    $options = \Drupal::service('entity_type.repository')->getEntityTypeLabels(FALSE);
    $element['target_type']['#options'] = ['occ_los' => $options['occ_los']];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['mandatory'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Mandatory'))
      ->setRequired(TRUE);

    $properties['year'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Year'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['mandatory'] = [
      'type' => 'int',
      'size' => 'tiny',
      'indexes' => [
        'mandatory' => ['value'],
      ],
    ];

    $schema['columns']['year'] = [
      'type' => 'text',
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    // In the base EntityReference class, this is used to populate the
    // list of field-types with options for each destination entity type.
    // This removes those options for each entity type.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    // $mandatory = $this->get('mandatory')->getValue();
    // if ($mandatory === NULL) {
    // return TRUE;
    // }
    // $year = $this->get('year')->getValue();
    // if ($year === NULL || $year === '') {
    // return TRUE;
    // }
    return parent::isEmpty();
  }

}
