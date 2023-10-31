[![Coverage Status](https://coveralls.io/repos/github/Yoast/wpseo-woocommerce/badge.svg?branch=trunk)](https://coveralls.io/github/Yoast/wpseo-woocommerce?branch=trunk)

WooCommerce Yoast SEO
=====================
Requires at least: 6.2
Tested up to: 6.4
Stable tag: 15.8
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

## 15.9

Release date: 2023-11-14

#### Enhancements

* This PR introduces a new way of retrieving translations for Yoast SEO for WooCommerce, by utilizing the TranslationPress service. Instead of having to ship all translations with every release, we can now load the translations on a per-install basis, tailored to the user's setup. This means smaller plugin releases and less bloat on the user's server.

#### Other

* Bumps the minimum required version of Yoast SEO to 21.6.
* Sets the minimum supported WordPress version to 6.2.
* Sets the WordPress tested up to version to 6.3.
* Sets the WordPress tested up to version to 6.4.
* Users requiring this package via [WP]Packagist can now use the `composer/installers` v2.

## 15.8

Release date: 2023-07-25

#### Enhancements

* Adds an edit button to the SKU and Product identifier assessments on product pages if the Yoast SEO Premium plugin is installed (version 20.12 or higher).
* Ensures compatibility of the breadcrumbs replacement feature with WooCommerce 7.9.0 when using blockified template for single products.

#### Bugfixes

* Fixes a bug where the Yoast SEO metabox would not load when the short description metabox had been deactivated.

#### Other

* Bumps the minimum required Yoast SEO version to 20.12.
* Sets the minimum supported WooCommerce version to 7.1.
* Sets the minimum supported WordPress version to 6.1.

### Earlier versions
For the changelog of earlier versions, please refer to [the changelog on yoast.com](https://yoa.st/woo-seo-changelog).
