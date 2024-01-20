<?php
// phpcs:disable
use CRM_Contactimagesync_ExtensionUtil as E;
// phpcs:enable

/**
 * Civirules action to sync a contact's image to the Drupal user's image
 */

class CRM_Contactimagesync_CivirulesAction_Sync extends CRM_Civirules_Action {

  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();

    CRM_Contactimagesync_Sync::run($contactId);
  }
}
