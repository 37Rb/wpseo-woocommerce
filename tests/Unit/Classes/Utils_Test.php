<?php

namespace Yoast\WP\Woocommerce\Tests\Unit\Classes;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use WPSEO_WooCommerce_Utils;
use Yoast\WP\Woocommerce\Tests\Unit\TestCase;

/**
 * Class Utils_Test.
 */
class Utils_Test extends TestCase {

	/**
	 * Test retrieving a primary term.
	 *
	 * @covers WPSEO_WooCommerce_Utils::search_primary_term
	 */
	public function test_search_primary_term() {
		$product_id = 123;

		$product = Mockery::mock( 'WC_Product' )->makePartial();
		$product->expects( 'get_id' )->once()->andReturn( $product_id );

		$primary_term_mock = Mockery::mock( 'overload:WPSEO_Primary_Term' );
		$primary_term_mock->expects( '__construct' )->once()->with( 'brand', $product_id )->andReturnSelf();
		$primary_term_mock->expects( 'get_primary_term' )->once()->with()->andReturn( 12345 );

		Functions\stubs(
			[
				'get_term_by' => (object) [ 'name' => 'Apple' ],
			]
		);

		$this->assertSame( 'Apple', WPSEO_WooCommerce_Utils::search_primary_term( [ 'brand' ], $product ) );
	}

	/**
	 * Test retrieving a primary term.
	 *
	 * @covers WPSEO_WooCommerce_Utils::search_primary_term
	 */
	public function test_search_primary_term_find_none() {
		$product_id = 123;

		$product = Mockery::mock( 'WC_Product' )->makePartial();
		$product->expects( 'get_id' )->once()->andReturn( $product_id );

		$primary_term_mock = Mockery::mock( 'overload:WPSEO_Primary_Term' );
		$primary_term_mock->expects( '__construct' )->once()->with( 'brand', $product_id )->andReturnSelf();
		$primary_term_mock->expects( 'get_primary_term' )->once()->with()->andReturn( false );

		$this->assertSame( '', WPSEO_WooCommerce_Utils::search_primary_term( [ 'brand' ], $product ) );
	}

	/**
	 * Test getting the product display price
	 *
	 * @covers WPSEO_WooCommerce_Utils::get_product_display_price
	 * @covers WPSEO_WooCommerce_Utils::prices_should_include_tax
	 */
	public function test_get_product_display_price() {
		$price    = 10;
		$tax_rate = 1.1;

		$product = Mockery::mock( 'WC_Product' )->makePartial();
		$product->expects( 'get_price' )->once()->andReturn( $price );
		$product->expects( 'get_min_purchase_quantity' )->once()->andReturn( 1 );

		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'woocommerce_tax_display_shop' )
			->andReturn( 'incl' );

		Functions\stubs(
			[
				'wc_get_price_decimals'      => 2,
				'wc_tax_enabled'             => true,
				'wc_prices_include_tax'      => false,
				'wc_format_decimal'          => static function ( $number, $decimals ) {
					return \number_format( $number, $decimals );
				},
				'wc_get_price_including_tax' => static function ( $product, $args ) {
					return ( $args['price'] * 1.1 );
				},
			]
		);

		$expected = \number_format( ( $price * $tax_rate ), 2 );

		$this->assertSame( $expected, WPSEO_WooCommerce_Utils::get_product_display_price( $product ) );
	}

	/**
	 * Test the different cases for prices with or without tax.
	 *
	 * @covers WPSEO_WooCommerce_Utils::prices_should_include_tax
	 */
	public function test_prices_should_include_tax() {
		// Prices do not include tax, tax should be shown in shop => tax should be added.
		Functions\stubs(
			[
				'wc_prices_include_tax' => false,
			]
		);
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'woocommerce_tax_display_shop' )
			->andReturn( 'excl' );

		$this->assertFalse( WPSEO_WooCommerce_Utils::prices_should_include_tax() );

		// Prices include tax => tax should never be added.
		Functions\stubs(
			[
				'wc_prices_include_tax' => true,
			]
		);

		$this->assertFalse( WPSEO_WooCommerce_Utils::prices_should_include_tax() );
	}

	/**
	 * Test the different cases for prices with or without tax.
	 *
	 * @covers WPSEO_WooCommerce_Utils::prices_should_exclude_tax
	 */
	public function test_prices_should_exclude_tax() {
		// Prices include tax, tax should not be shown in shop => tax should be subtracted.
		Functions\stubs(
			[
				'wc_prices_include_tax' => true,
			]
		);
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'woocommerce_tax_display_shop' )
			->andReturn( 'excl' );

		$this->assertTrue( WPSEO_WooCommerce_Utils::prices_should_exclude_tax() );

		// Prices do not include tax => tax should not never be subtracted.
		Functions\stubs(
			[
				'wc_prices_include_tax' => false,
			]
		);

		$this->assertFalse( WPSEO_WooCommerce_Utils::prices_should_exclude_tax() );
	}
}
