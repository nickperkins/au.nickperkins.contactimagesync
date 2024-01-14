<?php

use CRM_Contactimagesync_ExtensionUtil as E;
use Drupal\Core\File\FileSystemInterface;

/**
 * ContactImage.Sync API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_contact_image_Sync_spec(&$spec) {
  $spec['contact_id']['api.required'] = 1;
}

/**
 * ContactImage.Sync API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_contact_image_Sync($params) {
  $returnValues = CRM_Contactimagesync_Sync::run($params['contact_id']);
  return civicrm_api3_create_success($returnValues, $params, 'Contact', 'Synccontactimage');
}
