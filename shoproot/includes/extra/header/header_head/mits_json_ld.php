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

if (!defined('MODULE_MITS_JSON_LD_STATUS') || MODULE_MITS_JSON_LD_STATUS !== 'true') {
    return;
}

require_once DIR_FS_INC . 'parse_multi_language_value.inc.php';

$mitsJsonLdGraph = [];

/**
 * @param string $const
 *
 * @return bool
 */
function mits_flag(string $const): bool
{
    return defined($const) && constant($const) === 'true';
}

/**
 * @param mixed $value
 *
 * @return mixed
 */
function mits_ml(mixed $value): mixed
{
    return parse_multi_language_value($value, $_SESSION['language_code']);
}

/**
 * @param array $node
 *
 * @return void
 */
function mits_graph_add(array $node): void
{
    global $mitsJsonLdGraph;

    if (!empty($node)) {
        $mitsJsonLdGraph[] = $node;
    }
}

/**
 * @return void
 */
function mits_graph_output(): void
{
    global $mitsJsonLdGraph;

    if (empty($mitsJsonLdGraph)) {
        return;
    }

    $payload = [
      '@context' => 'https://schema.org',
      '@graph'   => $mitsJsonLdGraph,
    ];

    echo '<script type="application/ld+json">'
      . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . '</script>';
}

/**
 * @param array $data
 *
 * @return void
 */
function mits_output_json(array $data): void
{
    echo '<script type="application/ld+json">'
      . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . '</script>';
}

/**
 * @param array $data
 *
 * @return array
 */
function mits_jsonld_clean(array $data): array
{
    foreach ($data as $key => $value) {
        if ($value === null || $value === '' || $value === []) {
            unset($data[$key]);
            continue;
        }

        if (is_array($value)) {
            $data[$key] = mits_jsonld_clean($value);

            if ($data[$key] === []) {
                unset($data[$key]);
            }
        }
    }

    return $data;
}

/**
 * @param mixed $text
 *
 * @return string|null
 */
function mits_jsonld_sanitize(mixed $text): ?string
{
    static $charset = null;
    static $replaceQuotes = null;

    if ($charset === null) {
        $charset = defined('CHARSET') ? CHARSET : 'UTF-8';
    }

    if ($text === null || $text === '') {
        return null;
    }

    if (is_array($text)) {
        $text = implode(', ', $text);
    }

    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, $charset);

    $text = str_replace("\u{00A0}", ' ', $text);

    $text = str_replace("\u{00AD}", '', $text);

    if ($replaceQuotes === null) {
        $replaceQuotes = [
          "\u{201C}" => '"',
          "\u{201D}" => '"',
          "\u{201E}" => '"',
          "\u{201F}" => '"',
          "\u{00AB}" => '"',
          "\u{00BB}" => '"',
          "\u{2018}" => "'",
          "\u{2019}" => "'",
        ];
    }

    $text = strtr($text, $replaceQuotes);

    $text = preg_replace('/[\x{2000}-\x{206F}]/u', ' ', $text);

    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);

    if (class_exists('Normalizer')) {
        $text = Normalizer::normalize($text, Normalizer::FORM_C);
    }

    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}


/**
 * @param string $file
 * @param string $params
 * @param string $ssl
 *
 * @return string
 */
function mits_link(string $file, string $params = '', string $ssl = 'NONSSL'): string
{
    return xtc_href_link($file, $params, $ssl, false);
}

/**
 * @param int|string $productId
 * @param bool $noSsl
 *
 * @return string
 */
function mits_product_url(int|string $productId, bool $noSsl = true): string
{
    return xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($productId),
      $noSsl ? 'NONSSL' : 'SSL',
      false
    );
}


/**
 * @return string|null
 */
function mits_get_logo(): ?string
{
    if (!mits_flag('MODULE_MITS_JSON_LD_SHOW_LOGO') || MODULE_MITS_JSON_LD_LOGOFILE === '') {
        return null;
    }

    $paths = [
      'templates/' . CURRENT_TEMPLATE . '/img/' . MODULE_MITS_JSON_LD_LOGOFILE,
      'images/' . MODULE_MITS_JSON_LD_LOGOFILE,
    ];

    foreach ($paths as $p) {
        if (is_file(DIR_FS_CATALOG . $p)) {
            return DIR_WS_BASE . $p;
        }
    }

    return null;
}

/**
 * @param int $productId
 *
 * @return array
 */
function mits_build_additional_properties(int $productId): array
{
    if (
      !defined('TABLE_PRODUCTS_TAGS')
      || !defined('TABLE_PRODUCTS_TAGS_OPTIONS')
      || !defined('TABLE_PRODUCTS_TAGS_VALUES')
      || !defined('ADD_TAGS_SELECT')
    ) {
        return [];
    }

    $sql = "
        SELECT " . ADD_TAGS_SELECT . "
               pto.options_id,
               pto.options_name,
               pto.options_description,
               pto.sort_order AS options_sort_order,
               pto.options_content_group,
               ptv.values_id,
               ptv.values_name,
               ptv.values_description,
               ptv.sort_order AS values_sort_order,
               ptv.values_image,
               ptv.values_content_group
          FROM " . TABLE_PRODUCTS_TAGS . " pt
          JOIN " . TABLE_PRODUCTS_TAGS_OPTIONS . " pto
               ON pt.options_id = pto.options_id
              AND pto.status = '1'
              AND pto.languages_id = '" . (int)$_SESSION['languages_id'] . "'
          JOIN " . TABLE_PRODUCTS_TAGS_VALUES . " ptv
               ON ptv.values_id = pt.values_id
              AND ptv.status = '1'
              AND ptv.languages_id = '" . (int)$_SESSION['languages_id'] . "'
         WHERE pt.products_id = '" . (int)$productId . "'
      ORDER BY pt.sort_order,
               pt.options_id,
               pto.sort_order,
               pto.options_name,
               ptv.sort_order,
               ptv.values_name
    ";

    $tagsQuery = xtDBquery($sql);

    if (xtc_db_num_rows($tagsQuery, true) <= 0) {
        return [];
    }

    $tagsContent = [];

    while ($row = xtc_db_fetch_array($tagsQuery, true)) {
        if (!isset($tagsContent[$row['options_id']])) {
            $tagsContent[$row['options_id']] = [
              'OPTIONS_NAME'        => $row['options_name'],
              'OPTIONS_ID'          => $row['options_id'],
              'OPTIONS_SORT_ORDER'  => $row['options_sort_order'],
              'OPTIONS_DESCRIPTION' => $row['options_description'],
              'DATA'                => [],
            ];
        }

        $tagsContent[$row['options_id']]['DATA'][] = [
          'VALUES_NAME'        => $row['values_name'],
          'VALUES_ID'          => $row['values_id'],
          'VALUES_SORT_ORDER'  => $row['values_sort_order'],
          'VALUES_DESCRIPTION' => $row['values_description'],
        ];
    }

    $additional = [];

    foreach ($tagsContent as $tagOption) {
        $value = null;

        if (count($tagOption['DATA']) > 1) {
            $value = [];

            foreach ($tagOption['DATA'] as $tagValue) {
                $value[] = mits_jsonld_sanitize($tagValue['VALUES_NAME']);
            }
        } elseif (!empty($tagOption['DATA'][0]['VALUES_NAME'])) {
            $value = mits_jsonld_sanitize($tagOption['DATA'][0]['VALUES_NAME']);
        }

        if ($value === null) {
            continue;
        }

        $entry = [
          '@type' => 'PropertyValue',
          'name'  => mits_jsonld_sanitize($tagOption['OPTIONS_NAME']),
          'value' => $value,
        ];

        if (!empty($tagOption['OPTIONS_DESCRIPTION'])) {
            $entry['description'] = mits_jsonld_sanitize($tagOption['OPTIONS_DESCRIPTION']);
        }

        $additional[] = $entry;
    }

    return $additional;
}

/**
 * @param object $product
 *
 * @return bool
 */
function mits_jsonld_attributes_enabled_for_product(object $product): bool
{
    $globalEnabled = defined('MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES')
      && MODULE_MITS_JSON_LD_ENABLE_ATTRIBUTES === 'true';

    if (!isset($product->data['mits_jsonld_attributes_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_attributes_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));

        if ($val === 'true' || $val === '1') {
            return true;
        }

        return false;
    }

    return (bool)$val;
}

/**
 * @param object $product
 *
 * @return bool
 */
function mits_jsonld_tags_enabled_for_product(object $product): bool
{
    $globalEnabled = defined('MODULE_MITS_JSON_LD_ENABLE_TAGS')
      && MODULE_MITS_JSON_LD_ENABLE_TAGS === 'true';

    if (!isset($product->data['mits_jsonld_tags_enabled'])) {
        return $globalEnabled;
    }

    $val = $product->data['mits_jsonld_tags_enabled'];

    if (is_string($val)) {
        $val = strtolower(trim($val));

        if ($val === 'true' || $val === '1') {
            return true;
        }

        return false;
    }

    return (bool)$val;
}

/**
 * @param float|int|string $price
 *
 * @return string
 */
function mits_jsonld_price_format(float|int|string $price): string
{
    $price = (string)$price;

    if (function_exists('bcadd')) {
        return bcadd($price, '0', 2);
    }

    return number_format((float)$price, 2, '.', '');
}

/**
 * @param object $product
 * @param array  $productDataArray
 *
 * @return array|mixed
 */
function mits_build_offers_for_product(object $product, array $productDataArray): mixed
{
    global $xtPrice;

    $maxOffers = 100;

    if (defined('MODULE_MITS_JSON_LD_MAX_OFFERS') && (int)MODULE_MITS_JSON_LD_MAX_OFFERS > 0) {
        $maxOffers = (int)MODULE_MITS_JSON_LD_MAX_OFFERS;
    }

    $useAttributes = mits_jsonld_attributes_enabled_for_product($product)
      && method_exists($product, 'getAttributesCount')
      && $product->getAttributesCount() > 0;

    $offers     = [];
    $offerIndex = 0;

    if ($useAttributes) {
        $attributesByOptionId = [];

        $attributesQuery = xtDBquery(
          "
            SELECT pa.*, pav.products_options_values_name
              FROM " . TABLE_PRODUCTS_ATTRIBUTES . " AS pa
              JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pav
                ON pav.products_options_values_id = pa.options_values_id
               AND pav.language_id = " . (int)$_SESSION['languages_id'] . "
             WHERE pa.products_id = " . (int)$product->data['products_id'] . "
             ORDER BY pa.options_id, pa.sortorder
        "
        );

        while ($row = xtc_db_fetch_array($attributesQuery, true)) {
            if (!isset($attributesByOptionId[$row['options_id']])) {
                $attributesByOptionId[$row['options_id']] = [];
            }

            $attributesByOptionId[$row['options_id']][] = $row;
        }

        if (!empty($attributesByOptionId)) {
            $optionGroups = array_values($attributesByOptionId);

            $buildCombinationOffers = static function (
              array $optionGroups,
              int $currentOptionIndex,
              array $currentCombination,
              array &$offers,
              int &$offerIndex,
              int $maxOffers,
              object $product,
              array $productDataArray
            ) use (&$buildCombinationOffers, $xtPrice): void {
                if ($offerIndex >= $maxOffers) {
                    return;
                }

                if ($currentOptionIndex >= count($optionGroups)) {
                    $offers[] = mits_build_single_variant_offer_from_combination(
                      $currentCombination,
                      $product,
                      $productDataArray,
                      $xtPrice
                    );
                    $offerIndex++;

                    return;
                }

                $currentGroup = $optionGroups[$currentOptionIndex];

                foreach ($currentGroup as $attributeRow) {
                    if ($offerIndex >= $maxOffers) {
                        break;
                    }

                    $nextCombination   = $currentCombination;
                    $nextCombination[] = $attributeRow;

                    $buildCombinationOffers(
                      $optionGroups,
                      $currentOptionIndex + 1,
                      $nextCombination,
                      $offers,
                      $offerIndex,
                      $maxOffers,
                      $product,
                      $productDataArray
                    );
                }
            };

            $buildCombinationOffers(
              $optionGroups,
              0,
              [],
              $offers,
              $offerIndex,
              $maxOffers,
              $product,
              $productDataArray
            );
        }
    }

    if (empty($offers)) {
        $offers[] = mits_build_single_base_offer_without_attributes($product, $productDataArray, $xtPrice);
    }

    if (count($offers) === 1) {
        return $offers[0];
    }

    $allPrices = [];

    foreach ($offers as $offer) {
        if (isset($offer['price'])) {
            $allPrices[] = $offer['price'];
        }
    }

    if (empty($allPrices)) {
        return $offers[0];
    }

    $currency = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');

    return [
      '@type'         => 'AggregateOffer',
      'priceCurrency' => $currency,
      'lowPrice'      => mits_jsonld_price_format(min($allPrices)),
      'highPrice'     => mits_jsonld_price_format(max($allPrices)),
      'offerCount'    => count($offers),
      'offers'        => $offers,
    ];
}

/**
 * @param array  $attributeCombination
 * @param object $product
 * @param array  $productDataArray
 * @param object $xtPrice
 *
 * @return array
 */
function mits_build_single_variant_offer_from_combination(
  array $attributeCombination,
  object $product,
  array $productDataArray,
  object $xtPrice
): array {
    $variantStockQuantity   = $productDataArray['PRODUCTS_QUANTITY'] ?? $product->data['products_quantity'];
    $variantBaseUnitQuantity = (float)($product->data['products_vpe_value'] ?? 0);
    $variantBasePrice        = (float)($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN']
      ?? $product->data['products_price']);

    $usedOptionValueIds = [];
    $optionValueNames   = [];
    $optionModelParts   = [];

    $variantEan     = $productDataArray['PRODUCTS_EAN'] ?? ($product->data['products_ean'] ?? '');
    $hasAttributeEan = false;

    foreach ($attributeCombination as $attributeRow) {
        $usedOptionValueIds[$attributeRow['options_id']] = $attributeRow['options_values_id'];

        if (!empty($attributeRow['products_options_values_name'])) {
            $optionValueNames[] = $attributeRow['products_options_values_name'];
        }

        if (!empty($attributeRow['attributes_model'])) {
            $optionModelParts[] = $attributeRow['attributes_model'];
        }

        if (isset($attributeRow['attributes_stock']) && $attributeRow['attributes_stock'] !== '') {
            $qty = (float)$attributeRow['attributes_stock'];

            if ($variantStockQuantity === null) {
                $variantStockQuantity = $qty;
            } else {
                $variantStockQuantity = min($variantStockQuantity, $qty);
            }
        }

        if (!empty($attributeRow['options_values_price'])) {
            $attrPrice = (float)$xtPrice->xtcFormat(
              $attributeRow['options_values_price'],
              false,
              $product->data['products_tax_class_id']
            );

            switch ($attributeRow['price_prefix']) {
                case '-':
                    $variantBasePrice -= $attrPrice;
                    break;

                case '=':
                    $variantBasePrice = $attrPrice;
                    break;

                default: // '+'
                    $variantBasePrice += $attrPrice;
                    break;
            }
        }

        if (!empty($attributeRow['attributes_ean'])) {
            $variantEan     = $attributeRow['attributes_ean'];
            $hasAttributeEan = true;
        }

        if (!empty($attributeRow['attributes_vpe_value'])) {
            $attrVpe = (float)$attributeRow['attributes_vpe_value'];

            switch ($attributeRow['weight_prefix']) {
                case '-':
                    $variantBaseUnitQuantity -= $attrVpe;
                    break;

                case '=':
                    $variantBaseUnitQuantity = $attrVpe;
                    break;

                default:
                    $variantBaseUnitQuantity += $attrVpe;
                    break;
            }
        }
    }

    if (
      isset($product->data['products_discount'])
      && (float)$product->data['products_discount'] !== 0.0
      && !empty($_SESSION['customers_status']['customers_status_discount_attributes'])
    ) {
        $variantBasePrice *= (100 - (float)$product->data['products_discount']) / 100;
    }

    if ($variantStockQuantity === null) {
        $variantStockQuantity = $product->data['products_quantity'];
    }

    if (count($attributeCombination) === 1 && !$hasAttributeEan) {
        $variantStockQuantity = $product->data['products_quantity'];
    }

    if ($variantStockQuantity < 0) {
        $variantStockQuantity = 0;
    }

    $availability = ($variantStockQuantity <= 0 && mits_flag('STOCK_CHECK'))
      ? 'https://schema.org/OutOfStock'
      : 'https://schema.org/InStock';

    $productIdForLink = $product->data['products_id'];

    if (!empty($usedOptionValueIds)) {
        $attributeSignatureParts = [];

        foreach ($usedOptionValueIds as $optionId => $valueId) {
            $attributeSignatureParts[] = '{' . $optionId . '}' . $valueId;
        }

        if (!empty($attributeSignatureParts)) {
            $productIdForLink .= implode('', $attributeSignatureParts);
        }
    }

    $offerName = $productDataArray['PRODUCTS_NAME'] ?? $product->data['products_name'];

    if (!empty($optionValueNames)) {
        $offerName .= ' ' . implode(' ', $optionValueNames);
    }

    $offerSku = $productDataArray['PRODUCTS_MODEL'] ?? $product->data['products_model'];

    if (!empty($optionModelParts)) {
        $offerSku .= '-' . implode('-', $optionModelParts);
    }

    $offerUrl = xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($productIdForLink),
      'NONSSL',
      false,
      empty($usedOptionValueIds)
    );

    $currencyCode = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');
    $finalPrice   = mits_jsonld_price_format($variantBasePrice);

    $priceSpecification = [[
                             '@type'                 => 'UnitPriceSpecification',
                             'price'                 => $finalPrice,
                             'priceCurrency'         => $currencyCode,
                             'valueAddedTaxIncluded' => true,
                             'priceType'             => 'https://schema.org/RegularPrice',
                           ]];

    $eligibleQuantity = null;

    if (
      !empty($product->data['products_vpe_status'])
      && $variantBaseUnitQuantity > 0
      && $finalPrice > 0
    ) {
        $baseUnitName = mits_jsonld_sanitize(xtc_get_vpe_name($product->data['products_vpe']));

        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $finalPrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'referenceQuantity'     => [
            '@type'    => 'QuantitativeValue',
            'value'    => 1,
            'unitText' => $baseUnitName,
          ],
          'description'           => $xtPrice->xtcFormat(
              $finalPrice * (1 / $variantBaseUnitQuantity),
              true
            ) . TXT_PER . $baseUnitName,
        ];

        $eligibleQuantity = [
          '@type'    => 'QuantitativeValue',
          'value'    => $variantBaseUnitQuantity,
          'unitText' => $baseUnitName,
        ];
    }

    $sellerName = defined('META_COMPANY') ? META_COMPANY : STORE_NAME;

    $offer = [
      '@type'              => 'Offer',
      '@id'                => $offerUrl . '#offer-' . strtolower(preg_replace('/[^a-z0-9]+/', '-', $offerSku)),
      'name'               => mits_jsonld_sanitize($offerName),
      'sku'                => $offerSku,
      'url'                => $offerUrl,
      'priceCurrency'      => $currencyCode,
      'price'              => $finalPrice,
      'itemCondition'      => 'https://schema.org/NewCondition',
      'availability'       => $availability,
      'seller'             => [
        '@type' => 'Organization',
        'name'  => mits_jsonld_sanitize($sellerName),
      ],
      'priceSpecification' => $priceSpecification,
    ];

    if (!empty($variantEan)) {
        $offer['gtin'] = $variantEan;
    }

    if ($eligibleQuantity !== null) {
        $offer['eligibleQuantity'] = $eligibleQuantity;
    }

    return $offer;
}

/**
 * @param object $product
 * @param array  $productDataArray
 * @param object $xtPrice
 *
 * @return array
 */
function mits_build_single_base_offer_without_attributes(
  object $product,
  array $productDataArray,
  object $xtPrice
): array {
    $currencyCode  = $_SESSION['currency'] ?? ($xtPrice->actualCurr ?? 'EUR');
    $basePricePlain = (float)($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_PLAIN']
      ?? $product->data['products_price']);
    $basePrice      = mits_jsonld_price_format($basePricePlain);

    $priceSpecification = [];

    if (
      !empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'])
      && stripos($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_FLAG'], 'special') !== false
    ) {
        $oldPrice           = mits_jsonld_price_format(
          $productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_OLD_PRICE_PLAIN']
        );
        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $oldPrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/StrikethroughPrice',
        ];

        $currentSpec = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/RegularPrice',
        ];

        if (!empty($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE'])) {
            $currentSpec['validThrough'] = date(
              'Y-m-d',
              strtotime($productDataArray['PRODUCTS_PRICE_ARRAY'][0]['PRODUCTS_PRICE_EXPIRES_DATE'])
            );
        }

        $priceSpecification[] = $currentSpec;
    } else {
        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'priceType'             => 'https://schema.org/RegularPrice',
        ];
    }

    $eligibleQuantity = null;

    if (
      !empty($product->data['products_vpe_status'])
      && !empty($product->data['products_vpe_value'])
      && $product->data['products_vpe_value'] != 0.0
      && $basePricePlain > 0
    ) {
        $baseUnitName = mits_jsonld_sanitize(xtc_get_vpe_name($product->data['products_vpe']));

        $priceSpecification[] = [
          '@type'                 => 'UnitPriceSpecification',
          'price'                 => $basePrice,
          'priceCurrency'         => $currencyCode,
          'valueAddedTaxIncluded' => true,
          'referenceQuantity'     => [
            '@type'    => 'QuantitativeValue',
            'value'    => 1,
            'unitText' => $baseUnitName,
          ],
          'description'           => $xtPrice->xtcFormat(
              $basePricePlain * (1 / $product->data['products_vpe_value']),
              true
            ) . TXT_PER . $baseUnitName,
        ];

        $eligibleQuantity = [
          '@type'    => 'QuantitativeValue',
          'value'    => $product->data['products_vpe_value'],
          'unitText' => $baseUnitName,
        ];
    }

    $availability = ($productDataArray['PRODUCTS_QUANTITY'] <= 0 && mits_flag('STOCK_CHECK'))
      ? 'https://schema.org/OutOfStock'
      : 'https://schema.org/InStock';

    $sellerName = defined('META_COMPANY') ? META_COMPANY : STORE_NAME;

    $offerUrl = xtc_href_link(
      FILENAME_PRODUCT_INFO,
      xtc_product_link($product->data['products_id']),
      'NONSSL',
      false
    );

    $offer = [
      '@type'              => 'Offer',
      '@id'                => $offerUrl . '#offer',
      'sku'                => $productDataArray['PRODUCTS_MODEL'] ?? $product->data['products_model'],
      'url'                => $offerUrl,
      'priceCurrency'      => $currencyCode,
      'price'              => $basePrice,
      'itemCondition'      => 'https://schema.org/NewCondition',
      'availability'       => $availability,
      'seller'             => [
        '@type' => 'Organization',
        'name'  => mits_jsonld_sanitize($sellerName),
      ],
      'priceSpecification' => $priceSpecification,
    ];

    if (!empty($productDataArray['PRODUCTS_MANUFACTURERS_MODEL'])) {
        $offer['mpn'] = $productDataArray['PRODUCTS_MANUFACTURERS_MODEL'];
    }

    if (!empty($productDataArray['PRODUCTS_EAN'])) {
        $offer['gtin'] = $productDataArray['PRODUCTS_EAN'];
    }

    if ($eligibleQuantity !== null) {
        $offer['eligibleQuantity'] = $eligibleQuantity;
    }

    return $offer;
}

$logo = mits_get_logo();

/**
 * WebSite
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_WEBSITE')) {
    $webSite = [
      '@type'         => 'WebSite',
      'name'          => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_NAME)),
      'alternateName' => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_ALTERNATE_NAME)),
      'description'   => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_DESCRIPTION)),
      'url'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
    ];

    if ($logo) {
        $webSite['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_SEARCHFIELD')) {
        $webSite['potentialAction'] = [
          '@type'       => 'SearchAction',
          'target'      => xtc_href_link(
              FILENAME_ADVANCED_SEARCH_RESULT,
              '',
              $request_type,
              false
            ) . '?keywords={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ];
    }

    $webSite = mits_jsonld_clean($webSite);
    mits_graph_add($webSite);
}

/**
 * Organization
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_ORGANISTATION')) {
    global $lng;

    $org = [
      '@type'         => 'Organization',
      '@id'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'name'          => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_NAME)),
      'alternateName' => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_ALTERNATE_NAME)),
      'description'   => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_DESCRIPTION)),
      'url'           => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
    ];

    if ($logo) {
        $org['logo']  = $logo;
        $org['image'] = $logo;
    }

    if (mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT') && isset($lng->catalog_languages)) {
        $langs    = array_map(static fn ($x) => $x['name'], $lng->catalog_languages);
        $contacts = [];

        $map = [
          'MODULE_MITS_JSON_LD_TELEPHONE_SERVICE'   => 'customer service',
          'MODULE_MITS_JSON_LD_TELEPHONE_TECHNICAL' => 'technical support',
          'MODULE_MITS_JSON_LD_TELEPHONE_BILLING'   => 'billing support',
          'MODULE_MITS_JSON_LD_TELEPHONE_SALES'     => 'sales',
        ];

        foreach ($map as $const => $type) {
            if (defined($const) && !empty(constant($const))) {
                $contacts[] = [
                  '@type'             => 'ContactPoint',
                  'telephone'         => mits_jsonld_sanitize(mits_ml(constant($const))),
                  'contactType'       => $type,
                  'areaServed'        => strtoupper($_SESSION['language_code']),
                  'availableLanguage' => $langs,
                ];
            }
        }

        if (defined('MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT') && !empty(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT)) {
            $contacts[] = [
              '@type'       => 'ContactPoint',
              'telephone'   => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT)),
              'contactType' => 'customer service',
            ];
        }

        if (!empty($contacts)) {
            $org['contactPoint'] = $contacts;
        }
    }

    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $org['sameAs'] = preg_split('/\s*,\s*/', MODULE_MITS_JSON_LD_SOCIAL_MEDIA);
    }

    $org = mits_jsonld_clean($org);
    mits_graph_add($org);
}

/**
 * LocalBusiness
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_LOCATION')) {
    $loc = [
      '@type'     => 'LocalBusiness',
      'name'      => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_NAME)),
      '@id'       => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'url'       => xtc_href_link(FILENAME_DEFAULT, '', $request_type, false),
      'telephone' => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_TELEPHONE_DEFAULT)),
      'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_STREETADDRESS)),
        'addressLocality' => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_ADDRESSLOCALITY)),
        'postalCode'      => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_POSTALCODE)),
        'addressCountry'  => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_ADDRESSCOUNTRY)),
      ],
    ];

    if ($logo) {
        $loc['image'] = $logo;
    }

    if (!empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE) && !empty(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE)) {
        $loc['geo'] = [
          '@type'     => 'GeoCoordinates',
          'latitude'  => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_GEO_LATITUDE)),
          'longitude' => mits_jsonld_sanitize(mits_ml(MODULE_MITS_JSON_LD_LOCATION_GEO_LONGITUDE)),
        ];
    }

    if (defined('MODULE_MITS_JSON_LD_SOCIAL_MEDIA') && !empty(MODULE_MITS_JSON_LD_SOCIAL_MEDIA)) {
        $loc['sameAs'] = array_map('trim', explode(',', MODULE_MITS_JSON_LD_SOCIAL_MEDIA));
    }

    $loc = mits_jsonld_clean($loc);
    mits_graph_add($loc);
}

/**
 * Breadcrumb
 */
if (mits_flag('MODULE_MITS_JSON_LD_SHOW_BREADCRUMB') && isset($breadcrumb)) {
    $items = [];

    foreach ($breadcrumb->_trail as $i => $bc) {
        $items[] = [
          '@type'    => 'ListItem',
          'position' => $i + 1,
          'name'     => mits_jsonld_sanitize($bc['title']),
          'item'     => $bc['link'] ?: mits_link(basename($PHP_SELF), xtc_get_all_get_params([], true)),
        ];
    }

    mits_graph_add([
      '@type'           => 'BreadcrumbList',
      'itemListElement' => $items,
    ]);
}

/**
 * ContactPage (Kontaktseite)
 */
if (
  basename($PHP_SELF) === FILENAME_CONTENT
  && isset($_GET['coID'])
  && (int)$_GET['coID'] === 7
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_CONTACT')
  && isset($shop_content_data['content_title'])
) {
    $metaDescr = isset($metadata_array['description']) ? $metadata_array['description'] : '';

    $schema = [
      '@type'       => 'ContactPage',
      'url'         => xtc_href_link(FILENAME_CONTENT, 'coID=7', 'SSL', false),
      'name'        => mits_jsonld_sanitize($shop_content_data['content_title']),
      'description' => mits_jsonld_sanitize(decode_htmlentities($metaDescr)),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

/**
 * Produktseite: Product + Offers + Reviews + Tags
 */
if (
  basename($PHP_SELF) === FILENAME_PRODUCT_INFO
  && isset($product)
  && is_object($product)
  && $product->isProduct()
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT')
) {
    global $manufacturer, $productDataArray, $xtPrice;

    $productImages = [];

    if (!empty($product->data['products_image'])) {
        $productImages[] = $product->productImage($product->data['products_image'], 'info');

        $mo = xtc_get_products_mo_images($product->data['products_id']);

        if ($mo) {
            foreach ($mo as $m) {
                $productImages[] = $product->productImage($m['image_name'], 'info');
            }
        }
    }

    $productURL = mits_product_url($product->data['products_id']);

    $schema = [
      '@type'            => 'Product',
      '@id'              => $productURL,
      'url'              => $productURL,
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $productURL,
      ],
      'productID'        => $product->data['products_id'],
      'sku'              => mits_jsonld_sanitize(
        $product->data['products_model'] ?: $product->data['products_id']
      ),
      'gtin13'           => mits_jsonld_sanitize($product->data['products_ean']),
      'mpn'              => mits_jsonld_sanitize($product->data['products_manufacturers_model']),
      'image'            => $productImages,
      'name'             => mits_jsonld_sanitize($product->data['products_name']),
      'description'      => mits_jsonld_sanitize($product->data['products_description']),
    ];

    if (isset($manufacturer['manufacturers_name']) && $manufacturer['manufacturers_name'] !== '') {
        $schema['brand'] = [
          '@type' => 'Brand',
          'name'  => mits_jsonld_sanitize($manufacturer['manufacturers_name']),
        ];
    }

    if (mits_jsonld_tags_enabled_for_product($product)) {
        $additional = mits_build_additional_properties($product->data['products_id']);

        if (!empty($additional)) {
            $schema['additionalProperty'] = $additional;
        }
    }

    $reviewsCount = $product->getReviewsCount($product->data['products_id']);

    if (
      mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS')
      && $_SESSION['customers_status']['customers_status_read_reviews'] === '1'
      && $reviewsCount > 0
    ) {
        $schema['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => $product->getReviewsAverage(),
          'reviewCount' => $reviewsCount,
        ];

        $reviewList = $product->getReviews($product->data['products_id']);

        if ($reviewList) {
            foreach ($reviewList as $r) {
                $date = DateTime::createFromFormat('d.m.Y', $r['DATE']);

                $schema['review'][] = [
                  '@type'         => 'Review',
                  'author'        => [
                    '@type' => 'Person',
                    'name'  => mits_jsonld_sanitize($r['AUTHOR']),
                  ],
                  'datePublished' => $date ? $date->format('Y-m-d') : '',
                  'reviewBody'    => mits_jsonld_sanitize($r['TEXT']),
                  'reviewRating'  => [
                    '@type'       => 'Rating',
                    'ratingValue' => $r['RATING_VOTE'],
                    'worstRating' => '1',
                    'bestRating'  => '5',
                  ],
                ];
            }
        }
    }

    if (
      defined('MODULE_TS_TRUSTEDSHOPS_ID')
      && defined('MODULE_TS_PRODUCT_STICKER_STATUS')
      && MODULE_TS_PRODUCT_STICKER_STATUS === '1'
      && defined('MODULE_TS_PRODUCT_STICKER')
      && MODULE_TS_PRODUCT_STICKER !== ''
      && !empty($productDataArray['TS_FEED_REVIEW'])
      && $productDataArray['TS_FEED_REVIEW'] > 0
    ) {
        $schema['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => $productDataArray['TS_FEED_AGGREGATERATING'],
          'reviewCount' => $productDataArray['TS_FEED_REVIEW'],
        ];
    }

    $offersBlock      = mits_build_offers_for_product($product, $productDataArray);
    $schema['offers'] = $offersBlock;

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

/**
 * Einzelne Bewertungsseite (Produktbewertung)
 */
if (
  basename($PHP_SELF) === FILENAME_PRODUCT_REVIEWS_INFO
  && mits_flag('MODULE_MITS_JSON_LD_SHOW_PRODUCT_REVIEWS_INFO')
  && isset($reviews)
  && is_array($reviews)
  && count($reviews) > 0
  && isset($product)
  && is_object($product)
) {
    $r     = $reviews;
    $count = $product->getReviewsCount($r['products_id']);
    $date  = DateTime::createFromFormat('d.m.Y', $r['date_added']);

    $schema = [
      '@type'        => 'Review',
      'reviewRating' => [
        '@type'       => 'AggregateRating',
        'ratingValue' => $r['reviews_rating'],
        'ratingCount' => $count,
        'reviewCount' => $count,
        'worstRating' => '1',
        'bestRating'  => '5',
      ],
      'itemReviewed' => [
        '@type' => 'Product',
        'name'  => mits_jsonld_sanitize($r['products_name']),
      ],
      'author'       => [
        '@type' => 'Person',
        'name'  => mits_jsonld_sanitize($r['customers_name']),
      ],
      'datePublished' => $date ? $date->format('Y-m-d') : $r['date_added'],
      'reviewBody'    => mits_jsonld_sanitize($r['reviews_text']),
    ];

    $schema = mits_jsonld_clean($schema);
    mits_graph_add($schema);
}

mits_graph_output();
