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
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => 'Attribute in JSON+LD als Offers aktivieren?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Steuert die Ausgabe der Produktvarianten (Attribute) im JSON-LD. 
Wenn aktiviert, werden Varianten wie z. B. Farben oder Gr&ouml;&szlig;en im strukturierten Datenformat ausgegeben. 
Wichtig: Dieser Artikelschalter funktioniert nur, wenn im Systemmodul 
&bdquo;Artikelmerkmale (Attribute) im JSON+LD ausgeben (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES)&ldquo; auf &sbquo;Ja&lsquo; steht. 
Ist der globale Schalter deaktiviert, hat diese Einstellung keine Wirkung.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => 'Artikeleigenschaften in JSON+LD aktivieren?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Steuert die Ausgabe der Produkt-Tags im JSON-LD. 
Wenn aktiviert, werden beim Artikel hinterlegte Artikeleigenschaften als additionalProperty in den strukturierten Daten ausgegeben. 
Wichtig: Dieser Artikelschalter funktioniert nur, wenn im Systemmodul 
&bdquo;Artikeleigenschaften im JSON+LD ausgeben in JSON-LD ausgeben (MODULE_MITS_JSON_LD_ENABLE_TAGS)&ldquo; auf &sbquo;Ja&lsquo; steht. 
Ist der globale Schalter deaktiviert, wird die Tag-Ausgabe unabh&auml;ngig von dieser Einstellung unterdr&uuml;ckt.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
