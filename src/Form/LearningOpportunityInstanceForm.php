<?php

declare(strict_types=1);

namespace Drupal\occ_entities\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\occ_entities\Entity\LearningOpportunityInstance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the learning opportunity instance entity edit forms.
 */
final class LearningOpportunityInstanceForm extends ContentEntityForm {

  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    if (!$this->entity->isNew()) {
      return $form;
    }

    $step = $form_state->get('step') ?? 1;

    if ($step === 1) {
      foreach (array_keys(LearningOpportunityInstance::getCourseDefaultFieldCopyMapping()) as $field_name) {
        if (isset($form[$field_name])) {
          $form[$field_name]['#access'] = FALSE;
        }
      }

      $form['actions']['submit']['#value'] = $this->t('Continue');
      $form['actions']['submit']['#submit'] = ['::submitStep1'];
      unset($form['actions']['delete']);
    }

    return $form;
  }

  /**
   * Step 1 submit: Prefilling data from course after picking reference.
   */
  public function submitStep1(array &$form, FormStateInterface $form_state): void {
    $course_id = $form_state->getValue('course')[0]['target_id'] ?? NULL;

    if ($course_id) {
      /** @var \Drupal\occ_entities\Entity\LearningOpportunitySpecification $course */
      $course = $this->entityTypeManager->getStorage('occ_los')->load($course_id);

      if ($course) {
        foreach (LearningOpportunityInstance::getCourseDefaultFieldCopyMapping() as $loi_field => $course_field) {
          $value = $course->get($course_field)->getValue();
          // Set course for course instance to use in step 2.
          $this->entity->set($loi_field, $value);
        }
      }
    }

    $form_state->set('step', 2);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    if ($this->entity->isNew()) {
      $course_defaults = $form_state->get('course_defaults') ?? [];
      $user_modified = [];

      foreach ($course_defaults as $field_name => $default_value) {
        $submitted_value = $form_state->getValue($field_name);
        if ($submitted_value !== $default_value) {
          $user_modified[] = $field_name;
        }
      }

      $this->entity->_from_form = TRUE;
      $this->entity->_user_modified_fields = $user_modified;
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->toLink()->toString()];
    $logger_args = [
      '%label' => $this->entity->label(),
      'link' => $this->entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New learning opportunity instance %label has been created.', $message_args));
        $this->logger('occ_entities')->notice('New learning opportunity instance %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The learning opportunity instance %label has been updated.', $message_args));
        $this->logger('occ_entities')->notice('The learning opportunity instance %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl());

    return $result;
  }

}
