<?php

namespace Drupal\occ_entities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Checks if combnation of code and HEI reference is unique.
 *
 * @Constraint(
 *   id = "no_circular_reference",
 *   label = @Translation("Two entities reference each other as parents.", context = "Validation"),
 * )
 */
class NoCircularReferenceConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface
{

  protected EntityTypeManagerInterface $entityTypeManager;

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

  public function validate($entity, Constraint $constraint)
  {

    $entity_type = $entity->getEntityTypeId();
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
            '%code_2' => $parent_entity->get($constraint->code_field)->value
          ]);
        }
      }
    }
  }
}
