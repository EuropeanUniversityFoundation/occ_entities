<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\occ_entities\Entity\LearningOpportunitySpecificationType;

/**
 * Form handler for learning opportunity specification type forms.
 */
final class LearningOpportunitySpecificationTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    if ($this->operation === 'edit') {
      $form['#title'] = $this->t('Edit %label learning opportunity specification type', ['%label' => $this->entity->label()]);
    }

    $form['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->label(),
      '#description' => $this->t('The human-readable name of this learning opportunity specification type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => [LearningOpportunitySpecificationType::class, 'load'],
        'source' => ['label'],
      ],
      '#description' => $this->t('A unique machine-readable name for this learning opportunity specification type. It must only contain lowercase letters, numbers, and underscores.'),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state): array {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save learning opportunity specification type');
    $actions['delete']['#value'] = $this->t('Delete learning opportunity specification type');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var int<1, 2> $result */
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        SAVED_NEW => $this->t('The learning opportunity specification type %label has been added.', $message_args),
        SAVED_UPDATED => $this->t('The learning opportunity specification type %label has been updated.', $message_args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $result;
  }

}
