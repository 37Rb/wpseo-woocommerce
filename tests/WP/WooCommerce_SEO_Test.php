<?php

namespace Yoast\WP\Woocommerce\Tests\WP;

use Yoast\WP\Woocommerce\Tests\WP\Doubles\WooCommerce_SEO_Double;

/**
 * Unit tests.
 */
class WooCommerce_SEO_Test extends TestCase {

	/**
	 * Tests the sitemap filtering of a product that is not hidden.
	 *
	 * @covers Yoast_WooCommerce_SEO::filter_hidden_product
	 */
	public function test_filter_hidden_product_for_product_that_is_visible() {
		$product = self::factory()->post->create_and_get(
			[
				'post_type' => 'product',
			]
		);

		$instance = $this
			->getMockBuilder( WooCommerce_SEO_Double::class )
			->disableOriginalConstructor()
			->setMethods( [ 'excluded_from_catalog' ] )
			->getMock();

		$instance
			->expects( $this->once() )
			->method( 'excluded_from_catalog' )
			->will( $this->returnValue( [] ) );

		$this->assertSame(
			[ 'loc' => 'http://shop.site/product' ],
			$instance->filter_hidden_product( [ 'loc' => 'http://shop.site/product' ], 'post', $product )
		);
	}

	/**
	 * Tests the sitemap filtering of a product that is hidden from the catalog.
	 *
	 * @covers Yoast_WooCommerce_SEO::filter_hidden_product
	 */
	public function test_filter_hidden_product_for_product_that_is_hidden() {
		$product = self::factory()->post->create_and_get(
			[
				'post_type' => 'product',
			]
		);

		$instance = $this
			->getMockBuilder( WooCommerce_SEO_Double::class )
			->disableOriginalConstructor()
			->setMethods( [ 'excluded_from_catalog' ] )
			->getMock();

		$instance
			->expects( $this->once() )
			->method( 'excluded_from_catalog' )
			->will( $this->returnValue( [ $product->ID ] ) );

		$this->assertFalse(
			$instance->filter_hidden_product( [ 'loc' => 'http://shop.site/product' ], 'post', $product )
		);
	}

	/**
	 * Tests the sitemap filtering of a product that is not a product
	 *
	 * @covers Yoast_WooCommerce_SEO::filter_hidden_product
	 */
	public function test_filter_hidden_product_for_a_non_product() {
		$product = self::factory()->post->create_and_get(
			[
				'post_type' => 'post',
			]
		);

		$instance = $this
			->getMockBuilder( WooCommerce_SEO_Double::class )
			->disableOriginalConstructor()
			->setMethods( [ 'excluded_from_catalog' ] )
			->getMock();

		$instance
			->expects( $this->never() )
			->method( 'excluded_from_catalog' );

		$this->assertSame(
			[ 'loc' => 'http://shop.site/product' ],
			$instance->filter_hidden_product( [ 'loc' => 'http://shop.site/product' ], 'post', $product )
		);
	}

	/**
	 * Tests the sitemap filtering of a product when a invalid post object is given.
	 *
	 * @covers Yoast_WooCommerce_SEO::filter_hidden_product
	 */
	public function test_filter_hidden_product_when_invalid_post_object_is_given() {
		$instance = $this
			->getMockBuilder( WooCommerce_SEO_Double::class )
			->disableOriginalConstructor()
			->setMethods( [ 'excluded_from_catalog' ] )
			->getMock();

		$instance
			->expects( $this->never() )
			->method( 'excluded_from_catalog' );

		$this->assertSame(
			[ 'loc' => 'http://shop.site/product' ],
			$instance->filter_hidden_product( [ 'loc' => 'http://shop.site/product' ], 'post', null )
		);
	}

	/**
	 * Tests the sitemap filtering when no url loc is given to the url data.
	 *
	 * @covers Yoast_WooCommerce_SEO::filter_hidden_product
	 */
	public function test_filter_hidden_product_when_no_url_loc_is_present() {
		$instance = $this
			->getMockBuilder( WooCommerce_SEO_Double::class )
			->disableOriginalConstructor()
			->setMethods( [ 'excluded_from_catalog' ] )
			->getMock();

		$instance
			->expects( $this->never() )
			->method( 'excluded_from_catalog' );

		$this->assertSame(
			[ 'no-loc' => 'http://shop.site/product' ],
			$instance->filter_hidden_product( [ 'no-loc' => 'http://shop.site/product' ], 'post', null )
		);
	}
}
