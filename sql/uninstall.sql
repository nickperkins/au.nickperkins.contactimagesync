DELETE FROM
  civirule_action
WHERE
  name = 'contactimage_sync'
  AND label = 'Sync Contact Image to CMS'
  AND class_name = 'CRM_Contactimagesync_CivirulesAction_Sync';
