<?php
/**
 * --------------------------------------------------------------
 * File: mitsGoogleMerchant.php
 * Date: 13.11.2025
 * Time: 17:36
 *
 * Author: Hetfield
 * Copyright: (c) 2025 - MerZ IT-SerVice
 * Web: https://www.merz-it-service.de
 * Contact: info@merz-it-service.de
 * --------------------------------------------------------------
 */

/**
 * Google Merchant Feed JSON generator
 * Integrates cleanly with the refactored MITS JSON‑LD module
 *
 * Generates a fully compliant Google Merchant "Products" JSON feed.
 * Inspired by the JSON-LD structure, but formatted as a feed array.
 *
 * HOW TO USE:
 * 1. Include this file somewhere in admin or cron environment
 * 2. Call MitsMerchantFeed::generateFeed()
 * 3. Print or store the returned JSON
 */
class MitsMerchantFeed
{
    /**
     * Generate the complete JSON feed
     */
    public static function generateFeed(): string
    {
        $products = self::loadAllProducts();
        $feed = [];

        foreach ($products as $p) {
            $feed[] = self::mapProductToMerchantFormat($p);
        }

        return json_encode(["products" => $feed], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Load all active products in one query
     */
    private static function loadAllProducts(): array
    {
        $sql = "SELECT p.*, pd.products_name, pd.products_description, pd.products_short_description,
                       m.manufacturers_name,
                       (SELECT products_image FROM products_images WHERE products_id = p.products_id LIMIT 1) AS main_image
                FROM products p
                JOIN products_description pd ON pd.products_id = p.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                LEFT JOIN manufacturers m ON m.manufacturers_id = p.manufacturers_id
                WHERE p.products_status = 1";

        $query = xtDBquery($sql);
        $list = [];
        while ($r = xtc_db_fetch_array($query, true)) {
            $list[] = $r;
        }
        return $list;
    }

    /**
     * Map one product into Google Merchant JSON structure
     */
    private static function mapProductToMerchantFormat(array $p): array
    {
        $pid = (int)$p['products_id'];

        // Price
        global $xtPrice;
        $priceInfo = $xtPrice->xtcGetPrice($pid, true, 1, $p['products_tax_class_id']);

        // Images
        $images = [];
        if (!empty($p['main_image'])) {
            $images[] = xtc_href_link(DIR_WS_IMAGES . $p['main_image'], '', 'NONSSL', false);
        }

        $mo = xtc_get_products_mo_images($pid);
        if ($mo) {
            foreach ($mo as $m) {
                $images[] = xtc_href_link(DIR_WS_IMAGES . $m['image_name'], '', 'NONSSL', false);
            }
        }

        // Categories
        $categories = self::getCategoriesForProduct($pid);

        // Attributes / Variants
        $variants = self::buildVariantOffers($p);

        $entry = [
          "id"                      => (string)$pid,
          "title"                   => html_entity_decode($p['products_name'], ENT_QUOTES, 'UTF-8'),
          "description"             => strip_tags($p['products_description']),
          "link"                    => self::buildProductUrl($p),
          "image_link"              => $images[0] ?? null,
          "additional_image_link"   => array_slice($images, 1),
          "availability"            => $p['products_quantity'] > 0 ? "in stock" : "out of stock",
          "condition"               => "new",
          "brand"                   => $p['manufacturers_name'] ?? META_COMPANY,
          "gtin"                    => $p['products_ean'] ?: null,
          "mpn"                     => $p['products_manufacturers_model'] ?: $p['products_model'],
          "price"                   => number_format($priceInfo['plain'], 2, '.', '') . ' ' . $xtPrice->actualCurr,
          "product_type"            => implode(" > ", $categories),
          "google_product_category" => null, // optional mapping layer
        ];

        // Variant support
        if (!empty($variants)) {
            $entry["variants"] = $variants;
        }

        return $entry;
    }

    /**
     * Build SEO URL for product
     */
    private static function buildProductUrl(array $p): string
    {
        return xtc_href_link(
          FILENAME_PRODUCT_INFO,
          xtc_product_link($p['products_id'], $p['products_name']),
          'NONSSL',
          false
        );
    }

    /**
     * Build variant offer array (close to JSON-LD module)
     */
    private static function buildVariantOffers(array $p): array
    {
        $pid = (int)$p['products_id'];
        $attrData = self::loadAttributesForProduct($pid);
        if (empty($attrData)) {
            return [];
        }

        $grouped = [];
        foreach ($attrData as $a) {
            $grouped[$a['options_id']][] = $a;
        }

        $combinations = self::cartesian(array_values($grouped));
        $variants = [];

        global $xtPrice;

        foreach ($combinations as $combo) {
            $nameParts = [];
            $modelParts = [];
            $stock = PHP_INT_MAX;
            $ean = $p['products_ean'];

            $price = $p['products_price'];
            $pdif = 0;

            foreach ($combo as $attr) {
                $nameParts[] = $attr['products_options_values_name'];

                if (!empty($attr['attributes_model'])) {
                    $modelParts[] = $attr['attributes_model'];
                }

                if ($attr['attributes_stock'] < $stock) {
                    $stock = $attr['attributes_stock'];
                }

                // Price prefix logic
                if (!empty($attr['options_values_price'])) {
                    $val = $xtPrice->xtcFormat($attr['options_values_price'], false, $p['products_tax_class_id']);
                    switch ($attr['price_prefix']) {
                        case '-':
                            $pdif += $val;
                            break;
                        case '=':
                            $price = $val;
                            break;
                        default:
                            $pdif -= $val;
                    }
                }

                if (!empty($attr['attributes_ean'])) {
                    $ean = $attr['attributes_ean'];
                }
            }

            // apply difference
            $price -= $pdif;

            $variants[] = [
              "variant_title" => implode(" / ", $nameParts),
              "sku"           => $p['products_model'] . (empty($modelParts) ? '' : '-' . implode('-', $modelParts)),
              "availability"  => $stock > 0 ? "in stock" : "out of stock",
              "price"         => number_format($price, 2, '.', '') . ' EUR',
              "gtin"          => $ean,
              "link"          => self::buildVariantUrl($p, $combo)
            ];
        }

        return $variants;
    }

    /**
     * Load one-dimensional attribute list
     */
    private static function loadAttributesForProduct(int $pid): array
    {
        $sql = "SELECT pa.*, pav.products_options_values_name
                FROM products_attributes pa
                JOIN products_options_values pav
                     ON pav.products_options_values_id = pa.options_values_id
                     AND pav.language_id = " . (int)$_SESSION['languages_id'] . "
                WHERE pa.products_id = $pid
                ORDER BY pa.options_id, pa.sortorder";

        $q = xtDBquery($sql);
        $list = [];
        while ($r = xtc_db_fetch_array($q, true)) {
            $list[] = $r;
        }
        return $list;
    }

    /**
     * Cartesian combination generator
     */
    private static function cartesian(array $groups): array
    {
        $result = [[]];
        foreach ($groups as $group) {
            $new = [];
            foreach ($result as $old) {
                foreach ($group as $element) {
                    $new[] = array_merge($old, [$element]);
                }
            }
            $result = $new;
        }
        return $result;
    }

    /**
     * Build variant-specific URL
     */
    private static function buildVariantUrl(array $p, array $combo): string
    {
        $pid = $p['products_id'];
        $q = [];
        foreach ($combo as $a) {
            $q['{' . $a['options_id'] . '}'] = $a['options_values_id'];
        }
        $hash = implode('', array_map(fn($k, $v) => "$k$v", array_keys($q), $q));

        return xtc_href_link(
          FILENAME_PRODUCT_INFO,
          xtc_product_link($pid) . $hash,
          'NONSSL',
          false
        );
    }

    /**
     * Read category path
     */
    private static function getCategoriesForProduct(int $pid): array
    {
        $sql = "SELECT cd.categories_name, c.parent_id, c.categories_id
                FROM products_to_categories pc
                JOIN categories c ON c.categories_id = pc.categories_id
                JOIN categories_description cd
                     ON cd.categories_id = c.categories_id
                    AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                WHERE pc.products_id = $pid";

        $q = xtDBquery($sql);
        $row = xtc_db_fetch_array($q, true);
        if (!$row) {
            return [];
        }

        $path = [$row['categories_name']];
        $parent = $row['parent_id'];

        while ($parent > 0) {
            $q2 = xtDBquery(
              "SELECT c.parent_id, cd.categories_name
                               FROM categories c
                               JOIN categories_description cd
                                 ON c.categories_id = cd.categories_id
                                AND cd.language_id = " . (int)$_SESSION['languages_id'] . "
                              WHERE c.categories_id = $parent"
            );
            $r2 = xtc_db_fetch_array($q2, true);
            if (!$r2) {
                break;
            }
            array_unshift($path, $r2['categories_name']);
            $parent = $r2['parent_id'];
        }

        return $path;
    }
}

// Example usage:
// echo MitsMerchantFeed::generateFeed();
