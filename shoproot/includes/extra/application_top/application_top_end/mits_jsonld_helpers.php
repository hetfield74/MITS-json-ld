<?php
/**
 * --------------------------------------------------------------
 * File: mits_jsonld_helpers.php
 * Date: 09.12.2025
 * Time: 13:56
 *
 * Author: Hetfield
 * Copyright: (c) 2025 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

if (defined('MODULE_MITS_JSON_LD_STATUS') && MODULE_MITS_JSON_LD_STATUS == 'true') {

    if (!function_exists('mits_jsonld_custom_enabled')) {
        function mits_jsonld_custom_enabled(): bool
        {
            return defined('MODULE_MITS_JSON_LD_ENABLE_CUSTOM_JSON')
              && MODULE_MITS_JSON_LD_ENABLE_CUSTOM_JSON === 'true';
        }
    }


    if (!function_exists('mits_jsonld_extract_from_text')) {
        function mits_jsonld_extract_from_text($html): array
        {
            $nodes = [];

            if (empty($html) || !is_string($html)) {
                return $nodes;
            }

            if (!preg_match_all(
              '#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is',
              $html,
              $matches
            )) {
                return $nodes;
            }

            foreach ($matches[1] as $rawJson) {
                $rawJson = trim($rawJson);
                if ($rawJson === '') {
                    continue;
                }

                $data = json_decode($rawJson, true);
                if (!is_array($data)) {
                    continue;
                }

                if (isset($data['@graph']) && is_array($data['@graph'])) {
                    foreach ($data['@graph'] as $gNode) {
                        if (is_array($gNode)) {
                            $nodes[] = $gNode;
                        }
                    }
                } else {
                    $nodes[] = $data;
                }
            }

            return $nodes;
        }
    }

    if (!function_exists('mits_jsonld_extract_and_strip_from_text')) {
        function mits_jsonld_extract_and_strip_from_text($html): array
        {
            if (empty($html) || !is_string($html)) {
                return [[], $html];
            }

            $nodes = mits_jsonld_extract_from_text($html);
            $cleanHtml = preg_replace(
              '#<script[^>]+type=["\']application/ld\+json["\'][^>]*>.*?</script>#is',
              '',
              $html
            );

            return [$nodes, $cleanHtml];
        }
    }

}
