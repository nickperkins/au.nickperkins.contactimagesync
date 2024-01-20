<?php

use CRM_Contactimagesync_ExtensionUtil as E;
use Drupal\Core\File\FileSystemInterface;

/**
 * Class CRM_Contactimagesync_Sync
 *
 * This class is responsible for syncing contact images in the CRM system.
 * It provides methods to retrieve and update contact images from external sources.
 */

class CRM_Contactimagesync_Sync {

  /**
   * Method to sync the contact image to the CRM system
   *
   * @param int $contact_id
   * @return array
   * @access public
   *
   */
  public static function run($contact_id) {
    $result = civicrm_api3('Contact', 'get', [
      'sequential' => 1,
      'return' => ["image_URL"],
      'id' => $contact_id,
    ]);

    $image_url = $result['values'][0]['image_URL'];

    if (!$image_url) {
      return [];
    }

    $result = civicrm_api3('UFMatch', 'get', [
      'sequential' => 1,
      'return' => ["uf_id"],
      'contact_id' => $contact_id,
    ]);

    $cms_user_id = $result['values'][0]['uf_id'];

    if (!$cms_user_id) {
      // Not all users have a drupal accout so exit quietly
      return [];
    }

    // get file extension from image url
    $image_extension = pathinfo($image_url, PATHINFO_EXTENSION);

    // Copy the image from the URL and set it as the Drupal user's picture
    $image_data = file_get_contents($image_url);
    if ($image_data !== false) {
      // Save the image to the Drupal files directory
      $file = \Drupal::service('file.repository')->writeData($image_data, 'public://' . $cms_user_id . '.' . $image_extension, FileSystemInterface::EXISTS_REPLACE);
      // Set the Drupal user's picture to the file we just saved
      if ($file !== false) {
        $drupal_user = \Drupal\user\Entity\User::load($cms_user_id);
        $drupal_user->set('user_picture', $file->id());
        $drupal_user->save();
      }
    }

    return [];
  }
}
