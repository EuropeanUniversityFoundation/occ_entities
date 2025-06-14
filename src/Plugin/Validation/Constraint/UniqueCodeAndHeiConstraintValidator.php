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
    if ($is_new) {
      $result = $this->entityTypeManager
        ->getStorage($entity_type)
        ->loadByProperties(
          [
            $constraint->code_field => $entity_code,
            $constraint->hei_field => $entity_hei->id(),
          ]
        );
    } else {
      $result = $this->entityTypeManager
        ->getStorage($entity_type)
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition($constraint->code_field, $entity_code)
        ->condition($constraint->hei_field, $entity_hei->id())
        ->condition('id', $entity->id(), '<>')
        ->execute();
    }

    if (!empty($result)) {
      $this->context->addViolation($constraint->message, ['%entity_label' => $constraint->entity_label, '%code' => $entity_code, '%hei' => $entity_hei->label()]);
    }
  }
}
