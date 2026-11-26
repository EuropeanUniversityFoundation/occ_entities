<?php

declare(strict_types=1);

namespace Drupal\Tests\occ_entities\Kernel;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\user\UserInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests validation and persistence of learning opportunities.
 */
#[Group('occ_entities')]
#[RunTestsInSeparateProcesses]
final class LearningOpportunityTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system', 'user', 'field', 'datetime', 'link', 'options', 'text',
    'serialization', 'rest', 'telephone', 'views', 'ewp_core',
    'ewp_flexible_address', 'ewp_phone_number', 'ewp_contact',
    'ewp_institutions', 'ewp_ounits', 'entity_reference_validators',
    'isced_field', 'elm_vocabulary_field', 'occ_entities',
  ];

  /**
   * The default institution for test specifications.
   */
  private ContentEntityInterface $institution;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    foreach (['user', 'hei', 'ounit', 'occ_los', 'occ_loi'] as $type) {
      $this->installEntitySchema($type);
    }
    foreach (['ewp_institutions', 'ewp_ounits', 'occ_entities'] as $module) {
      $this->installConfig([$module]);
    }

    // Reference validation should use an account with access to the fixtures.
    $admin = $this->createEntity('user', ['uid' => 1, 'name' => 'admin', 'status' => 1]);
    $this->assertInstanceOf(UserInterface::class, $admin);
    $admin->enforceIsNew();
    $admin->save();
    $this->container->get('current_user')->setAccount($admin);
    $this->institution = $this->saveInstitution('home.example');
  }

  /**
   * Tests required fields and references to missing entities.
   */
  public function testMissingValuesAndReferences(): void {
    $course = $this->specification('course', 'main-course');
    $course->set('hei', NULL);
    $this->assertViolationPaths($course, ['hei']);
    $course->set('hei', $this->institution->id());

    $course->set('ounit', 9999);
    $course->set('course__prerequisite_course', 9999);
    $course->set('course__related_programme', [
      'target_id' => 9999,
      'mandatory' => FALSE,
      'year' => '1/3',
    ]);
    $this->assertViolationPaths($course, [
      'ounit.0.target_id',
      'course__prerequisite_course.0.target_id',
      'course__related_programme.0.target_id',
    ]);

    $instance = $this->instance($this->saveAndReload($this->specification('course', 'instance-course')));
    $instance->set('academic_term_id', NULL);
    $this->assertViolationPaths($instance, ['academic_term_id']);
  }

  /**
   * Tests institution-scoped references and code uniqueness.
   */
  public function testInstitutionMatchingAndCodeUniqueness(): void {
    $other = $this->saveInstitution('other.example');
    $original = $this->saveAndReload($this->specification('course', 'shared'));
    $programme = $this->saveAndReload($this->specification('programme', 'home-programme'));
    $ounit = $this->saveAndReload($this->createEntity('ounit', [
      'label' => 'Home department',
      'parent_hei' => $this->institution->id(),
    ]));
    $course = $this->specification('course', 'main-course');
    $course->set('ounit', $ounit->id());
    $course->set('course__prerequisite_course', $original->id());
    $course->set('course__related_programme', [
      'target_id' => $programme->id(),
      'mandatory' => FALSE,
      'year' => '1/3',
    ]);
    $this->assertViolationPaths($course, []);

    $course->set('hei', $other->id());
    $this->assertViolationPaths($course, [
      'ounit',
      'course__prerequisite_course.0',
      'course__related_programme.0',
    ]);

    $duplicate = $this->specification('programme', 'shared');
    $this->assertViolationPaths($duplicate, ['code']);
    $duplicate->set('hei', $other->id());
    $this->assertViolationPaths($duplicate, []);
    $this->assertViolationPaths($original, []);
  }

  /**
   * Tests programme duration, flexible years, and duplicate references.
   */
  public function testRelatedProgrammeYears(): void {
    $programme = $this->saveAndReload($this->specification('programme', 'programme'));
    $course = $this->specification('course', 'course');
    foreach ([
      '7/0' => [],
      '2/3' => [],
      '2/4' => ['course__related_programme.0.year'],
      '4/3' => ['course__related_programme.0', 'course__related_programme.0.year'],
    ] as $year => $paths) {
      $reference = ['target_id' => $programme->id(), 'mandatory' => FALSE, 'year' => $year];
      $course->set('course__related_programme', $reference);
      $this->assertViolationPaths($course, $paths);
    }
    $reference['year'] = '2/3';
    $course->set('course__related_programme', [$reference, $reference]);
    $this->assertViolationPaths($course, ['course__related_programme.1']);

    // Simulate an invalid length already in storage.
    $programme->set('programme__length', '6/0');
    $programme->save();
    $course->set('course__related_programme', $reference);
    $this->assertViolationPaths($course, ['course__related_programme.0.year']);
    $reference['year'] = '7/0';
    $course->set('course__related_programme', $reference);
    $this->assertViolationPaths($course, []);
  }

  /**
   * Tests the course reference, academic term, and date range.
   */
  public function testCourseInstanceValidation(): void {
    $course = $this->saveAndReload($this->specification('course', 'course'));
    $programme = $this->saveAndReload($this->specification('programme', 'programme'));
    $instance = $this->instance($course);
    $this->assertViolationPaths($instance, []);
    $instance->set('course', $programme->id());
    $this->assertViolationPaths($instance, ['course.0.target_id']);
    $instance->set('course', $course->id());

    $invalid_terms = [
      '2025-2026-1/2',
      "2025/2026-1/2\n",
      '2025/2027-1/2',
      '2025/2026-0/2',
      '2025/2026-0/0',
      '2025/2026-2/1',
    ];
    foreach ($invalid_terms as $term) {
      $instance->set('academic_term_id', $term);
      $this->assertViolationPaths($instance, ['academic_term_id']);
    }
    $instance->set('academic_term_id', '2025/2026-1/2');
    foreach (['2025-09-01' => ['end_date'], '2025-09-02' => [], '2025-09-03' => []] as $end => $paths) {
      $instance->set('end_date', $end);
      $this->assertViolationPaths($instance, $paths);
    }
  }

  /**
   * Tests validation of configurable fields on all three bundles.
   */
  public function testAdditionalFieldValidation(): void {
    $programme = $this->specification('programme', 'programme');
    // Level 9 violates both the base field range and the programme range.
    foreach ([4 => 1, 5 => 0, 8 => 0, 9 => 2] as $level => $count) {
      $programme->set('programme__eqf_level_provided', $level);
      $this->assertViolationPaths($programme, array_fill(0, $count, 'programme__eqf_level_provided.0.value'));
    }
    $programme->set('programme__eqf_level_provided', 5);
    $programme->set('programme__length', '6/0');
    $this->assertViolationPaths($programme, ['programme__length.0']);

    $course = $this->specification('course', 'course');
    $course->set('course__local_classification', ['type' => 'department']);
    $violations = $course->validate();
    $this->assertContains('course__local_classification.0', array_map(static fn ($violation) => $violation->getPropertyPath(), iterator_to_array($violations)));
    $course->set('course__local_classification', ['type' => 'department', 'value' => 'science']);
    foreach (['0/0/0', '0/2', '3/2'] as $term) {
      $course->set('course__academic_term', $term);
      $this->assertViolationPaths($course, ['course__academic_term.0']);
    }
    $course->set('course__academic_term', '1/2');
    $this->assertViolationPaths($course, []);

    $instance = $this->instance($this->saveAndReload($course));
    $instance->set('course__result_dist_category', ['count' => 0]);
    $this->assertViolationPaths($instance, ['course__result_dist_category.0.label']);
    $instance->set('course__result_dist_category', ['label' => 'A', 'count' => 0]);
    $this->assertViolationPaths($instance, []);
    $instance->set('course__result_dist_category', ['label' => 'A', 'count' => -1]);
    $this->assertViolationPaths($instance, ['course__result_dist_category.0.count']);
  }

  /**
   * Tests generated labels and course defaults after reloading.
   */
  public function testPersistenceAndInstanceDefaults(): void {
    $programme = $this->saveAndReload($this->specification('programme', 'Programme title'));
    $this->assertSame('Programme title', $programme->label());
    $this->assertSame('5/2', $programme->get('programme__length')->value);

    $course = $this->saveAndReload($this->specification('course', 'Course title'));
    $this->assertSame('Course title', $course->label());

    $instance = $this->instance($course);
    $instance->set('ects', NULL);
    $instance->set('language_of_instruction', ['lang' => 'de']);
    $instance = $this->saveAndReload($instance);
    $this->assertSame('Course title | 2025/2026-1/2', $instance->label());
    $this->assertSame(6.0, (float) $instance->get('ects')->value);
    $this->assertSame('de', $instance->get('language_of_instruction')->lang);
    $this->assertSame($course->id(), $instance->get('course')->target_id);
  }

  /**
   * Creates and saves a referenceable institution.
   */
  private function saveInstitution(string $domain): ContentEntityInterface {
    return $this->saveAndReload($this->createEntity('hei', [
      'hei_id' => $domain,
      'label' => $domain,
      'status' => 1,
    ]));
  }

  /**
   * Builds an unsaved specification with all required values.
   */
  private function specification(string $bundle, string $code): ContentEntityInterface {
    $values = [
      'bundle' => $bundle,
      'code' => $code,
      'title' => ['string' => $code],
      'description' => ['multiline' => 'Description'],
      'learning_outcomes' => ['multiline' => 'Learning outcomes'],
      'language_of_instruction' => ['lang' => 'en'],
      'hei' => $this->institution->id(),
      $bundle . '__ects' => '6.00',
    ];
    if ($bundle === 'programme') {
      $values += [
        'programme__length' => '5/2',
        'programme__isced_code' => '0611',
        'programme__eqf_level_provided' => 6,
      ];
    }
    return $this->createEntity('occ_los', $values);
  }

  /**
   * Builds an unsaved instance referencing an existing course.
   */
  private function instance(ContentEntityInterface $course): ContentEntityInterface {
    return $this->createEntity('occ_loi', [
      'bundle' => 'course_instance',
      'course' => $course->id(),
      'start_date' => '2025-09-02',
      'end_date' => '2026-01-31',
      'academic_term_id' => '2025/2026-1/2',
      'ects' => '6.00',
      'language_of_instruction' => ['lang' => 'en'],
    ]);
  }

  /**
   * Creates an unsaved content entity.
   */
  private function createEntity(string $type, array $values): ContentEntityInterface {
    $entity = $this->container->get('entity_type.manager')->getStorage($type)->create($values);
    $this->assertInstanceOf(ContentEntityInterface::class, $entity);
    return $entity;
  }

  /**
   * Saves an entity and reloads it from storage.
   */
  private function saveAndReload(ContentEntityInterface $entity): ContentEntityInterface {
    $entity->save();
    $reloaded = $this->container->get('entity_type.manager')
      ->getStorage($entity->getEntityTypeId())
      ->loadUnchanged($entity->id());
    $this->assertInstanceOf(ContentEntityInterface::class, $reloaded);
    return $reloaded;
  }

  /**
   * Asserts the complete set of validation paths, including duplicates.
   */
  private function assertViolationPaths(ContentEntityInterface $entity, array $expected): void {
    $paths = [];
    $messages = [];
    foreach ($entity->validate() as $violation) {
      $paths[] = $violation->getPropertyPath();
      $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
    }
    $this->assertEqualsCanonicalizing($expected, $paths, implode("\n", $messages));
  }

}
