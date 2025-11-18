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
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => 'Attivare gli attributi in JSON-LD come Offers?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Controlla la visualizzazione delle varianti di prodotto (attributi) in JSON-LD.
Se attivato, varianti come colori o taglie vengono incluse nei dati strutturati.
Importante: Questo interruttore a livello di articolo funziona solo se nel modulo di sistema
“Mostrare gli attributi del prodotto in JSON-LD (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES)” è impostato su “Sì”.
Se l’impostazione globale è disattivata, questa opzione non ha alcun effetto.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => 'Attivare le proprietà del prodotto in JSON-LD?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Controlla la visualizzazione dei tag del prodotto in JSON-LD.
Se attivato, le proprietà del prodotto salvate nell’articolo vengono mostrate come additionalProperty nei dati strutturati.
Importante: Questo interruttore a livello di articolo funziona solo se nel modulo di sistema
“Mostrare le proprietà del prodotto in JSON-LD (MODULE_MITS_JSON_LD_ENABLE_TAGS)” è impostato su “Sì”.
Se l’impostazione globale è disattivata, la visualizzazione dei tag viene soppressa indipendentemente da questa opzione.',
);


foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
