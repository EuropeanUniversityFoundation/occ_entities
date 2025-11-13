<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the UniqueCodeAndHei constraint.
 */
class UniqueCodeAndHeiConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

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
  public function validate($entity, Constraint $constraint) {
    $entity_code = $entity->get($constraint->code_field)->value;
    $entity_hei = $entity->get($constraint->hei_field)->referencedEntities();
    $entity_hei = reset($entity_hei);
    $entity_type = $entity->getEntityTypeId();
    $is_new = $entity->isNew();

    /** @var UniqueCodeAndHeiConstraint $constraint */
    $query = $this->entityTypeManager
      ->getStorage($entity_type)
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition($constraint->hei_field, $entity_hei->id())
      ->condition($constraint->code_field, $entity_code);

    if (!$is_new) {
      $query->condition('id', $entity->id(), '<>');
    }

    $result = $query->execute();

    if (!empty($result)) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('%entity_label', $entity->label())
        ->setParameter('%code', $entity_code)
        ->setParameter('%hei', $entity_hei->label())
        ->atPath($constraint->code_field)
        ->addViolation();
    }
  }

}
