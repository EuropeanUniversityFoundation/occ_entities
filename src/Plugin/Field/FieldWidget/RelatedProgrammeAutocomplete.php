<?php

namespace Drupal\occ_entities\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'related_programme_autocomplete' widget.
 */
#[FieldWidget(
  id: 'related_programme_autocomplete',
  label: new TranslatableMarkup('Autocomplete with mandatory flag and year'),
  field_types: ['related_programme'],
)]
class RelatedProgrammeAutocomplete extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $widget = parent::formElement($items, $delta, $element, $form, $form_state);
    unset($widget['target_id']['#title_display']);
    $widget['target_id']['#title'] = $this->t('Programme');
    $widget['target_id']['#description'] = $this->t('Title of the programme the course is part of.');

    $widget['year'] = [
      '#title' => $this->t('Year'),
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->year ?? '',
      '#placeholder' => 'e.g. 1/2',
      '#description' => $this->t('Year of course delivery / Total years in programme'),
      '#weight' => 2,
    ];

    $mandatory_value = $items[$delta]->mandatory;
    if (is_null($mandatory_value)) {
      $mandatory_value = '_none';
    }

    $widget['mandatory'] = [
      '#title' => $this->t('Mandatory'),
      '#type' => 'select',
      '#options' => [
        '_none' => $this->t('- Not specified -'),
        '0' => $this->t('No'),
        '1' => $this->t('Yes'),
      ],
      '#key_column' => 'mandatory',
      '#default_value' => $mandatory_value,
      '#weight' => 3,
    ];

    $widget['mandatory']['#element_validate'][] = [static::class, 'validateMandatoryElement'];

    return $widget;
  }

  /**
   * Validates the Mandatory property element.
   */
  public static function validateMandatoryElement(array $element, FormStateInterface $form_state) {
    if ($element['#required'] && $element['#value'] == '_none') {
      if (isset($element['#required_error'])) {
        $form_state->setError($element, $element['#required_error']);
      }
      else {
        $form_state->setError($element, new TranslatableMarkup('@name field is required.', ['@name' => $element['#title']]));
      }
    }

    if ($element['#value'] == '_none') {
      $form_state->setValueForElement($element, NULL);
    }
  }

}
