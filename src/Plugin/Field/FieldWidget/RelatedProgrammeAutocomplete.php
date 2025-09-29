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
    $widget['mandatory'] = [
      '#title' => $this->t('Mandatory'),
      '#type' => 'checkbox',
      '#key_column' => 'mandatory',
      '#default_value' => $mandatory_value ?? TRUE,
      '#weight' => 3,
    ];

    return $widget;
  }

}
