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

if (defined('MODULE_MITS_JSON_LD_STATUS') && MODULE_MITS_JSON_LD_STATUS == 'true'
 && defined('MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT') && defined('MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP')
 && defined('MITS_JSON_LD_ENABLE_TAGS_TEXT') && defined('MITS_JSON_LD_ENABLE_TAGS_TOOLTIP')
) {
    ?>
  <table class="tableInput border0">
    <tr>
      <td class="main">
          <?php echo MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT;?>
        <span class="tooltip">
          <img src="images/icons/tooltip_icon.png" alt="Tooltip" style="border:0;">
          <em><?php echo MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP;?></em>
        </span>
      </td>
      <td>
        <span class="main"><?php
              echo draw_on_off_selection('mits_jsonld_attributes_enabled', 'checkbox', isset($pInfo->mits_jsonld_attributes_enabled) && $pInfo->mits_jsonld_attributes_enabled == '1' ? true : false) ?>
        </span>
      </td>
    </tr>
    <tr>
      <td class="main">
          <?php echo MITS_JSON_LD_ENABLE_TAGS_TEXT;?>
        <span class="tooltip">
          <img src="images/icons/tooltip_icon.png" alt="Tooltip" style="border:0;">
          <em><?php echo MITS_JSON_LD_ENABLE_TAGS_TOOLTIP;?></em>
        </span>
      </td>
      <td>
        <span class="main"><?php
              echo draw_on_off_selection('mits_jsonld_tags_enabled', 'checkbox', isset($pInfo->mits_jsonld_tags_enabled) && $pInfo->mits_jsonld_tags_enabled == '1' ? true : false) ?>
        </span>
      </td>
    </tr>
  </table>
  <script>
    $(document).ready(function () {
      $("[name='mits_jsonld_attributes_enabled']").closest("tr").detach().insertAfter($("[name='shipping_status']").closest("tr"));
      $("[name='mits_jsonld_tags_enabled']").closest("tr").detach().insertAfter($("[name='mits_jsonld_attributes_enabled']").closest("tr"));
    });
  </script>
    <?php
}