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
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => 'Attributen in JSON-LD als Offers activeren?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Bepaalt de uitvoer van productvarianten (attributen) in JSON-LD. 
Wanneer geactiveerd, worden varianten zoals kleuren of maten in gestructureerde data weergegeven. 
Belangrijk: Deze artikelschakelaar werkt alleen wanneer in het systeemmodule 
“Artikelkenmerken (attributen) in JSON-LD weergeven (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES)” op “Ja” staat. 
Als de globale instelling is uitgeschakeld, heeft deze optie geen effect.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => 'Producteigenschappen in JSON-LD activeren?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Bepaalt de uitvoer van producttags in JSON-LD. 
Wanneer geactiveerd, worden in het artikel opgeslagen producteigenschappen als additionalProperty uitgegeven. 
Belangrijk: Deze artikelschakelaar werkt alleen wanneer in het systeemmodule 
“Producteigenschappen in JSON-LD weergeven (MODULE_MITS_JSON_LD_ENABLE_TAGS)” op “Ja” staat. 
Als de globale instelling is uitgeschakeld, wordt de uitvoer van tags onderdrukt, ongeacht deze optie.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
