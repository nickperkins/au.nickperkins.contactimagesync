<?php

use PHPUnit\Framework\TestCase;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepository;

/**
 * ContactImage.Sync API Test Case
 * This is a generic test class implemented with PHPUnit.
 * @group headless
 */
class api_v3_ContactImage_SyncTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  /**
   * @var int
   */
  protected $contactId;

  private function createDrupalUser($contactId, $mail) {
    /** @var \Drupal\user\Entity\User $account */
    $account = \Drupal::entityTypeManager()->getStorage('user')->create();
    $account->setUsername($mail)->setEmail($mail);

    $account->setPassword(FALSE);
    $account->enforceIsNew();
    $account->activate();

    $this->callAPISuccess('UFMatch', 'create', array(
      'contact_id' => $this->contactId,
      'uf_id' => 1,
      'uf_name' => 'testcontact',
    ));
  }

  private function deleteDrupalUser($contactId) {
    // get the user id from the UF Match
    $ufMatch = $this->callAPISuccess('UFMatch', 'get', array(
      'contact_id' => $this->contactId,
    ));
    $userId = $ufMatch['values'][$this->contactId]['uf_id'];
    // delete the user from Drupal
    $account = \Drupal::entityTypeManager()->getStorage('user')->load($userId);
    $account->delete();
    // delete the UF Match
    $this->callAPISuccess('UFMatch', 'delete', array(
      'contact_id' => $this->contactId,
    ));
  }

  /**
   * Set up for headless tests.
   *
   * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
   *
   * See: https://docs.civicrm.org/dev/en/latest/testing/phpunit/#civitest
   */
  public function setUpHeadless(): CiviEnvBuilder {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * The setup() method is executed before the test is executed (optional).
   */
  public function setUp(): void {
    parent::setUp();

    $container = new ContainerBuilder();

    $entityTypeManager = $this
      ->getMockBuilder(EntityTypeManagerInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $container->set(EntityTypeManagerInterface::class, $entityTypeManager);

    $entityTypeRepository = $this
      ->getMockBuilder(EntityTypeRepository::class)
      ->disableOriginalConstructor()
      ->getMock();
    $container->set(EntityTypeRepository::class, $entityTypeRepository);
    $entityStorageInterface = $this
      ->getMockBuilder(EntityStorageInterface::class)
      ->disableOriginalConstructor()
      ->setMethods(get_class_methods(EntityStorageInterface::class))
      ->getMock();

    $container->set(EntityStorageInterface::class, $entityStorageInterface);
    $userDrupal = $this
      ->getMockBuilder(\Drupal\user\Entity\User::class)
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('user', $userDrupal);
    \Drupal::setContainer($container);

    // Set the mock Drupal container
    \Drupal::setContainer($container);

    // generate a fake email address
    $email = 'test' . time() . '@example.com';
    // create a contact
    $this->contactId = civicrm_api3('Contact', 'create', array(
      'contact_type' => 'Individual',
      'first_name' => 'Test',
      'last_name' => 'Contact',
      'email' => $email,
      'image_URL' => 'https://civicrm.org/sites/civicrm.org/files/civicrm/persist/contribute/images/2018-11-29_15-00-00.png',
    ))['id'];
    // create a user in drupal and link to the contact
    $this->createDrupalUser($this->contactId, $email);
  }

  /**
   * The tearDown() method is executed after the test was executed (optional)
   * This can be used for cleanup.
   */
  public function tearDown(): void {
    parent::tearDown();
    // remove the drupal user and link to the contact
    $this->deleteDrupalUser($this->contactId);
    // remove the contact
    $this->callAPISuccess('Contact', 'delete', array(
      'id' => $this->contactId,
    ));
  }

  /**
   * Simple example test case.
   *
   * Note how the function name begins with the word "test".
   */
  public function testApiExample() {
    $result = $this->callAPISuccess('ContactImage', 'sync', array('contact_id' => $this->contactId));
    $this->assertEquals([], $result['values']);
  }
}
