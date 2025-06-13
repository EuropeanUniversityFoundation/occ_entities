<?php

namespace Drupal\occ_entities\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * @FieldWidget(
 *   id = "related_programme_autocomplete",
 *   label = @Translation("Autocomplete with mandatory flag and year"),
 *   description = @Translation("An autocomplete text field with an associated mandatory flag and year value."),
 *   field_types = {
 *     "related_programme"
 *   }
 * )
 */
class RelatedProgrammeAutocomplete extends EntityReferenceAutocompleteWidget
{

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {

    $widget = parent::formElement($items, $delta, $element, $form, $form_state);
    unset($widget['target_id']['#title_display']);
    $widget['target_id']['#title'] = $this->t('Programme');
    $widget['target_id']['#description'] = $this->t('Title of the programme the course is part of.');

    $widget['year'] = array(
      '#title' => $this->t('Year'),
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->year ?? '',
      '#placeholder' => 'e.g. 1/2',
      '#description' => $this->t('Year of course delivery / Total years in programme'),
      '#weight' => 2,
    );

    // @phpstan-ignore property.notFound
    $mandatory_value = $items[$delta]->mandatory;
    if (is_null($mandatory_value)) {
      $mandatory_value = '_none';
    }

    $widget['mandatory'] = array(
      '#title' => $this->t('Mandatory'),
      '#type' => 'select',
      '#options' => [
        '_none' => '- Not specified -',
        '0' => 'No',
        '1' => 'Yes'
      ],
      '#key_column' => 'mandatory',
      '#default_value' => $mandatory_value,
      '#weight' => 3,
    );

    $widget['mandatory']['#element_validate'][] = [static::class, 'validateMandatoryElement'];

    return $widget;
  }

  public static function validateMandatoryElement(array $element, FormStateInterface $form_state)
  {
    if ($element['#required'] && $element['#value'] == '_none') {
      if (isset($element['#required_error'])) {
        $form_state->setError($element, $element['#required_error']);
      } else {
        $form_state->setError($element, new TranslatableMarkup('@name field is required.', ['@name' => $element['#title']]));
      }
    }

    if ($element['#value'] == '_none') {
      $form_state->setValueForElement($element, NULL);
    }
  }
}
