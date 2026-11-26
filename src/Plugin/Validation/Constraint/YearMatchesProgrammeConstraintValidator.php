<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\occ_entities\Plugin\Field\FieldType\RelatedProgrammeItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the YearMatchesProgramme constraint.
 *
 * @phpstan-consistent-constructor
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
    $field_value = $value instanceof RelatedProgrammeItem ? $value->getValue() : $value;
    if (!is_array($field_value)) {
      return;
    }

    $programme = $value instanceof RelatedProgrammeItem
      ? $value->entity
      : $this->findProgramme($field_value);
    if (!$programme instanceof ContentEntityInterface || $programme->bundle() !== 'programme' || !$programme->hasField('programme__length')) {
      return;
    }

    $year = $field_value['year'] ?? '';
    if (!preg_match('~\A(\d+)/(\d+)\z~', $year, $year_parts) || (int) $year_parts[2] === 0) {
      return;
    }

    /** @var YearMatchesProgrammeConstraint $constraint */
    $length = $programme->get('programme__length')->value ?? '';
    if (!preg_match('~\A(\d+)/(\d+)\z~', $length, $length_parts)
        || (int) $length_parts[1] <= 0 || (int) $length_parts[2] <= 0) {
      $this->context->buildViolation($constraint->invalidLengthMessage)
        ->setParameter('%programme_label', $programme->label())
        ->atPath('year')
        ->addViolation();
      return;
    }

    $programme_years = (int) ceil((int) $length_parts[1] / (int) $length_parts[2]);
    if ((int) $year_parts[2] !== $programme_years || (int) $year_parts[1] > $programme_years) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('%programme_length', (string) $programme_years)
        ->setParameter('%programme_label', $programme->label())
        ->atPath('year')
        ->addViolation();
    }
  }

  /**
   * Finds a Programme from a field value.
   */
  private function findProgramme(array $value): ?EntityInterface {
    $storage = $this->entityTypeManager->getStorage('occ_los');

    if (!empty($value['target_id'])) {
      return $storage->load($value['target_id']);
    }

    if (!empty($value['target_uuid'])) {
      $programme = $storage->loadByProperties([
        'uuid' => $value['target_uuid'],
      ]);
      return reset($programme) ?: NULL;
    }

    return NULL;
  }

}
