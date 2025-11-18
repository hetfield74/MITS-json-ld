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
  'MODULE_' . $modulname . '_TITLE'        => 'MITS JSON-LD pour modified eCommerce Shopsoftware <span style="white-space:nowrap;">© par <span style="padding:2px;background:#ffe;color:#6a9;font-weight:bold;">Hetfield (MerZ IT-SerVice)</span></span>',
  'MODULE_' . $modulname . '_DESCRIPTION'  => '
    <a href="https://www.merz-it-service.de/" target="_blank">
      <img src="' . (ENABLE_SSL === true ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . DIR_WS_IMAGES . 'merz-it-service.png" border="0" alt="MerZ IT-SerVice" style="display:block;max-width:100%;height:auto;" />
    </a><br />
    <p style="font-size: larger">Ce module ajoute à votre boutique modified eCommerce les balises JSON-LD recommandées par Google.</p>
    <p>Ce module prend en charge les types de balisage suivants :</p>
    <ul style="font-size: larger">
      <li>WebSite <small>name, alternateName, description, url et logo</small></li>
      <li>Organization <small>name, alternateName, description, url, logo et ContactPoints pour customer service, technical support, billing support, sales</small></li>
      <li>LocalBusiness <small>name, image, url, telephone, address, geo, sameAs</small></li>
      <li>ContactPage <small>name, url, description</small></li>
      <li>Breadcrumb</li>
      <li>Product <small>name, image, description, brand, priceCurrency, priceValidUntil (fixé à 1 mois), price, url, itemCondition, availability, mpn, sku, gtin13 et reviews</small></li>
      <li>Review (Rating et aggregateRating) <small>ratingValue, worstRating, bestRating, author, datePublished, reviewBody</small></li>
      <li>Sitelink Searchbox</li>
    </ul>
    <p style="font-size: larger">Ce module peut bien sûr être étendu et adapté selon vos besoins. Pour toute demande de personnalisation, veuillez nous contacter directement.<br />
    <div style="text-align:center;">
      <small>La version la plus récente du module est toujours disponible sur Github !</small><br />
      <a style="background:#6a9;color:#444" target="_blank" href="https://github.com/hetfield74/MITS-json-ld" class="button" onclick="this.blur();">MITS JSON-LD sur Github</a>
    </div>
    <p>Pour toute question, problème ou demande concernant ce module ou tout autre sujet lié à modified eCommerce Shopsoftware, n’hésitez pas à nous contacter :</p> 
    <div style="text-align:center;"><a style="background:#6a9;color:#444" target="_blank" href="https://www.merz-it-service.de/Kontakt.html" class="button" onclick="this.blur();">Page de contact MerZ-IT-SerVice.de</a></div>  
',
  'MODULE_' . $modulname . '_STATUS_TITLE' => 'Activer le module ?',
  'MODULE_' . $modulname . '_STATUS_DESC'  => 'Activer le module MITS JSON-LD dans la boutique ?',

  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_TITLE' => 'Activer le fil d’Ariane ?',
  'MODULE_' . $modulname . '_SHOW_BREADCRUMB_DESC'  => 'Activer le balisage JSON-LD pour le fil d’Ariane ?',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_TITLE' => 'Activer les produits ?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_DESC'  => 'Activer le balisage JSON-LD pour les produits sur la page de détails ?',

  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_TITLE' => 'Afficher les attributs dans JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_ATTRIBUTES_DESC'  => 'Les attributs de produits doivent-ils être affichés comme des offres ?',

  'MODULE_' . $modulname . '_ENABLE_TAGS_TITLE' => 'Afficher les propriétés du produit dans JSON-LD',
  'MODULE_' . $modulname . '_ENABLE_TAGS_DESC'  => 'Les propriétés du produit (tags) doivent-elles être affichées dans additionalProperty ?',

  'MODULE_' . $modulname . '_MAX_OFFERS_TITLE' => 'Nombre maximal d’offres',
  'MODULE_' . $modulname . '_MAX_OFFERS_DESC'  => 'Évite les problèmes de mémoire en cas de nombreux attributs. Par défaut : 100.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_TITLE' => 'Activer les avis des produits ?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_DESC'  => 'Activer le balisage JSON-LD pour les avis sur la page produit ? Seulement en combinaison avec le balisage Produit.',

  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_TITLE' => 'Activer la page détaillée d’un avis ?',
  'MODULE_' . $modulname . '_SHOW_PRODUCT_REVIEWS_INFO_DESC'  => 'Activer le balisage JSON-LD pour la page de détails d’un avis ?',

  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_TITLE' => 'Activer la Sitelinks Searchbox ?',
  'MODULE_' . $modulname . '_SHOW_SEARCHFIELD_DESC'  => 'Activer le balisage JSON-LD pour la Sitelinks Searchbox ?',

  'MODULE_' . $modulname . '_SHOW_WEBSITE_TITLE' => 'Activer WebSite ?',
  'MODULE_' . $modulname . '_SHOW_WEBSITE_DESC'  => 'Activer le balisage JSON-LD pour WebSite ?',

  'MODULE_' . $modulname . '_SHOW_LOGO_TITLE' => 'Activer le logo ?',
  'MODULE_' . $modulname . '_SHOW_LOGO_DESC'  => 'Le logo est utilisé dans les balisages WebSite, Organization et LocalBusiness.',

  'MODULE_' . $modulname . '_LOGOFILE_TITLE' => 'Logo',
  'MODULE_' . $modulname . '_LOGOFILE_DESC'  => 'Votre logo d’entreprise, nom de fichier sans chemin. Le fichier doit se trouver dans le dossier img du template utilisé (par défaut : logo.gif).',

  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_TITLE' => 'Activer Organization ?',
  'MODULE_' . $modulname . '_SHOW_ORGANISTATION_DESC'  => 'Activer le balisage JSON-LD pour Organization ?',

  'MODULE_' . $modulname . '_SHOW_CONTACT_TITLE' => 'Activer ContactPage ?',
  'MODULE_' . $modulname . '_SHOW_CONTACT_DESC'  => 'Activer le balisage JSON-LD pour ContactPage ?',

  'MODULE_' . $modulname . '_SHOW_LOCATION_TITLE' => 'Activer LocalBusiness ?',
  'MODULE_' . $modulname . '_SHOW_LOCATION_DESC'  => 'Activer le balisage JSON-LD pour LocalBusiness ?',

  'MODULE_' . $modulname . '_NAME_TITLE' => 'Nom de l’entreprise / du site',
  'MODULE_' . $modulname . '_NAME_DESC'  => 'Le nom est utilisé dans les balisages WebSite, Organization et LocalBusiness.',

  'MODULE_' . $modulname . '_ALTERNATE_NAME_TITLE' => 'Nom alternatif de l’entreprise / du site',
  'MODULE_' . $modulname . '_ALTERNATE_NAME_DESC'  => 'Le nom alternatif est utilisé dans les balisages WebSite, Organization et LocalBusiness.',

  'MODULE_' . $modulname . '_DESCRIPTION_TITLE' => 'Description de l’entreprise / du site',
  'MODULE_' . $modulname . '_DESCRIPTION_DESC'  => 'Utilisé dans WebSite, Organization et LocalBusiness. Si vide, la méta-description standard sera utilisée.',

  'MODULE_' . $modulname . '_EMAIL_TITLE' => 'Adresse e-mail de l’entreprise / du site',
  'MODULE_' . $modulname . '_EMAIL_DESC'  => 'Utilisée dans les balisages WebSite, Organization et LocalBusiness.',

  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_TITLE' => 'Numéro de téléphone',
  'MODULE_' . $modulname . '_TELEPHONE_DEFAULT_DESC'  => 'Numéro principal utilisé pour Organization et LocalBusiness. Format requis : +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_TITLE' => 'Numéro du service client',
  'MODULE_' . $modulname . '_TELEPHONE_SERVICE_DESC'  => 'Numéro utilisé pour le service client. Format requis : +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_TITLE' => 'Numéro du support technique',
  'MODULE_' . $modulname . '_TELEPHONE_TECHNICAL_DESC'  => 'Numéro pour le support technique. Format requis : +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_BILLING_TITLE' => 'Numéro de la facturation',
  'MODULE_' . $modulname . '_TELEPHONE_BILLING_DESC'  => 'Numéro pour les questions de facturation. Format requis : +49-2722-631363',

  'MODULE_' . $modulname . '_TELEPHONE_SALES_TITLE' => 'Numéro du service commercial',
  'MODULE_' . $modulname . '_TELEPHONE_SALES_DESC'  => 'Numéro du service commercial. Format requis : +49-2722-631363',

  'MODULE_' . $modulname . '_FAX_TITLE' => 'Numéro de fax',
  'MODULE_' . $modulname . '_FAX_DESC'  => 'Numéro de fax de l’entreprise. Format requis : +49-2722-631400',

  'MODULE_' . $modulname . '_SOCIAL_MEDIA_TITLE' => 'Profils de réseaux sociaux',
  'MODULE_' . $modulname . '_SOCIAL_MEDIA_DESC'  => 'Saisissez ici les URL complètes de vos profils sociaux. Séparez plusieurs URL par une virgule.',

  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_TITLE' => 'Rue / numéro',
  'MODULE_' . $modulname . '_LOCATION_STREETADDRESS_DESC'  => 'Rue et numéro pour le balisage LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_TITLE' => 'Ville',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSLOCALITY_DESC'  => 'Ville pour le balisage LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_TITLE' => 'Code postal',
  'MODULE_' . $modulname . '_LOCATION_POSTALCODE_DESC'  => 'Code postal utilisé dans LocalBusiness.',

  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_TITLE' => 'Pays',
  'MODULE_' . $modulname . '_LOCATION_ADDRESSCOUNTRY_DESC'  => 'Pays en code ISO-2 (par ex. DE).',

  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_TITLE' => 'Latitude GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LATITUDE_DESC'  => 'Optionnel. Laisser vide si non utilisé.',

  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_TITLE' => 'Longitude GEO',
  'MODULE_' . $modulname . '_LOCATION_GEO_LONGITUDE_DESC'  => 'Optionnel. Laisser vide si non utilisé.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;padding:2px;border:1px solid #900;">Veuillez effectuer une mise à jour !</span>',
  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_DESC'  => '',
  'MODULE_' . $modulname . '_UPDATE_FINISHED'        => 'Le module MITS JSON-LD a été mis à jour.',
  'MODULE_' . $modulname . '_UPDATE_ERROR'           => 'Erreur',
  'MODULE_' . $modulname . '_UPDATE_MODUL'           => 'Mettre à jour le module',
  'MODULE_' . $modulname . '_DELETE_MODUL'           => 'Supprimer complètement MITS JSON-LD du serveur',
  'MODULE_' . $modulname . '_CONFIRM_DELETE_MODUL'   => 'Voulez-vous vraiment supprimer le module MITS JSON-LD ainsi que tous ses fichiers du serveur ?',
  'MODULE_' . $modulname . '_DELETE_FINISHED'        => 'Le module MITS JSON-LD a été supprimé du serveur.',
);

foreach ($lang_array as $key => $val) {
    defined($key) || define($key, $val);
}
