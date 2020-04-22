<?php

namespace Yoast\WP\Woocommerce\Tests\Classes\Presenters;

use Brain\Monkey\Filters;
use Mockery;
use WPSEO_WooCommerce_Product_Condition_Presenter;
use Yoast\WP\Woocommerce\Tests\TestCase;

/**
 * Class Product_Condition_Presenter_Test.
 *
 * @coversDefaultClass \WPSEO_WooCommerce_Product_Condition_Presenter
 *
 * @group presenters
 */
class Product_Condition_Presenter_Test extends TestCase {

	/**
	 * Holds the product.
	 *
	 * @var \WC_Product|\Mockery\MockInterface
	 */
	protected $product;

	/**
	 * Holds the instance to test.
	 *
	 * @var WPSEO_WooCommerce_Product_Condition_Presenter
	 */
	protected $instance;

	/**
	 * Initializes the test setup.
	 */
	public function setUp() {
		parent::setUp();

		// Needs to exist as WPSEO_WooCommerce_Abstract_Product_Presenter depends on it.
		Mockery::mock( 'overload:Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter' );

		$this->product = Mockery::mock( 'WC_Product' );

		$this->instance = new WPSEO_WooCommerce_Product_Condition_Presenter( $this->product );
	}

	/**
	 * Tests the constructor.
	 *
	 * @covers ::__construct
	 * @covers \WPSEO_WooCommerce_Abstract_Product_Presenter::__construct
	 */
	public function test_construct() {
		$this->assertAttributeEquals( $this->product, 'product', $this->instance );
	}

	/**
	 * Tests the tag format.
	 */
	public function test_tag_format() {
		$this->assertAttributeEquals( '<meta property="product:condition" content="%s" />', 'tag_format', $this->instance );
	}

	/**
	 * Tests that the filter is applied.
	 *
	 * @covers ::get
	 */
	public function test_get() {
		Filters\expectApplied( 'Yoast\WP\Woocommerce\product_condition' )
			->once()
			->with( 'new', $this->product )
			->andReturn( 'condition' );

		$this->assertEquals( 'condition', $this->instance->get() );
	}

	/**
	 * Tests that the filter output is converted to a string.
	 *
	 * @covers ::get
	 */
	public function test_get_string_conversion() {
		Filters\expectApplied( 'Yoast\WP\Woocommerce\product_condition' )
			->once()
			->with( 'new', $this->product )
			->andReturn( 123 );

		$this->assertEquals( '123', $this->instance->get() );
	}
}
