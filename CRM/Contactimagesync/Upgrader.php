<?php
// phpcs:disable
use CRM_Contactimagesync_ExtensionUtil as E;
// phpcs:enable

/**
 * Collection of upgrade steps.
 */
class CRM_Contactimagesync_Upgrader extends CRM_Extension_Upgrader_Base {

  public function install(): void {

    if (!method_exists('CRM_Civirules_Utils_Upgrader', 'insertActionsFromJson'))
      throw new Exception('Method CRM_Civirules_Utils_Upgrader::insertActionsFromJson() not found. Is the CiviRules extension enabled?');
    CRM_Civirules_Utils_Upgrader::insertActionsFromJson($this->extensionDir . DIRECTORY_SEPARATOR . 'civirules_actions.json');
  }
}
