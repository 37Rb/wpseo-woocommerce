<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * The Woocommerce_Import_Export class.
 * This class adds the GTIN8, GTIN12, GTIN13, GTIN14, ISBN, and MPN columns to the WooCommerce Product Import/Export screens
 */
class Woocommerce_Import_Export {

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

		add_filter( 'woocommerce_product_export_product_column_gtin8', [ $this, 'add_export_data_gtin8' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin12', [ $this, 'add_export_data_gtin12' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin13', [ $this, 'add_export_data_gtin13' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_gtin14', [ $this, 'add_export_data_gtin14' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_isbn', [ $this, 'add_export_data_isbn' ], 10, 2 );
		add_filter( 'woocommerce_product_export_product_column_mpn', [ $this, 'add_export_data_mpn' ], 10, 2 );
	}

	/**
	 * Add automatic mapping support for 'Custom Column'. 
	 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
	 *
	 * @param array $columns
	 * @return array $columns
	 */
	function add_column_to_mapping_screen( $columns ) {
		
		// potential column name => column slug
		$columns['GTIN8'] = 'gtin8';
		$columns['GTIN12 / UPC'] = 'gtin12';
		$columns['GTIN13 / EAN'] = 'gtin13';
		$columns['GTIN14 / ITF-14'] = 'gtin14';
		$columns['ISBN'] = 'isbn';
		$columns['MPN'] = 'mpn';

		return $columns;
	}

	/**
	 * Register the 'Custom Column' column in the importer.
	 *
	 * @param array $options
	 * @return array $options
	 */
	function add_column_to_importer( $options ) {

		// column slug => column name
		$options['gtin8'] = 'GTIN8';
		$options['gtin12'] = 'GTIN12 / UPC';
		$options['gtin13'] = 'GTIN13 / EAN';
		$options['gtin14'] = 'GTIN14 / ITF-14';
		$options['isbn'] = 'ISBN';
		$options['mpn'] = 'MPN';

		return $options;
	}

	/**
	 * Process the data read from the CSV file.
	 * This just saves the value in meta data, but you can do anything you want here with the data.
	 *
	 * @param WC_Product $object - Product being imported or updated.
	 * @param array $data - CSV data read for the product.
	 * @return WC_Product $object
	 */
	function process_import( $object, $data ) {
		


		$global_identifier_values = get_post_meta( $object->id, 'wpseo_global_identifier_values', true );
		$global_identifier_keys = array_keys($global_identifier_values);
		$data_keys = array_keys($data);
		
		$common_keys = array_intersect_key($global_identifier_keys, $data_keys);
		if ( $common_keys ) {
			foreach($common_keys as $key){
				$global_identifier_values[$key] = $data[$key];
			}
			update_post_meta( $object->id, 'wpseo_global_identifier_values', $global_identifier_values );
		}

		return $object;
	}


	/**
	 * Add the custom column to the exporter and the exporter column menu.
	 *
	 * @param array $columns
	 * @return array $columns
	 */
	function add_export_column( $columns ) {

		// column slug => column name
		$columns['gtin8'] = 'GTIN8';
		$columns['gtin12'] = 'GTIN12 / UPC';
		$columns['gtin13'] = 'GTIN13 / EAN';
		$columns['gtin14'] = 'GTIN14 / ITF-14';
		$columns['isbn'] = 'ISBN';
		$columns['mpn'] = 'MPN';

		return $columns;
	}

    /**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_gtin8($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'gtin8' ];
    }

	/**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_gtin12($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'gtin12' ];
    }

	/**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_gtin13($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'gtin13' ];
    }

	/**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_gtin14($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'gtin14' ];
    }

	/**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_isbn($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'isbn' ];
    }

	/**
     * Provide the data to be exported for one item in the column.
     *
     * @param mixed $value (default: '')
     * @param WC_Product $product
     * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
     */
    function add_export_data_mpn($value, $product){
		
		$wpseo_global_identifier_values = get_post_meta($product->id, 'wpseo_global_identifier_values', true);
        return $wpseo_global_identifier_values[ 'mpn' ];
    }

}
