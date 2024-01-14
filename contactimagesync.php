<?php

require_once 'contactimagesync.civix.php';
// phpcs:disable
use CRM_Contactimagesync_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function contactimagesync_civicrm_config(&$config): void {
  _contactimagesync_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function contactimagesync_civicrm_install(): void {
  _contactimagesync_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function contactimagesync_civicrm_enable(): void {
  _contactimagesync_civix_civicrm_enable();
}


