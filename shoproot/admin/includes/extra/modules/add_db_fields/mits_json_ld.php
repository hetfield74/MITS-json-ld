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

if (defined('MODULE_MITS_JSON_LD_STATUS') && MODULE_MITS_JSON_LD_STATUS == 'true') {
    $add_products_fields[] = 'mits_jsonld_attributes_enabled';
    $add_products_fields[] = 'mits_jsonld_tags_enabled';
}