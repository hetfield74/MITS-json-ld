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

$modulname = strtoupper("mits_json_ld");

$lang_array = array(
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD para modified eCommerce Shopsoftware <span style="white-space:nowrap;">© por <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Este módulo amplía tu tienda modified eCommerce con los marcados JSON-LD recomendados por Google.</p>
    <p>El módulo admite los siguientes tipos de marcado:</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url y logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo y ContactPoints para customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (fijo a 1 mes), price, url, itemCondition, availability, mpn, sku, gtin13 y reviews</small></li>
      <li>Review (rating y aggregateRating) <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Este módulo puede ampliarse o adaptarse según tus necesidades. Para personalizaciones individuales, contáctanos directamente.<br />
    <div style="text-align:center;">
      <small>¡La versión más actual del módulo siempre está disponible en Github!</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON-LD en Github</a>
    </div>
    <p>Si tienes preguntas, problemas o deseas soporte adicional sobre modified eCommerce Shopsoftware, solo contáctanos:</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Página de contacto en MerZ-IT-SerVice.de</a></div>  
',

  'MODULE_' . $modulname . '_STATUS_TITLE' => '¿Activar el módulo?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => '¿Activar el módulo MITS JSON-LD en la tienda?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => '¿Activar Breadcrumb?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => '¿Activar el marcado JSON-LD para Breadcrumb?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => '¿Activar productos?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => '¿Activar el marcado JSON-LD para los productos en la página de detalle?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Mostrar atributos en JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => '¿Deben mostrarse los atributos del producto como ofertas?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Mostrar propiedades del producto en JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => '¿Deben mostrarse las propiedades (tags) del producto en additionalProperty?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Número máximo de ofertas',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Evita problemas de memoria con muchos atributos. Valor por defecto: 100.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => '¿Activar reseñas de productos?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => '¿Activar marcado JSON-LD para reseñas en la página del producto? Solo junto con marcado de producto.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => '¿Activar página de detalles de reseñas?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => '¿Activar marcado JSON-LD para la página de detalle de una reseña?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => '¿Activar Sitelinks Searchbox?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => '¿Activar marcado JSON-LD para la Sitelinks Searchbox?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => '¿Activar WebSite?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => '¿Activar marcado JSON-LD para WebSite?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => '¿Activar Logo?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'El logo se usa en los tipos WebSite, Organization y LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Logo de la empresa, indicar solo nombre de archivo. Debe estar en la carpeta img del template utilizado (estándar: logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => '¿Activar Organization?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => '¿Activar marcado JSON-LD para Organization?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => '¿Activar ContactPage?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => '¿Activar marcado JSON-LD para ContactPage?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => '¿Activar LocalBusiness?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => '¿Activar marcado JSON-LD para LocalBusiness?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Nombre de la empresa/sitio web',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Se utiliza en WebSite, Organization y LocalBusiness.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Nombre alternativo de la empresa/sitio web',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Se utiliza en WebSite, Organization y LocalBusiness.',

  'MODULE_' . $modulname . '_DESCRIPTION_TITLE' => 'Descripción de la empresa/sitio web',
  'MODULE_' . $modulname . '_DESCRIPTION_DESC'  => 'Usada para WebSite, Organization y LocalBusiness. Si está vacía, se usa la meta descripción estándar.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'Dirección de correo electrónico',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'Usado en WebSite, Organization y LocalBusiness.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Número de teléfono',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'Número principal utilizado en Organization y LocalBusiness. Formato: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Número de servicio al cliente',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Número para atención al cliente. Formato: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Número de soporte técnico',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Número para soporte técnico. Formato: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Número de facturación',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Número para consultas de facturación. Formato: +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Número de ventas',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Número para el departamento de ventas. Formato: +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Número de fax',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Número de fax de la empresa. Formato: +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Perfiles de redes sociales',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Introduce las URL completas de tus perfiles. Separar varias con comas.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Calle / número',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Se utiliza en el marcado LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'Ciudad',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'Ciudad utilizada en LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'Código postal',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'Código postal utilizado en LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'País',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'Código ISO-2 del país (por ejemplo: DE para Alemania).',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'Latitud GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Opcional. Dejar vacío si no se usa.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'Longitud GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Opcional. Dejar vacío si no se usa.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">¡Actualización del módulo necesaria!</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'El módulo MITS JSON-LD ha sido actualizado.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Error',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Actualizar módulo',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'Eliminar completamente MITS JSON-LD del servidor',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => '¿Deseas eliminar el módulo MITS JSON-LD y todos sus archivos del servidor?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'El módulo MITS JSON-LD ha sido eliminado del servidor.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
