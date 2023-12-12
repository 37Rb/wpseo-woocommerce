[![Coverage Status](https://coveralls.io/repos/github/Yoast/wpseo-woocommerce/badge.svg?branch=trunk)](https://coveralls.io/github/Yoast/wpseo-woocommerce?branch=trunk)

WooCommerce Yoast SEO
=====================
Requires at least: 6.2
Tested up to: 6.4
Stable tag: 16.0
Requires PHP: 7.2.5
Depends: Yoast SEO, WooCommerce

Description
-----------

This extension to WooCommerce and Yoast SEO makes sure there's perfect communication between the two plugins.

This repository uses [the Yoast grunt tasks plugin](https://github.com/Yoast/plugin-grunt-tasks).

Installation
------------

1. Go to Plugins -> Add New.
2. Click "Upload" right underneath "Install Plugins".
3. Upload the zip file that this readme was contained in.
4. Activate the plugin.
5. Go to SEO -> Licenses and enter your WooCommerce SEO license key.
6. Save settings, your license key will be validated. If all is well, you should now see the WooCommerce SEO settings.

Frequently Asked Questions
--------------------------

You can find the FAQ [online here](https://kb.yoast.com/kb/category/woocommerce-seo/).

Changelog
=========

## 16.0

Release date: 2023-12-12

The last release of WooCommerce SEO received generative AI support for product titles and meta descriptions. In WooCommerce SEO 16.0, we added another great feature: easy import and export of global identifier values like GTIN8 and UPC. Check out this great timesaver! Find out more about what's new in Yoast WooCommerce SEO 16.0 in [our release post](https://yoa.st/release-12-12-23)!

#### Enhancements

* Adds support for global identifier values (GTIN8, GTIN12 / UPC, GTIN13 / EAN, GTIN14 / ITF-14, ISBN, MPN) into the Import/Export feature of the WooCommerce Product.

#### Other

* Bumps the minimum required version of Yoast SEO to 21.7.
* Adds checks to ensure that the plugin has no known incompatibilities with PHP 8.3.
* Improves the discoverability of the security policy.

## 15.9

Release date: 2023-11-28

Give your store the edge with our AI-enhanced WooCommerce SEO tools! Rapidly generate standout product titles and descriptions that capture attention and convert. This AI update -- for which you need Yoast SEO Premium -- integrates seamlessly with WooCommerce, making your products irresistible to search engines and shoppers. Revamp your product SEO and watch your sales grow! Find out more about what's new in Yoast WooCommerce SEO 15.9 in [our release post](https://yoa.st/release-28-11-23)!

#### Enhancements

* Introducing a new feature: AI SEO title and meta description generation for WooCommerce products! To unlock this SEO magic, make sure you have both Yoast SEO Premium and Yoast WooCommerce SEO installed.
* This PR introduces a new way of retrieving translations for Yoast SEO for WooCommerce, by utilizing the TranslationPress service. Instead of having to ship all translations with every release, we can now load the translations on a per-install basis, tailored to the user's setup. This means smaller plugin releases and less bloat on the user's server.

#### Other

* Bumps the minimum required version of Yoast SEO to 21.6.
* Sets the minimum supported WordPress version to 6.2.
* Sets the WordPress tested up to version to 6.4.
* Users requiring this package via [WP]Packagist can now use the `composer/installers` v2.

### Earlier versions
For the changelog of earlier versions, please refer to [the changelog on yoast.com](https://yoa.st/woo-seo-changelog).
