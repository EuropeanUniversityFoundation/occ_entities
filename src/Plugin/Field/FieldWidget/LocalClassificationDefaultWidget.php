<?php

namespace Drupal\occ_entities\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ewp_other_hei_id_default' widget.
 */
#[FieldWidget(
  id: 'occ_entities_local_classification_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['occ_entities_local_classification'],
)]
class LocalClassificationDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'type_size' => 60,
      'type_placeholder' => '',
      'value_size' => 60,
      'value_placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['type_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of type textfield'),
      '#default_value' => $this->getSetting('type_size'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $text = 'Text shown inside the form field until a value is entered.';
    $hint = 'Usually a sample value or description of the expected format.';

    $elements['type_placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type placeholder'),
      '#default_value' => $this->getSetting('type_placeholder'),
      '#description' => $this->t('@text @hint', [
        '@text' => $text,
        '@hint' => $hint,
      ]),
    ];

    $elements['value_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of value textfield'),
      '#default_value' => $this->getSetting('value_size'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $text = 'Text shown inside the form field until a value is entered.';
    $hint = 'Usually a sample value or description of the expected format.';

    $elements['value_placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value placeholder'),
      '#default_value' => $this->getSetting('value_placeholder'),
      '#description' => $this->t('@text @hint', [
        '@text' => $text,
        '@hint' => $hint,
      ]),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('Type textfield size: @size', [
      '@size' => $this->getSetting('type_size'),
    ]);

    if (!empty($this->getSetting('type_placeholder'))) {
      $summary[] = $this->t('Type placeholder: @placeholder', [
        '@placeholder' => $this->getSetting('type_placeholder'),
      ]);
    }

    $summary[] = $this->t('Value textfield size: @size', [
      '@size' => $this->getSetting('value_size'),
    ]);

    if (!empty($this->getSetting('value_placeholder'))) {
      $summary[] = $this->t('Value placeholder: @placeholder', [
        '@placeholder' => $this->getSetting('value_placeholder'),
      ]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = $element + [
      '#type' => 'container',
      '#attributes' => ['class' => ['inline-widget']],
    ];
    $element['#attached']['library'][] = 'ewp_core/inline_widget';

    // Get the field name from this particular field definiton.
    // $field_name = $items->getFieldDefinition()->getName();
    // Get the field defaults.
    $default_type = $items[$delta]->type ?? NULL;
    $default_value = $items[$delta]->value ?? NULL;

    $element['type'] = [
      '#type' => 'textfield',
      '#default_value' => $default_type,
      '#size' => $this->getSetting('type_size'),
      '#placeholder' => $this->getSetting('type_placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => ['class' => ['inline-shrink']],
    ];

    $element['value'] = [
      '#type' => 'textfield',
      '#default_value' => $default_value,
      '#size' => $this->getSetting('value_size'),
      '#placeholder' => $this->getSetting('value_placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => ['class' => ['inline-shrink']],
    ];

    // If cardinality is 1, ensure a proper label is output for the field.
    $cardinality = $this->fieldDefinition
      ->getFieldStorageDefinition()
      ->getCardinality();

    if ($cardinality === 1) {
      $element['type']['#title'] = $element['#title'];
      $element['value']['#title'] = '&nbsp;';
    }

    return $element;
  }

}
