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
 *   id = "code_and_hei_unique",
 *   label = @Translation("The combination of the code and the Institution must be unique.", context = "Validation"),
 * )
 */
class UniqueCodeAndHeiConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface
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
    // @phpstan-ignore property.notFound
    $entity_code = $entity->get($constraint->code_field)->value;
    // @phpstan-ignore property.notFound
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
