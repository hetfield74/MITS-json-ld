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
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => 'Activer les attributs dans JSON+LD en tant qu’Offers ?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Contrôle l’affichage des variantes de produit (attributs) dans JSON-LD.
Si activé, les variantes telles que les couleurs ou les tailles sont incluses dans les données structurées.
Important : Ce réglage au niveau de l’article ne fonctionne que si, dans le module système,
« Afficher les attributs de produit dans JSON+LD (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES) » est défini sur « Oui ».
Si l’option globale est désactivée, ce réglage n’a aucun effet.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => 'Activer les propriétés du produit dans JSON+LD ?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Contrôle l’affichage des tags de produit dans JSON-LD.
Si activé, les propriétés enregistrées pour l’article sont affichées sous additionalProperty dans les données structurées.
Important : Ce réglage au niveau de l’article ne fonctionne que si, dans le module système,
« Afficher les propriétés du produit dans JSON+LD (MODULE_MITS_JSON_LD_ENABLE_TAGS) » est défini sur « Oui ».
Si l’option globale est désactivée, l’affichage des tags est bloqué quel que soit ce réglage.',
);


foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
