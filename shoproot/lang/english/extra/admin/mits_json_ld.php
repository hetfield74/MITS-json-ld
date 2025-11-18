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

$lang_array = array(
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => 'Activate attributes in JSON+LD as Offers?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Controls the output of product variants (attributes) in JSON-LD.
If enabled, variants such as colours or sizes are included in the structured data.
Important: This product-level switch only works if the system module
“Output product attributes in JSON+LD (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES)” is set to “Yes”.
If the global switch is disabled, this setting has no effect.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => 'Activate product properties in JSON+LD?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Controls the output of product tags in JSON-LD.
If enabled, product properties stored for the item are output as additionalProperty in the structured data.
Important: This product-level switch only works if the system module
“Output product properties in JSON+LD (MODULE_MITS_JSON_LD_ENABLE_TAGS)” is set to “Yes”.
If the global switch is disabled, tag output is suppressed regardless of this setting.',
);


foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
