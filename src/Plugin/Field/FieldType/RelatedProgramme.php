<?php

namespace Drupal\occ_entities\Plugin\Field\FieldType;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 *
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

 class RelatedProgramme extends EntityReferenceItem {

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);
    $mandatory_definition = DataDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Mandatory'));

    $properties['mandatory'] = $mandatory_definition;

    $year_definition = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Year'));
    $properties['year'] = $year_definition;

    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $schema['columns']['mandatory'] = array(
      'type' => 'int',
      //'size' => 'tiny',
      'indexes' => [
        'mandatory' => ['value'],
      ],
    );

    $schema['columns']['year'] = array(
      'type' => 'text',
    );

    return $schema;
  }

  public static function getPreconfiguredOptions() {
    // In the base EntityReference class, this is used to populate the
    // list of field-types with options for each destination entity type.
    // This removes those options for each entity type.
    return [];
  }

}