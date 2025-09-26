<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\occ_entities\Plugin\Field\FieldType\RelatedProgrammeItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the YearMatchesProgramme constraint.
 */
class YearMatchesProgrammeConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $field_value = ($value instanceof RelatedProgrammeItem)
      ? $value->getValue()
      : $value;

    /** @var \Drupal\occ_entities\Entity\LearningOpportunitySpecificationInterface|null $programme */
    $programme = $this->findProgramme(($field_value));

    if (!is_null($programme)) {
      $programme_length = $programme->get('programme__length')->value;
      $programme_length_values = explode('/', $programme_length);
      $total_terms = (int) $programme_length_values[0];
      $terms_per_year = (int) $programme_length_values[1];
      $programme_length_int = (int) ceil($total_terms / $terms_per_year);

      $programme_label = $programme->label();

      $year_value = $field_value['year'];

      if (strpos($year_value, '/') !== FALSE) {
        $year_values = explode('/', $year_value);
        $year_item = (int) $year_values[0];
        $year_total = (int) $year_values[1];

        $year_total_match = ($year_total === $programme_length_int);
        $year_item_within = ($year_item <= $programme_length_int);

        if (!$year_total_match || !$year_item_within) {
          /** @var YearMatchesProgrammeConstraint $constraint */
          $this->context->addViolation($constraint->message, [
            '%programme_length' => $programme_length_int,
            '%programme_label' => $programme_label,
          ]);
        }
      }
    }
  }

  /**
   * Finds a Programme from a field value.
   */
  private function findProgramme($value): ?EntityInterface {
    if (is_null($value)) {
      return NULL;
    }

    $storage = $this->entityTypeManager->getStorage('occ_los');

    if (array_key_exists('target_id', $value)) {
      return $storage->load($value['target_id']);
    }

    if (array_key_exists('target_uuid', $value)) {
      $programme = $storage->loadByProperties([
        'uuid' => $value['target_uuid'],
      ]);
      return array_values($programme)[0];
    }

    return NULL;
  }

}
