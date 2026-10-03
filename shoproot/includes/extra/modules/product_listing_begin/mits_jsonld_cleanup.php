<?php
/**
 * --------------------------------------------------------------
 * File: mits_jsonld_cleanup.php
 * Date: 09.12.2025
 * Time: 13:42
 *
 * Author: Hetfield
 * Copyright: (c) 2025 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

if (defined('MODULE_MITS_JSON_LD_STATUS')
  && MODULE_MITS_JSON_LD_STATUS == 'true'
  && function_exists('mits_jsonld_extract_and_strip_from_text')
  && function_exists('mits_jsonld_custom_enabled')
  && basename($PHP_SELF) != FILENAME_ADVANCED_SEARCH_RESULT
  && isset($current_category_id)
  && $current_category_id != 0
  && isset($module_smarty)
  && isset($category)
  && is_array($category)
  && mits_jsonld_custom_enabled()
) {
    if (!isset($GLOBALS['mits_jsonld_custom_nodes'])) {
        $GLOBALS['mits_jsonld_custom_nodes'] = [];
    }

    $descriptionMap = [
      'categories_short_description'   => 'CATEGORIES_SHORT_DESCRIPTION',
      'categories_description'         => 'CATEGORIES_DESCRIPTION',
      'categories_description_2'       => 'CATEGORIES_DESCRIPTION_2',
    ];

    foreach ($descriptionMap as $field => $smartyKey) {
        if (empty($category[$field]) || !is_string($category[$field])) {
            continue;
        }

        list($nodes, $cleanHtml) = mits_jsonld_extract_and_strip_from_text($category[$field]);

        if (!empty($nodes)) {
            $GLOBALS['mits_jsonld_custom_nodes'] = array_merge($GLOBALS['mits_jsonld_custom_nodes'], $nodes);
        }

        $category[$field] = $cleanHtml;
        $module_smarty->assign($smartyKey, $cleanHtml);
    }
}
