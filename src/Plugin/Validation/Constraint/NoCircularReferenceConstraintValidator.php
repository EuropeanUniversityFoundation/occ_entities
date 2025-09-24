<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Validates the NoCircularReference constraint.
 */
class NoCircularReferenceConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

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

    // $entity_type = $entity->getEntityTypeId();
    $entity_id = $entity->id();
    /** @var NoCircularReferenceConstraint $constraint */
    $parent_entity = $entity->get($constraint->parent_field)->referencedEntities();

    if (!empty($parent_entity)) {
      $parent_entity = reset($parent_entity);
      $parent_referenced_entity = $parent_entity->get($constraint->parent_field)->referencedEntities();

      if (!empty($parent_referenced_entity)) {
        $parent_referenced_entity = reset($parent_referenced_entity);

        if ($parent_referenced_entity->id() == $entity_id) {
          $this->context->addViolation($constraint->message, [
            '%entity_label' => $constraint->entity_label,
            '%code_1' => $entity->get($constraint->code_field)->value,
            '%code_2' => $parent_entity->get($constraint->code_field)->value,
          ]);
        }
      }
    }
  }

}
