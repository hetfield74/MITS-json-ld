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

  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_TITLE' => 'Activer la correction Microdata ?',
  'MODULE_' . $modulname . '_ENABLE_MICRODATA_FIX_DESC'  => '<i>Supprime les attributs Microdata de la boutique à l\'aide de jQuery, au cas où le schéma Microdata serait encore présent dans le template utilisé. Les données structurées en double (JSON-LD et Microdata) ne sont pas idéales car elles peuvent entraîner des incohérences.',

  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_TITLE' => 'Détecter et intégrer automatiquement le JSON-LD personnalisé à partir des textes ?',
  'MODULE_' . $modulname . '_ENABLE_CUSTOM_JSON_DESC'  => 'Si activé, le module recherche les blocs &lt;script type="application/ld+json"&gt;&mldr;&lt;/script&gt; intégrés dans les pages produits et de contenu, les retire du texte et les intègre correctement dans le JSON-LD central du module.<br><br><strong>Remarque :</strong> Cette fonction n\'est nécessaire que si des données structurées sont intégrées dans l\'éditeur. Pour les textes très volumineux ou les boutiques à trafic élevé, cela peut entraîner une légère augmentation de la charge du serveur.',

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

  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_TITLE' => 'Description de l’entreprise / du site',
  'MODULE_' . $modulname . '_WEBSITE_DESCRIPTION_DESC'  => 'Utilisé dans WebSite, Organization et LocalBusiness. Si vide, la méta-description standard sera utilisée.',

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

  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_TITLE' => 'Afficher les ShippingDetails en JSON-LD ?',
  'MODULE_' . $modulname . '_ENABLE_SHIPPING_DETAILS_DESC'  => 'Si "Oui", les informations de livraison configurées ci-dessous seront affichées comme <code>shippingDetails</code> dans les Offres.',

  'MODULE_' . $modulname . '_SHIPPING_CONFIG_TITLE' => 'Configuration de la livraison pour shippingDetails',
  'MODULE_' . $modulname . '_SHIPPING_CONFIG_DESC'  => '<code>country|label|price|currency|handlingMin|handlingMax|transitMin|transitMax|minValue|maxValue</code>

<p><strong>Champs:</strong></p>
<ul>
<li><b>country</b>: Codes pays (ISO2), séparés par des virgules
  &nbsp;&nbsp;ex. <code>DE</code> ou <code>DE,AT,CH</code></li>
<li><b>label</b>: Nom de la méthode de livraison
  &nbsp;&nbsp;ex. <code>DHL Standard</code>, <code>Livraison Internationale</code></li>
<li><b>price</b>: Frais de livraison<br>
  &nbsp;&nbsp;&ndash; <code>0.00</code> pour gratuit<br>
  &nbsp;&nbsp;&ndash; <code>free</code> est automatiquement converti en <code>0.00</code></li>
<li><b>currency</b>: Devise, ex. <code>EUR</code></li>
<li><b>handlingMin / handlingMax</b>: Temps de traitement en jours
  &nbsp;&nbsp;&rarr; ex. <code>0|1</code> = entre 0 et 1 jour</li>
<li><b>transitMin / transitMax</b>: Temps de transit/livraison en jours
  &nbsp;&nbsp;&rarr; ex. <code>1|3</code> = livraison entre 1 et 3 jours</li>
<li><b>minValue</b> (facultatif): Valeur minimale des marchandises à partir de laquelle cette règle de livraison s\'applique</li>
<li><b>maxValue</b> (facultatif): Valeur maximale des marchandises jusqu\'à laquelle la règle s\'applique</li>
</ul>
<p>Si minValue/maxValue sont laissés vides &rarr; s\'applique à toutes les valeurs de marchandises.</p>
<p><strong>Exemples:</strong></p>
<ol>
<li>Allemagne & Autriche, Livraison standard 4,90 €
  <code>DE,AT|Livraison Standard|4.90|EUR|0|1|1|3</code></li>
<li>Allemagne, Livraison gratuite à partir de 150 €
  <code>DE|DHL Standard à partir de 150 EUR|0.00|EUR|0|1|1|3|150|</code></li>
<li>Suisse, Livraison internationale 9,90 €, sans limitation de valeur de marchandises
  <code>CH|Livraison Internationale|9.90|EUR|0|1|2|5</code></li>
<li>Livraison UE avec valeur minimale et maximale
  <code>EU|Livraison UE|12.90|EUR|0|2|3|7|50|200</code></li>
</ol>
<p><strong>Remarques:</strong></p>
<ul>
<li>Chaque ligne génère sa propre structure <em>OfferShippingDetails</em>.</li>
<li>Si plusieurs pays sont spécifiés, le système crée automatiquement des entrées individuelles par pays.</li>
<li>minValue/maxValue sont facultatifs &ndash; si les champs sont vides, aucune restriction ne s\'applique.</li>
</ul>',

  'MODULE_' . $modulname . '_ENABLE_RETURNS_TITLE' => 'Afficher la Politique de Retour (hasMerchantReturnPolicy)?',
  'MODULE_' . $modulname . '_ENABLE_RETURNS_DESC'  => 'Si "Oui", la politique de retour configurée ci-dessous sera affichée comme <code>hasMerchantReturnPolicy</code> dans les Offres.',

  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_TITLE' => 'Configuration de la Politique de Retour',
  'MODULE_' . $modulname . '_RETURN_POLICY_CONFIG_DESC'  => 'Entrez une règle de retour par ligne. Format:<br>
<code>country|minDays|maxDays|category|feeType|method</code>
<p><strong>Champs:</strong></p>
<ul>
<li><b>country</b>: Codes pays (ISO2), séparés par des virgules &ndash; ex. <code>DE</code> ou <code>DE,AT,CH</code></li>
<li><b>minDays</b>: Période minimale de retour en jours</li>
<li><b>maxDays</b>: Période maximale de retour en jours (peut être identique à minDays)</li>
<li><b>category</b>: Type de règle de retour<br>
 &nbsp;&nbsp;&bull; <code>finite</code> &ndash; retour possible et limité dans le temps<br>
 &nbsp;&nbsp;&bull; <code>unlimited</code> &ndash; retour illimité dans le temps<br>
 &nbsp;&nbsp;&bull; <code>not_permitted</code> &ndash; retour non autorisé</li>
<li><b>feeType</b>: Qui prend en charge les frais de retour?<br>
 &nbsp;&nbsp;&bull; <code>free</code> &ndash; Le marchand prend en charge les frais (FreeReturn)<br>
 &nbsp;&nbsp;&bull; <code>buyer</code> &ndash; Le client prend en charge les frais<br>
 &nbsp;&nbsp;&bull; <code>seller</code> &ndash; Le marchand prend en charge les frais</li>
<li><b>method</b>: Méthode de retour<br>
 &nbsp;&nbsp;&bull; <code>mail</code> &ndash; Retour par courrier/envoi<br>
 &nbsp;&nbsp;&bull; <code>store</code> &ndash; Retour en magasin physique<br>
 &nbsp;&nbsp;&bull; <code>both</code> &ndash; Retour par courrier ou en magasin<br>
 &nbsp;&nbsp;&bull; <code>none</code> &ndash; Aucun retour possible</li>
</ul>
<p><strong>Exemples:</strong></p>
<ol>
<li>Allemagne, période de retour 14–30 jours, gratuit, par envoi:<br>
<code>DE|14|30|finite|free|mail</code></li>
<li>Autriche & Suisse, 14–30 jours, le client paie le retour:<br>
<code>AT,CH|14|30|finite|buyer|mail</code></li>
<li>Pas de retour pour des pays spécifiques:<br>
<code>US|0|0|not_permitted|buyer|none</code></li>
</ol>',

  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_TITLE' => 'Validité par défaut pour priceValidUntil (jours)',
  'MODULE_' . $modulname . '_PRICEVALID_DEFAULT_DAYS_DESC'  => 'S\'il n\'y a pas de date d\'expiration d\'un prix spécial disponible, <code>priceValidUntil</code> est défini à ce nombre de jours dans le futur. 0 = ne pas définir <code>priceValidUntil</code>.',

  'MODULE_' . $modulname . '_UPDATE_AVAILABLE_TITLE' => '<span style="font-weight:bold;color:#900;background:#ff6;border-radius:3px;padding:2px;border:1px solid #900;">Veuillez effectuer une mise à jour !</span>',
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
