<?php

namespace Yoast\WP\Woocommerce\Tests\Classes;

use Brain\Monkey\Functions;
use Yoast_Woocommerce_Import_Export;
use Yoast\WP\Woocommerce\Tests\TestCase;

/**
 * Class Import_Export_Test.
 *
 * @coversDefaultClass Yoast_Woocommerce_Import_Export
 */
class Import_Export_Test extends TestCase {

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

		$this->assertNotFalse( has_filter( 'woocommerce_product_export_column_names', [ $this->instance, 'add_columns' ] ), 'Adds options to export.' );
		$this->assertNotFalse( has_filter( 'woocommerce_product_export_product_default_columns', [ $this->instance, 'add_columns' ] ), 'Adds default options to export.' );

		$this->assertNotFalse( has_filter( 'woocommerce_csv_product_import_mapping_options', [ $this->instance, 'add_columns' ] ), 'Adds columns to import.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_csv_product_import_mapping_default_columns', [ $this->instance, 'add_column_to_mapping_screen' ] ), 'Adds columns to mapping screen.' );

		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin8', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data gtin8.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin12', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data gtin12.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin13', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data gtin13.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_gtin14', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data gtin14.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_isbn', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data isbn.' );
		$this->assertEquals( 10, has_filter( 'woocommerce_product_export_product_column_mpn', [ $this->instance, 'add_export_data_global_identifier_values' ] ), 'Adds export data mpn.' );
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
			'GTIN14 / ITF-14' => 'gtin14',
			'GTIN14/ITF-14'   => 'gtin14',
			'gtin14 / itf-14' => 'gtin14',
			'gtin14/itf-14'   => 'gtin14',
			'ISBN'            => 'isbn',
			'MPN'             => 'mpn',
			'GTIN8'           => 'gtin8',
			'gtin8'           => 'gtin8',
			'GTIN12 / UPC'    => 'gtin12',
			'GTIN12/UPC'      => 'gtin12',
			'gtin12 / upc'    => 'gtin12',
			'gtin12/upc'      => 'gtin12',
			'GTIN12'          => 'gtin12',
			'gtin12'          => 'gtin12',
			'UPC'             => 'gtin12',
			'upc'             => 'gtin12',
			'GTIN13 / EAN'    => 'gtin13',
			'GTIN13/EAN'      => 'gtin13',
			'gtin13 / ean'    => 'gtin13',
			'gtin13/ean'      => 'gtin13',
			'GTIN13'          => 'gtin13',
			'gtin13'          => 'gtin13',
			'EAN'             => 'gtin13',
			'ean'             => 'gtin13',
			'GTIN14'          => 'gtin14',
			'gtin14'          => 'gtin14',
			'ITF-14'          => 'gtin14',
			'itf-14'          => 'gtin14',
			'isbn'            => 'isbn',
			'mpn'             => 'mpn',
		];

		$this->assertEquals( $expected, $this->instance->add_column_to_mapping_screen( $columns ) );
	}

	/**
	 * Tests add_columns method.
	 *
	 * @covers ::add_columns
	 */
	public function test_add_columns() {
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

		$this->assertEquals( $expected, $this->instance->add_columns( $columns ) );
	}

	/**
	 * Tests add_export_data_global_identifier_values method.
	 *
	 * @covers ::add_export_data_global_identifier_values
	 * @covers ::get_global_identifier_values
	 *
	 * @dataProvider data_provider_add_export_data_global_identifier_values
	 *
	 * @param string $current_filter The current filter.
	 * @param int    $get_post_meta_times Number of times get_post_meta() is expected to be called.
	 * @param array  $global_identifier The global identifier values.
	 * @param string $expected The expected result.
	 */
	public function test_add_export_data_global_identifier_values( $current_filter, $get_post_meta_times, $global_identifier, $expected ) {
		Functions\expect( 'current_filter' )
			->once()
			->andReturn( $current_filter );

		Functions\expect( 'get_post_meta' )
			->times( $get_post_meta_times )
			->with( $this->product->id, 'wpseo_global_identifier_values', true )
			->andReturn( $global_identifier );

		$this->assertEquals( $expected, $this->instance->add_export_data_global_identifier_values( '', $this->product ) );
	}

	/**
	 * Data provider for the test_add_export_data_global_identifier_values.
	 *
	 * @return array
	 */
	public static function data_provider_add_export_data_global_identifier_values() {
		return [
			'Callback form a woocommerce_product_export_product_column filter' => [
				'current_filter'      => 'woocommerce_product_export_product_column_gtin8',
				'get_post_meta_times' => 1,
				'global_identifier'   => [ 'gtin8' => '12345678' ],
				'expected'            => '12345678',
			],
			'Callback form a filter not woocommerce_product_export_product_column' => [
				'current_filter'      => 'other_filter',
				'get_post_meta_times' => 0,
				'global_identifier'   => [],
				'expected'            => '',
			],
			'No post meta value' => [
				'current_filter'      => 'woocommerce_product_export_product_column_gtin8',
				'get_post_meta_times' => 1,
				'global_identifier'   => false,
				'expected'            => '',
			],
			'No global_identifier in the post meta value' => [
				'current_filter'      => 'woocommerce_product_export_product_column_gtin8',
				'get_post_meta_times' => 1,
				'global_identifier'   => [],
				'expected'            => '',
			],
		];
	}

	/**
	 * Tests process import callback methods.
	 *
	 * @dataProvider data_provider_process_import
	 *
	 * @covers ::process_import
	 * @covers ::get_global_identifier_values
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
	public static function data_provider_process_import() {
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
			'No global_identifier_values post meta' => [
				'data'                     => [
					'gtin8' => '123',
				],
				'global_identifier_values' => false,
				'update_times'             => 1,
				'expected'                 => [
					'gtin8'  => '123',
					'gtin12' => '',
					'gtin13' => '',
					'gtin14' => '',
					'isbn'   => '',
					'mpn'    => '',
				],
			],
		];
	}
}
