<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * The Yoast_Woocommerce_Import_Export class.
 * This class adds the GTIN8, GTIN12, GTIN13, GTIN14, ISBN, and MPN columns to the WooCommerce Product Import/Export screens.
 */
class Yoast_Woocommerce_Import_Export {

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_product_export_column_names', [ $this, 'add_export_column' ] );
		add_filter( 'woocommerce_product_export_product_default_columns', [ $this, 'add_export_column' ] );

		add_filter( 'woocommerce_csv_product_import_mapping_options', [ $this, 'add_column_to_importer' ] );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', [ $this, 'add_column_to_mapping_screen' ] );

		add_filter( 'woocommerce_product_import_pre_insert_product_object', [ $this, 'process_import' ], 10, 2 );

		add_filter( 'woocommerce_product_export_product_column_gtin8', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin12', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin13', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin14', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_isbn', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_mpn', [ $this, 'add_export_data_global_identifier_values' ], 10, 2 );
	}

	/**
	 * Add automatic mapping support for wpseo_global_identifier_values options.
	 * This will automatically select the correct mapping for columns of wpseo_global_identifier_values options.
	 *
	 * @param array $columns - The column names.
	 * @return array $columns - The updated column names with the custom potential names.
	 */
	public function add_column_to_mapping_screen( $columns ) {
		$columns['GTIN8'] = 'gtin8';
		$columns['gtin8'] = 'gtin8';

		$columns['GTIN12 / UPC'] = 'gtin12';
		$columns['gtin12 / upc'] = 'gtin12';
		$columns['gtin12/upc']   = 'gtin12';
		$columns['GTIN12']       = 'gtin12';
		$columns['gtin12']       = 'gtin12';
		$columns['UPC']          = 'gtin12';
		$columns['upc']          = 'gtin12';

		$columns['GTIN13 / EAN'] = 'gtin13';
		$columns['gtin13 / ean'] = 'gtin13';
		$columns['gtin13/ean']   = 'gtin13';
		$columns['GTIN13']       = 'gtin13';
		$columns['gtin13']       = 'gtin13';
		$columns['EAN']          = 'gtin13';
		$columns['ean']          = 'gtin13';

		$columns['GTIN14 / ITF-14'] = 'gtin14';
		$columns['gtin14 / itf-14'] = 'gtin14';
		$columns['gtin14/itf-14']   = 'gtin14';
		$columns['GTIN14']          = 'gtin14';
		$columns['gtin14']          = 'gtin14';
		$columns['ITF-14']          = 'gtin14';
		$columns['itf-14']          = 'gtin14';

		$columns['ISBN'] = 'isbn';
		$columns['isbn'] = 'isbn';

		$columns['MPN'] = 'mpn';
		$columns['mpn'] = 'mpn';

		return $columns;
	}

	/**
	 * Register the wpseo_global_identifier_values columns in the importer.
	 *
	 * @param array $options - The column names.
	 * @return array $options - The updated column names.
	 */
	public function add_column_to_importer( $options ) {
		// column slug => column name.
		$options['gtin8']  = 'GTIN8';
		$options['gtin12'] = 'GTIN12 / UPC';
		$options['gtin13'] = 'GTIN13 / EAN';
		$options['gtin14'] = 'GTIN14 / ITF-14';
		$options['isbn']   = 'ISBN';
		$options['mpn']    = 'MPN';

		return $options;
	}

	/**
	 * Process the data read from the CSV file.
	 * Adds the global identifiers values to the corespondent meta field.
	 *
	 * @param WC_Product $object - Product being imported or updated.
	 * @param array      $data - CSV data read for the product.
	 * @return WC_Product $object
	 */
	public function process_import( $object, $data ) {
		$global_identifier_values   = get_post_meta( $object->id, 'wpseo_global_identifier_values', true );
		$global_identifier_defaults = [
			'gtin8'  => '',
			'gtin12' => '',
			'gtin13' => '',
			'gtin14' => '',
			'isbn'   => '',
			'mpn'    => '',
		];
		$values                     = array_intersect_key( $data, $global_identifier_defaults );

		if ( $values && is_array( $values ) ) {
			$values = array_map( 'sanitize_text_field', $values );
			$base   = ( $global_identifier_values && is_array( $global_identifier_values ) ) ? $global_identifier_values : $global_identifier_defaults;
			$merged = array_merge( $base, $values );
			update_post_meta( $object->id, 'wpseo_global_identifier_values', $merged );
		}

		return $object;
	}

	/**
	 * Add the custom column to the exporter and the exporter column menu.
	 *
	 * @param array $columns - The column names.
	 * @return array $columns - The updated column names.
	 */
	public function add_export_column( $columns ) {
		// column slug => column name.
		$columns['gtin8']  = 'GTIN8';
		$columns['gtin12'] = 'GTIN12 / UPC';
		$columns['gtin13'] = 'GTIN13 / EAN';
		$columns['gtin14'] = 'GTIN14 / ITF-14';
		$columns['isbn']   = 'ISBN';
		$columns['mpn']    = 'MPN';

		return $columns;
	}

	/**
	 * Provide the data to be exported for one item in a column of the wpseo global identifier values.
	 *
	 * @param mixed      $value (default: '').
	 * @param WC_Product $product - The product object.
	 * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
	 */
	public function add_export_data_global_identifier_values( $value, $product ) {
		$current_hook = current_filter();
		if ( strpos( $current_hook, 'woocommerce_product_export_product_column_' ) !== false ) {
			$global_identifier              = str_replace( 'woocommerce_product_export_product_column_', '', $current_hook );
			$wpseo_global_identifier_values = get_post_meta( $product->id, 'wpseo_global_identifier_values', true );
			if ( is_array( $wpseo_global_identifier_values ) && array_key_exists( $global_identifier, $wpseo_global_identifier_values ) ) {
				return $wpseo_global_identifier_values[ $global_identifier ];
			}
		}
		return '';
	}
}
