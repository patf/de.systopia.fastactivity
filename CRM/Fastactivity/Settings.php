<?php

use CRM_Fastactivity_ExtensionUtil as E;

class CRM_Fastactivity_Settings {

  CONST TITLE = 'FastActivity';

  /**
   * Get settings prefix name for this extension
   * @return string
   */
  public static function getPrefix() {
    return 'fastactivity_';
  }

  /**
   * Get filter of valid settings for this extension
   * @return array
   */
  public static function getFilter() {
    return array('group' => 'fastactivity');
  }

  /**
   * Get name of setting
   * @param: setting name
   * @prefix: Boolean
   * @return: string
   */
  public static function getName($name, $prefix = false) {
    $ret = str_replace(self::getPrefix(),'',$name);
    if ($prefix) {
      $ret = self::getPrefix().$ret;
    }
    return $ret;
  }

  /**
   * Save settings. Accepts an array of name=>value pairs.  Name can be with or without prefix (it will be added if missing).
   * @param array $values Array of settings and values with or without prefix (eg. array(smartdebit_username => 'test')) to save
   */
  public static function save($settings) {
    foreach ($settings as $name => $value) {
      $prefixedSettings[self::getName($name, TRUE)] = $value;
    }
    civicrm_api3('setting', 'create', $prefixedSettings);
  }

  /**
   * Read setting that has prefix in database and return single value
   * @param $name
   * @return mixed
   */
  public static function getValue($name) {
    $className = E::CLASS_PREFIX . '_Settings';
    $settings = civicrm_api3('setting', 'get', array('return' => $className::getName($name,true)));
    $domainID = CRM_Core_Config::domainID();
    if (isset($settings['values'][$domainID][$className::getName($name,true)])) {
      return $settings['values'][$domainID][$className::getName($name, true)];
    }
    return '';
  }

  /**
   * Get settings
   * @param array $settings of settings (eg. array(username, password))
   *
   * @return array
   */
  public static function get($settings) {
    if ((!is_array($settings) || empty($settings))) {
      return array();
    }

    $domainID = CRM_Core_Config::domainID();

    foreach ($settings as $name) {
      $prefixedSettings[] = self::getName($name, TRUE);
    }
    $settingsResult = civicrm_api3('setting', 'get', array('return' => $prefixedSettings));
    if (isset($settingsResult['values'][$domainID])) {
      foreach ($settingsResult['values'][$domainID] as $name => $value) {
        $unprefixedSettings[self::getName($name)] = $value;
      }
      return empty($unprefixedSettings) ? NULL : $unprefixedSettings;
    }
    return array();
  }

}
