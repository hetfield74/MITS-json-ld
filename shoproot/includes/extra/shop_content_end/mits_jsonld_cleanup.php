<?php
/**
 * --------------------------------------------------------------
 * File: mits_jsonld_cleanup.php
 * Date: 09.12.2025
 * Time: 13:43
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
  && isset($smarty)
  && mits_jsonld_custom_enabled()
) {
    if (!isset($GLOBALS['mits_jsonld_custom_nodes'])) {
        $GLOBALS['mits_jsonld_custom_nodes'] = [];
    }

    $cleanHtml = null;

    if (isset($content_body) && !empty($content_body) && is_string($content_body)) {
        list($nodes, $cleanHtml) = mits_jsonld_extract_and_strip_from_text($content_body);

        if (!empty($nodes)) {
            $GLOBALS['mits_jsonld_custom_nodes'] = array_merge($GLOBALS['mits_jsonld_custom_nodes'], $nodes);
        }

        $content_body = $cleanHtml;
    }

    if (isset($shop_content_data['content_text']) && !empty($shop_content_data['content_text']) && is_string($shop_content_data['content_text'])) {
        list($nodes, $cleanContentText) = mits_jsonld_extract_and_strip_from_text($shop_content_data['content_text']);

        if (!empty($nodes)) {
            $GLOBALS['mits_jsonld_custom_nodes'] = array_merge($GLOBALS['mits_jsonld_custom_nodes'], $nodes);
        }

        $shop_content_data['content_text'] = $cleanContentText;

        if ($cleanHtml === null) {
            $cleanHtml = $cleanContentText;
        }
    }

    if ($cleanHtml !== null) {
        $smarty->assign('CONTENT_BODY', $cleanHtml);
    }
}
