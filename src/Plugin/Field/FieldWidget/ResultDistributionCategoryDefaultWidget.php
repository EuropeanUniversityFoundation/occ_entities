<?php

namespace Drupal\occ_entities\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
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
  id: 'result_distribution_category_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['result_distribution_category'],
)]
class ResultDistributionCategoryDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
      'label_size' => 60,
      'label_placeholder' => '',
      'count_size' => 10,
      'count_placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['label_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of label textfield'),
      '#default_value' => $this->getSetting('label_size'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $text = 'Text shown inside the form field until a value is entered.';
    $hint = 'Usually a sample value or description of the expected format.';

    $elements['label_placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label placeholder'),
      '#default_value' => $this->getSetting('label_placeholder'),
      '#description' => $this->t('@text @hint', [
        '@text' => $text,
        '@hint' => $hint,
      ]),
    ];

    $elements['count_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of count number field'),
      '#default_value' => $this->getSetting('count_size'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $text = 'Text shown inside the form field until a value is entered.';
    $hint = 'Usually a sample value or description of the expected format.';

    $elements['count_placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Count placeholder'),
      '#default_value' => $this->getSetting('count_placeholder'),
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

    $summary[] = $this->t('Label textfield size: @size', [
      '@size' => $this->getSetting('label_size'),
    ]);

    if (!empty($this->getSetting('label_placeholder'))) {
      $summary[] = $this->t('Label placeholder: @placeholder', [
        '@placeholder' => $this->getSetting('label_placeholder'),
      ]);
    }

    $summary[] = $this->t('Count number field size: @size', [
      '@size' => $this->getSetting('count_size'),
    ]);

    if (!empty($this->getSetting('count_placeholder'))) {
      $summary[] = $this->t('Count placeholder: @placeholder', [
        '@placeholder' => $this->getSetting('count_placeholder'),
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
    $default_label = $items[$delta]->label ?? NULL;
    $default_count = $items[$delta]->count ?? NULL;

    $element['label'] = [
      '#type' => 'textfield',
      '#default_value' => $default_label,
      '#size' => $this->getSetting('label_size'),
      '#placeholder' => $this->getSetting('label_placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => ['class' => ['inline-shrink']],
    ];

    $element['count'] = [
      '#type' => 'number',
      '#default_value' => $default_count,
      '#size' => $this->getSetting('count_size'),
      '#placeholder' => $this->getSetting('count_placeholder'),
      '#attributes' => ['class' => ['inline-shrink']],
      '#min' => 0,
      '#step' => 1,
    ];

    // If cardinality is 1, ensure a proper label is output for the field.
    $cardinality = $this->fieldDefinition
      ->getFieldStorageDefinition()
      ->getCardinality();

    if ($cardinality === 1) {
      $element['label']['#title'] = $element['#title'];
      $element['count']['#title'] = '&nbsp;';
    }

    return $element;
  }

}
