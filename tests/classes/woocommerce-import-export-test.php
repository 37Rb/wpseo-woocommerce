<?php

namespace Yoast\WP\Woocommerce\Tests\Classes;

use Brain\Monkey\Functions;
use Yoast_Woocommerce_Import_Export;
use Yoast\WP\Woocommerce\Tests\TestCase;

/**
 * Class WooCommerce_Import_Export_Test.
 *
 * @coversDefaultClass Yoast_Woocommerce_Import_Export
 */
class WooCommerce_Import_Export_Test extends TestCase {

	/**
	 * The instance of the class under test.
	 *
	 * @var Yoast_Woocommerce_Import_Export
	 */
	protected $instance;

	/**
	 * The product object
	 *
	 * @var object
	 */
	protected $product;

	/**
	 * Testing environment setup
	 */
	public function set_up() {
		parent::set_up();
		$this->instance = new Yoast_Woocommerce_Import_Export();

		$this->product = (object) [
			'id' => 1,
		];
	}

	/**
	 * Tests register_hooks method.
	 *
	 * @covers ::register_hooks
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();

		$this->assertNotFalse( has_filter( 'woocommerce_product_export_column_names', [ $this->instance, 'add_export_column' ] ), 'Adds options to export.' );
		$this->assertNotFalse( has_filter( 'woocommerce_product_export_product_default_columns', [ $this->instance, 'add_export_column' ] ), 'Adds default options to export.' );

		$this->assertNotFalse( has_filter( 'woocommerce_csv_product_import_mapping_options', [ $this->instance, 'add_column_to_importer' ] ), 'Adds columns to import.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_csv_product_import_mapping_default_columns', [ $this->instance, 'add_column_to_mapping_screen' ] ), 'Adds columns to mapping screen.' );

		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin8', [ $this->instance, 'add_export_data_gtin8' ] ), 'Adds export data gtin8.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin12', [ $this->instance, 'add_export_data_gtin12' ] ), 'Adds export data gtin12.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin13', [ $this->instance, 'add_export_data_gtin13' ] ), 'Adds export data gtin13.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin14', [ $this->instance, 'add_export_data_gtin14' ] ), 'Adds export data gtin14.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_isbn', [ $this->instance, 'add_export_data_isbn' ] ), 'Adds export data isbn.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_mpn', [ $this->instance, 'add_export_data_mpn' ] ), 'Adds export data mpn.' );
	}

	/**
	 * Tests add_column_to_mapping_screen method.
	 *
	 * @covers ::add_column_to_mapping_screen
	 */
	public function test_add_column_to_mapping_screen() {
		$columns = [
			'column1' => 'column1',
			'column2' => 'column2',
		];

		$expected = [
			'column1'         => 'column1',
			'column2'         => 'column2',
			'GTIN8'           => 'gtin8',
			'GTIN12 / UPC'    => 'gtin12',
			'GTIN13 / EAN'    => 'gtin13',
			'GTIN14 / ITF-14' => 'gtin14',
			'ISBN'            => 'isbn',
			'MPN'             => 'mpn',
		];

		$this->assertEquals( $expected, $this->instance->add_column_to_mapping_screen( $columns ) );
	}

	/**
	 * Tests add_column_to_importer method.
	 *
	 * @covers ::add_column_to_importer
	 */
	public function test_add_column_to_importer() {
		$columns = [
			'column1' => 'column1',
			'column2' => 'column2',
		];

		$expected = [
			'column1' => 'column1',
			'column2' => 'column2',
			'gtin8'   => 'GTIN8',
			'gtin12'  => 'GTIN12 / UPC',
			'gtin13'  => 'GTIN13 / EAN',
			'gtin14'  => 'GTIN14 / ITF-14',
			'isbn'    => 'ISBN',
			'mpn'     => 'MPN',
		];

		$this->assertEquals( $expected, $this->instance->add_column_to_importer( $columns ) );
	}

	/**
	 * Tests add_export_column method.
	 *
	 * @covers ::add_export_column
	 */
	public function test_add_export_column() {
		$columns = [
			'column1' => 'column1',
			'column2' => 'column2',
		];

		$expected = [
			'column1' => 'column1',
			'column2' => 'column2',
			'gtin8'   => 'GTIN8',
			'gtin12'  => 'GTIN12 / UPC',
			'gtin13'  => 'GTIN13 / EAN',
			'gtin14'  => 'GTIN14 / ITF-14',
			'isbn'    => 'ISBN',
			'mpn'     => 'MPN',
		];

		$this->assertEquals( $expected, $this->instance->add_export_column( $columns ) );
	}

	/**
	 * Tests add_export_data_gtin8 method.
	 *
	 * @covers ::add_export_data_gtin8
	 */
	public function test_add_export_data_gtin8() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'gtin8' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_gtin8( '', $this->product ) );
	}

	/**
	 * Tests add_export_data_gtin12 method.
	 *
	 * @covers ::add_export_data_gtin12
	 */
	public function test_add_export_data_gtin12() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'gtin12' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_gtin12( '', $this->product ) );
	}

	/**
	 * Tests add_export_data_gtin13 method.
	 *
	 * @covers ::add_export_data_gtin13
	 */
	public function test_add_export_data_gtin13() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'gtin13' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_gtin13( '', $this->product ) );
	}

	/**
	 * Tests add_export_data_gtin14 method.
	 *
	 * @covers ::add_export_data_gtin14
	 */
	public function test_add_export_data_gtin14() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'gtin14' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_gtin14( '', $this->product ) );
	}

	/**
	 * Tests add_export_data_isbn method.
	 *
	 * @covers ::add_export_data_isbn
	 */
	public function test_add_export_data_isbn() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'isbn' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_isbn( '', $this->product ) );
	}

	/**
	 * Tests add_export_data_mpn method.
	 *
	 * @covers ::add_export_data_mpn
	 */
	public function test_add_export_data_mpn() {
		$expected = '12345678';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn(
				[
					'mpn' => $expected,
				]
			);

		$this->assertEquals( $expected, $this->instance->add_export_data_mpn( '', $this->product ) );
	}

	/**
	 * Tests process import callback methods.
	 *
	 * @dataProvider data_provider_process_import
	 *
	 * @covers ::process_import
	 *
	 * @param array $data                      The data to be imported.
	 * @param array $global_identifier_values The global identifier values.
	 * @param int   $update_times    Number of times update_post_meta_times() is expected to be called.
	 * @param array $expected                  The expected result.
	 */
	public function test_process_import( $data, $global_identifier_values, $update_times, $expected ) {

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn( $global_identifier_values );

		Functions\expect( 'update_post_meta' )
			->times( $update_times )
			->with( $this->product->id, 'wpseo_global_identifier_values', $expected );

		$this->instance->process_import( $this->product, $data );
	}

	/**
	 * Data provider for the test_process_import.
	 *
	 * @return array
	 */
	public function data_provider_process_import() {
		return [
			'Update data' => [
				'data'                     => [
					'gtin8'  => '12345678',
					'gtin12' => '123456789012',
					'gtin13' => '1234567890123',
					'gtin14' => '12345678901234',
					'isbn'   => '1234567890123',
					'mpn'    => '1234567890123',
				],
				'global_identifier_values' => [
					'gtin8'  => '12',
					'gtin12' => '1234012',
					'gtin13' => '1230123',
					'gtin14' => '101234',
					'isbn'   => '1234123',
					'mpn'    => '123453',
				],
				'update_times'             => 1,
				'expected'                 => [
					'gtin8'  => '12345678',
					'gtin12' => '123456789012',
					'gtin13' => '1234567890123',
					'gtin14' => '12345678901234',
					'isbn'   => '1234567890123',
					'mpn'    => '1234567890123',
				],
			],
			'New partial data' => [
				'data'                     => [
					'gtin8'  => '12345678',
					'gtin13' => '1234567890123',
					'gtin14' => '12345678901234',

				],
				'global_identifier_values' => [],
				'update_times'             => 1,
				'expected'                 => [
					'gtin8'  => '12345678',
					'gtin12' => '',
					'gtin13' => '1234567890123',
					'gtin14' => '12345678901234',
					'isbn'   => '',
					'mpn'    => '',
				],
			],
			'No data' => [
				'data'                     => [],
				'global_identifier_values' => [
					'gtin8'  => '12',
					'gtin12' => '1234012',
					'gtin13' => '1230123',
					'gtin14' => '101234',
					'isbn'   => '1234123',
					'mpn'    => '123453',
				],
				'update_times'             => 0,
				'expected'                 => [],
			],
			'update_partial_data' => [
				'data'                     => [
					'gtin8' => '888',
				],
				'global_identifier_values' => [
					'gtin8'  => '12',
					'gtin12' => '1234012',
					'gtin13' => '1230123',
					'gtin14' => '101234',
					'isbn'   => '1234123',
					'mpn'    => '123453',
				],
				'update_times'             => 1,
				'expected'                 => [
					'gtin8'  => '888',
					'gtin12' => '1234012',
					'gtin13' => '1230123',
					'gtin14' => '101234',
					'isbn'   => '1234123',
					'mpn'    => '123453',
				],
			],
			'No matching data' => [
				'data'                     => [
					'test' => 123,
				],
				'global_identifier_values' => [
					'gtin8'  => '12',
					'gtin12' => '1234012',
					'gtin13' => '1230123',
					'gtin14' => '101234',
					'isbn'   => '1234123',
					'mpn'    => '123453',
				],
				'update_times'             => 0,
				'expected'                 => [],
			],
		];
	}
}
