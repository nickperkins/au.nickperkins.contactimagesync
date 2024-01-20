<?php
// phpcs:disable
use CRM_Contactimagesync_ExtensionUtil as E;
// phpcs:enable

/**
 * Collection of upgrade steps.
 */
class CRM_Contactimagesync_Upgrader extends CRM_Extension_Upgrader_Base {

  public function install(): void {
    $this->executeSqlFile('sql/install.sql');
  }

  public function uninstall(): void {
    $this->executeSqlFile('sql/uninstall.sql');
  }

  public function enable(): void {
    $this->executeSqlFile('sql/enable.sql');
  }

  public function disable(): void {
    $this->executeSqlFile('sql/disable.sql');
  }
}
