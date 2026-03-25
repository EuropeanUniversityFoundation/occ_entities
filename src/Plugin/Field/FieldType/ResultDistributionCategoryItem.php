<?php

namespace Drupal\occ_entities\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'result_distribution_category' field type.
 */
#[FieldType(
  id: "result_distribution_category",
  module: "occ_entities",
  label: new TranslatableMarkup("Result Distribution Category"),
  description: [
    new TranslatableMarkup("Stores a histogram category label and student count pair."),
    new TranslatableMarkup("Label may contain ranges like '10-20' or grade letters like 'A'."),
  ],
  category: "occ_entities",
  default_widget: "result_distribution_category_default",
  default_formatter: "result_distribution_category_default",
)]
class ResultDistributionCategoryItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    return [
      'max_length' => 255,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Category label'))
      ->setRequired(TRUE);

    $properties['count'] = DataDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Student count'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'label' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => TRUE,
          'default' => '',
        ],
        'count' => [
          'type' => 'int',
          'size' => 'normal',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'default' => 0,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): string {
    return 'label';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $label = $this->get('label')->getValue();
    return $label === NULL || $label === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();

    if ($max_length = $this->getSetting('max_length')) {
      $constraints[] = $constraint_manager->create('ComplexData', [
        'label' => [
          'Length' => [
            'max' => $max_length,
            'maxMessage' => $this->t(
              '%name: the label may not be longer than @max characters.',
              ['%name' => $this->getFieldDefinition()->getLabel(), '@max' => $max_length]
            ),
          ],
        ],
      ]);
    }

    $constraints[] = $constraint_manager->create('ComplexData', [
      'count' => [
        'Range' => [
          'min' => 0,
          'minMessage' => $this->t(
            '%name: the student count must be 0 or greater.',
            ['%name' => $this->getFieldDefinition()->getLabel()]
          ),
        ],
      ],
    ]);

    return $constraints;
  }

}
