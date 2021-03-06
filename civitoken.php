<?php

require_once 'civitoken.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civitoken_civicrm_config(&$config) {
  _civitoken_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civitoken_civicrm_xmlMenu(&$files) {
  _civitoken_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civitoken_civicrm_install() {
  _civitoken_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civitoken_civicrm_uninstall() {
  _civitoken_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civitoken_civicrm_enable() {
  _civitoken_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civitoken_civicrm_disable() {
  _civitoken_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civitoken_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civitoken_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civitoken_civicrm_managed(&$entities) {
  _civitoken_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civitoken_civicrm_caseTypes(&$caseTypes) {
  _civitoken_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civitoken_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civitoken_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
* implementation of CiviCRM hook
*/
function civitoken_civicrm_tokens(&$tokens) {
  $tokenFunctions = civitoken_initialize();
  $civitokens = array();
  foreach ($tokenFunctions as $token) {
    $fn = $token . '_civitoken_declare';
    $tokens[$token] = array_merge($civitokens, $fn($token));
  }
  $tokens['civitokens']= $civitokens;
}

/**
 * implementation of CiviCRM hook
 *
 * @param array $values
 * @param $contactIDs
 * @param null $job
 * @param array $tokens
 * @param null $context
 */
function civitoken_civicrm_tokenValues(&$values, $contactIDs, $job = null, $tokens = array(), $context = null) {
  $tokenFunctions = civitoken_initialize();
  // @todo figure out full conditions for returning here.
  if (empty($tokens) || array_keys($tokens) == array('contact')) {
    return;
  }

  foreach ($tokenFunctions as $token) {
    if (in_array($token, array_keys($tokens))) {
      $fn = $token . '_civitoken_get';
      foreach ($contactIDs as $contactID) {
        $value =& $values[$contactID];
        $fn($contactID, $value, $context);
      }
    }
  }
}
/**
* Gather functions from tokens in tokens folder
*/
function civitoken_initialize() {
  static $civitoken_init = null;
  static $tokens = array();
  if ($civitoken_init){
    return $tokens;
  }
  static $tokenFiles = null;
  $config = CRM_Core_Config::singleton();
  if(!is_array($tokenFiles)){
    $directories = array( __DIR__  . '/tokens');
    if (!empty($config->customPHPPathDir)) {
      if (file_exists($config->customPHPPathDir . '/tokens')) {
        $directories[] = $config->customPHPPathDir . '/tokens';
      }
    }
    foreach ($directories as $directory) {
      $tokenFiles = _civitoken_civix_find_files($directory, '*.inc');
      foreach ($tokenFiles as $file) {
        require_once $file;
        $re = "/.*\\/([a-z]*).inc/";
        preg_match($re, $file, $matches);
        $tokens[] = $matches[1];
      }
    }
  }
  $civitoken_init = 1;
  return $tokens;
}
