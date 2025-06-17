<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\occ_entities\Entity\LearningOpportunitySpecification;

class YearMatchesProgrammeConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface
{

  public const PROGRAMME_SUBFIELD_NAME = 'target_id';
  public const YEAR_SUBFIELD_NAME = 'year';

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager)
  {
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container)
  {
    // @phpstan-ignore new.static
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  public function validate($value, Constraint $constraint)
  {

    foreach ($value as $item) {
      $referenced_programme_id = $item->{self::PROGRAMME_SUBFIELD_NAME};
      $referenced_programme = $this->findProgrammeById($referenced_programme_id);
      // @phpstan-ignore property.notFound
      $referenced_programme_length = $referenced_programme->get('programme__length')->value;
      $referenced_programme_label = $referenced_programme->label();
      $year_value = $item->{self::YEAR_SUBFIELD_NAME};

      if (strpos($year_value, '/') !== false) {
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

  private function findProgrammeById($id): LearningOpportunitySpecification
  {
    $programme = \Drupal::entityTypeManager()
      ->getStorage('occ_los')
      ->load($id);

    // @phpstan-ignore return.type
    return $programme;
  }
}
