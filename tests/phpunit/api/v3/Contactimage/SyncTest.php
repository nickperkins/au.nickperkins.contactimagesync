<?php

use PHPUnit\Framework\TestCase;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Drupal\user\Entity\User;
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
  /** @var PHPUnit_Framework_MockObject_MockObject $user */
  private $user;

  private function createDrupalUser($contactId, $mail) {

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

    // Mock the load method of \Drupal\user\Entity\User
    $user = $this->getMockBuilder(UserWrapper::class)
      ->disableOriginalConstructor()
      ->setMethods(['load'])
      ->getMock();
    $this->user = $user;

    $user_data = (object)[
      'first_name' => (object)[
        'value' => 'test'
      ],
      'field_middle_name' => (object)[
        'value' => 'test'
      ],
      'last_name' => (object)[
        'value' => 'test'
      ],
      'mail' => (object)[
        'value' => 'test@test.com'
      ],
      'field_telephone_number' => (object)[
        'value' => '9876543210'
      ],
      'field_mobile_number' => (object)[
        'value' => '9876543210'
      ],
      'user_picture' => (object)[
        'value' => null
      ],
    ];

    $this->user
      ->method('load')
      ->willReturn($user_data);

    $container = new ContainerBuilder();
    $container->set('user', $user);
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

    // create a drupal user
    civicrm_api3('UFMatch', 'create', array(
      'contact_id' => $this->contactId,
      'uf_id' => 1,
      'uf_name' => 'test',
    ));
  }

  /**
   * The tearDown() method is executed after the test was executed (optional)
   * This can be used for cleanup.
   */
  public function tearDown(): void {
    parent::tearDown();
    // remove the drupal user and link to the contact
    civicrm_api3('UFMatch', 'delete', array(
      'contact_id' => $this->contactId,
    ));
    // remove the contact
    civicrm_api3('Contact', 'delete', array(
      'id' => $this->contactId,
    ));
  }

  /**
   * Simple example test case.
   *
   * Note how the function name begins with the word "test".
   */
  public function testApiExample() {
    $result = civicrm_api3('ContactImage', 'sync', array('contact_id' => $this->contactId));
    $this->assertEquals([], $result['values']);
  }
}
