<?php
/**
 * --------------------------------------------------------------
 * File: mits_json_ld.php
 * Date: 18.03.2019
 * Time: 15:28
 *
 * Author: Hetfield
 * Copyright: (c) 2019 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class mits_json_ld
{
    public string $code;
    public string $name;
    public string $version;
    public mixed $sort_order;
    public string $title;
    public string $description;
    public mixed $do_update;
    public bool $enabled;
    private bool $_check;
    private string $default_columns;

    /**
     *
     */
    public function __construct()
    {
        $this->code = 'mits_json_ld';
        $this->name = 'MODULE_' . strtoupper($this->code);
        $this->version = '1.1.2';

        $this->sort_order = defined($this->name . '_SORT_ORDER') ? constant($this->name . '_SORT_ORDER') : 0;
        $this->enabled = defined($this->name . '_STATUS') && (constant($this->name . '_STATUS') == 'true');
        $this->default_columns = 'configuration_key, configuration_value, configuration_group_id, sort_order, set_function';

        if (defined($this->name . '_VERSION') && $this->version != constant($this->name . '_VERSION')) {
            $this->do_update = (defined($this->name . '_UPDATE_AVAILABLE_TITLE')) ? constant($this->name . '_UPDATE_AVAILABLE_TITLE') : '';
        } else {
            $this->do_update = '';
        }

        $this->title = (defined($this->name . '_TITLE') ? constant($this->name . '_TITLE') : $this->code) . ' - v' . $this->version . $this->do_update;
        $this->description = '';
        if ($this->do_update != '') {
            $this->description .= '<a class="button btnbox but_green" style="text-align:center;" onclick="this.blur();" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $this->code . '&action=update') . '">' . constant($this->name . '_UPDATE_MODUL') . '</a><br>';
        }
        $this->description .= defined($this->name . '_DESCRIPTION') ? constant($this->name . '_DESCRIPTION') . '<hr style="margin:10px 0">' : '';

        if (!$this->enabled) {
            $this->description .= '<div style="text-align:center;margin:30px 0"><a class="button but_red" style="text-align:center;" onclick="return confirmLink(\'' . constant($this->name . '_CONFIRM_DELETE_MODUL') . '\', \'\' ,this);" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=' . $this->code . '&action=custom') . '">' . constant(
                $this->name . '_DELETE_MODUL'
              ) . '</a></div><br>';
        }
    }

    /**
     * @param $file
     * @return void
     */
    function process($file)
    {
        //do nothing
    }

    /**
     * @return string[]
     */
    public function display(): array
    {
        return array(
          'text' => '<br /><div align="center">' . xtc_button(BUTTON_SAVE) .
            xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $this->code)) . "</div>"
        );
    }

    /**
     * @return true
     */
    public function check()
    {
        if (!isset($this->_check)) {
            if (defined($this->name . '_STATUS')) {
                $this->_check = true;
            } else {
                $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '" . $this->name . "_STATUS'");
                $this->_check = xtc_db_num_rows($check_query);
            }
        }
        return $this->_check;
    }

    /**
     * @return void
     */
    public function install(): void
    {
        $country_query = xtc_db_query("SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES . " WHERE countries_id = " . (int)STORE_COUNTRY);
        $country = xtc_db_fetch_array($country_query);

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_STATUS', 'false', 6, 1, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_BREADCRUMB', 'false', 6, 2, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_PRODUCT', 'false', 6, 3, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_ATTRIBUTES', 'true', 6, 4, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_MAX_OFFERS', '100', 6, 5, NULL, now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_TAGS', 'true', 6, 6, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_MICRODATA_FIX', 'false', 6, 7, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_PRODUCT_REVIEWS', 'false', 6, 8, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_PRODUCT_REVIEWS_INFO', 'true', 6, 9, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_SEARCHFIELD', 'true', 6, 10, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_WEBSITE', 'true', 6, 11, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_LOGO', 'true', 6, 12, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_LOGOFILE', 'logo.gif', 6, 13, NULL, now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_ORGANISTATION', 'true', 6, 14, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_CONTACT', 'true', 6, 15, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SHOW_LOCATION', 'false', 6, 16, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");

        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_EMAIL_SUBJECT', '". $this->standard_mail_subject . "', 6, 30, 'xtc_cfg_input_email_language;" . $this->name . "_EMAIL_SUBJECT', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_NAME', '', '6', 31, 'xtc_cfg_input_email_language;" . $this->name . "_NAME', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_ALTERNATE_NAME', '', '6', 32, 'xtc_cfg_input_email_language;" . $this->name . "_ALTERNATE_NAME', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_WEBSITE_DESCRIPTION', '', 6, 33, 'xtc_cfg_input_email_language;" . $this->name . "_WEBSITE_DESCRIPTION', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_EMAIL', '" . CONTACT_US_EMAIL_ADDRESS . "', 6, 34, 'xtc_cfg_input_email_language;" . $this->name . "_EMAIL', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_TELEPHONE_DEFAULT', '', 6, 35, 'xtc_cfg_input_email_language;" . $this->name . "_TELEPHONE_DEFAULT', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_TELEPHONE_SERVICE', '', 6, 36, 'xtc_cfg_input_email_language;" . $this->name . "_TELEPHONE_SERVICE', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_TELEPHONE_TECHNICAL', '', 6, 37, 'xtc_cfg_input_email_language;" . $this->name . "_TELEPHONE_TECHNICAL', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_TELEPHONE_BILLING', '', 6, 38, 'xtc_cfg_input_email_language;" . $this->name . "_TELEPHONE_BILLING', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_TELEPHONE_SALES', '', 6, 39, 'xtc_cfg_input_email_language;" . $this->name . "_TELEPHONE_SALES', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_FAX', '', 6, 40, 'xtc_cfg_input_email_language;" . $this->name . "_FAX', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_SOCIAL_MEDIA', '', 6, 41, 'xtc_cfg_textarea(', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_STREETADDRESS', '', 6, 80, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_STREETADDRESS', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_ADDRESSLOCALITY', '', 6, 81, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_ADDRESSLOCALITY', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_POSTALCODE', '', 6, 82, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_POSTALCODE', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_ADDRESSCOUNTRY', '', 6, 83, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_ADDRESSCOUNTRY', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_GEO_LATITUDE', '', 6, 84, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_GEO_LATITUDE', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", use_function, date_added) VALUES ('" . $this->name . "_LOCATION_GEO_LONGITUDE', '',  6, 85, 'xtc_cfg_input_email_language;" . $this->name . "_LOCATION_GEO_LONGITUDE', 'xtc_get_email_language_names', now())");
        xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_VERSION', '" . $this->version . "', 6, 99, NULL, now())");

        if (!$this->columnExists(TABLE_PRODUCTS, 'mits_jsonld_attributes_enabled')) {
            xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD mits_jsonld_attributes_enabled TINYINT(1) NOT NULL DEFAULT 1");
        }
        if (!$this->columnExists(TABLE_PRODUCTS, 'mits_jsonld_tags_enabled')) {
            xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD mits_jsonld_tags_enabled TINYINT(1) NOT NULL DEFAULT 1");
        }

    }

    /**
     * @return void
     */
    public function remove(): void
    {
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
        xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE '" . $this->name . "_%'");
    }

    /**
     * @return void
     */
    public function update(): void
    {
        global $messageStack;

        xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . $this->version . "' WHERE configuration_key = '" . $this->name . "_VERSION'");

        if (!defined($this->name . '_ENABLE_ATTRIBUTES')) {
          xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_ATTRIBUTES', 'false', 6, 4, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        }
        if (!defined($this->name . '_MAX_OFFERS')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_MAX_OFFERS', '100', 6, 5, NULL, now())");
        }
        if (!defined($this->name . '_ENABLE_TAGS')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_TAGS', 'false', 6, 6, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        }
        if (!defined($this->name . '_ENABLE_MICRODATA_FIX')) {
            xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (" . $this->default_columns . ", date_added) VALUES ('" . $this->name . "_ENABLE_MICRODATA_FIX', 'false', 6, 7, 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        }
        if (!defined($this->name . '_WEBSITE_DESCRIPTION')) {
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " SET configuration_key = '" . $this->name . "_WEBSITE_DESCRIPTION' WHERE configuration_key = '" . $this->name . "_DESCRIPTION'");
        }

        if (!$this->columnExists(TABLE_PRODUCTS, 'mits_jsonld_attributes_enabled')) {
            xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD mits_jsonld_attributes_enabled TINYINT(1) NOT NULL DEFAULT 1");
        }
        if (!$this->columnExists(TABLE_PRODUCTS, 'mits_jsonld_tags_enabled')) {
            xtc_db_query("ALTER TABLE " . TABLE_PRODUCTS . " ADD mits_jsonld_tags_enabled TINYINT(1) NOT NULL DEFAULT 1");
        }

        $this->removeOldFiles();

        $messageStack->add_session(constant($this->name . '_UPDATE_FINISHED'), 'success');
    }

    /**
     * @return void
     */
    public function custom(): void
    {
        global $messageStack;

        $this->remove();
        $this->removeModulfiles();

        $messageStack->add_session(constant($this->name . '_DELETE_FINISHED'), 'success');

    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        $key = array(
          $this->name . '_STATUS',
          $this->name . '_SHOW_BREADCRUMB',
          $this->name . '_SHOW_PRODUCT',
          $this->name . '_ENABLE_ATTRIBUTES',
          $this->name . '_ENABLE_TAGS',
          $this->name . '_MAX_OFFERS',
          $this->name . '_ENABLE_MICRODATA_FIX',
          $this->name . '_SHOW_PRODUCT_REVIEWS',
          $this->name . '_SHOW_PRODUCT_REVIEWS_INFO',
          $this->name . '_SHOW_SEARCHFIELD',
          $this->name . '_SHOW_WEBSITE',
          $this->name . '_SHOW_LOGO',
          $this->name . '_LOGOFILE',
          $this->name . '_SHOW_ORGANISTATION',
          $this->name . '_SHOW_CONTACT',
          $this->name . '_SHOW_LOCATION',
          $this->name . '_NAME',
          $this->name . '_ALTERNATE_NAME',
          $this->name . '_WEBSITE_DESCRIPTION',
          $this->name . '_EMAIL',
          $this->name . '_TELEPHONE_DEFAULT',
          $this->name . '_TELEPHONE_SERVICE',
          $this->name . '_TELEPHONE_TECHNICAL',
          $this->name . '_TELEPHONE_BILLING',
          $this->name . '_TELEPHONE_SALES',
          $this->name . '_FAX',
          $this->name . '_SOCIAL_MEDIA',
          $this->name . '_LOCATION_STREETADDRESS',
          $this->name . '_LOCATION_ADDRESSLOCALITY',
          $this->name . '_LOCATION_POSTALCODE',
          $this->name . '_LOCATION_ADDRESSCOUNTRY',
          $this->name . '_LOCATION_GEO_LATITUDE',
          $this->name . '_LOCATION_GEO_LONGITUDE',
        );

        return $key;
    }

    /**
     * @param $table
     * @param $column
     * @return bool
     */
    private function columnExists($table, $column): bool
    {
        $res = xtc_db_query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return xtc_db_num_rows($res) > 0;
    }
  
    /**
     * @return void
     */
    protected function removeOldFiles(): void
    {
        $old_files_array = array(
          DIR_FS_DOCUMENT_ROOT . 'includes/extra/application_bottom/' . $this->code . '.php',
        );

        if (count($old_files_array) > 0) {
            foreach ($old_files_array as $delete_file) {
                if (is_file($delete_file)) {
                    unlink($delete_file);
                }
            }
        }
    }


    /**
     * @return void
     */
    protected function removeModulfiles(): void
    {
        $remove_files_array = array(
          DIR_FS_DOCUMENT_ROOT . (defined('DIR_ADMIN') ? DIR_ADMIN : 'admin/') . 'includes/modules/system/' . $this->code . '.php',
          DIR_FS_DOCUMENT_ROOT . 'lang/english/modules/system/' . $this->code . '.php',
          DIR_FS_DOCUMENT_ROOT . 'lang/german/modules/system/' . $this->code . '.php',
          DIR_FS_DOCUMENT_ROOT . 'includes/extra/header/header_head/' . $this->code . '.php',
          DIR_FS_DOCUMENT_ROOT . 'includes/extra/application_bottom/' . $this->code . '.php',
        );

        foreach ($remove_files_array as $delete_file) {
            if (is_file($delete_file)) {
                unlink($delete_file);
            }
        }
    }  

}
