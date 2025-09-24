<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\occ_entities\Entity\LearningOpportunitySpecification;

/**
 * Validates the YearMatchesProgramme constraint.
 */
class YearMatchesProgrammeConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  public const PROGRAMME_SUBFIELD_NAME = 'target_id';
  public const YEAR_SUBFIELD_NAME = 'year';

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
    foreach ($value as $item) {
      $reference_value = $item->{self::PROGRAMME_SUBFIELD_NAME};
      if ($reference_value instanceof LearningOpportunitySpecification) {
        $referenced_programme_id = $reference_value->id();
      }
      else {
        $referenced_programme_id = $reference_value;
      }
      $referenced_programme = $this->findProgrammeById($referenced_programme_id);
      $referenced_programme_length = $referenced_programme->get('programme__length')->value;
      $referenced_programme_label = $referenced_programme->label();
      $year_value = $item->{self::YEAR_SUBFIELD_NAME};

      if (strpos($year_value, '/') !== FALSE) {
        $year_values = explode('/', $year_value);
        $programme_length_value = $year_values[1];
        if ((int) $programme_length_value !== (int) $referenced_programme_length) {
          /** @var YearMatchesProgrammeConstraint $constraint */
          $this->context->addViolation($constraint->message, [
            '%programme_length' => $referenced_programme_length,
            '%programme_label' => $referenced_programme_label,
          ]);
        }
      }
    }
  }

  /**
   * Finds a Programme by ID.
   */
  private function findProgrammeById($id): ?LearningOpportunitySpecification {
    /** @var \Drupal\occ_entities\Entity\LearningOpportunitySpecification|null $programme */
    $programme = $this->entityTypeManager
      ->getStorage('occ_los')
      ->load($id);

    return $programme;
  }

}
