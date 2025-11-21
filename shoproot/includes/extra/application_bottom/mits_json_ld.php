<?php
/**
 * --------------------------------------------------------------
 * File: mits_json_ld.php
 * Date: 21.11.2025
 * Time: 12:45
 *
 * Author: Hetfield
 * Copyright: (c) 2025 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

if (defined('MODULE_MITS_JSON_LD_STATUS') && MODULE_MITS_JSON_LD_STATUS == 'true') {
    if (defined('MODULE_MITS_JSON_LD_ENABLE_MICRODATA_FIX') && MODULE_MITS_JSON_LD_ENABLE_MICRODATA_FIX == 'true') {
        ?>
      <script>
        $(document).ready(function () {
          $('meta[itemprop]').remove();
          $('[itemscope], [itemtype], [itemprop]').each(function () {
            $(this).removeAttr('itemscope')
              .removeAttr('itemtype')
              .removeAttr('itemprop');
          });
        });
      </script>
        <?php
    }
}
