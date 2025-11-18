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
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TEXT' => '¿Activar atributos en JSON+LD como Offers?',
  'MITS_JSON_LD_ENABLE_ATTRIBUTES_TOOLTIP' => 'Controla la salida de variantes del producto (atributos) en JSON-LD.
Si está activado, variantes como colores o tamaños se incluirán en los datos estructurados.
Importante: Este interruptor a nivel de artículo solo funciona si en el módulo del sistema
“Mostrar atributos del producto en JSON+LD (MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES)” está configurado en “Sí”.
Si el ajuste global está desactivado, esta opción no tendrá efecto.',

  'MITS_JSON_LD_ENABLE_TAGS_TEXT' => '¿Activar propiedades del producto en JSON+LD?',
  'MITS_JSON_LD_ENABLE_TAGS_TOOLTIP' => 'Controla la salida de las etiquetas del producto en JSON-LD.
Si está activado, las propiedades del artículo se muestran como additionalProperty en los datos estructurados.
Importante: Este interruptor a nivel de artículo solo funciona si en el módulo del sistema
“Mostrar propiedades del producto en JSON+LD (MODULE_MITS_JSON_LD_ENABLE_TAGS)” está configurado en “Sí”.
Si el ajuste global está desactivado, la salida de etiquetas se bloqueará independientemente de esta opción.',
);


foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
