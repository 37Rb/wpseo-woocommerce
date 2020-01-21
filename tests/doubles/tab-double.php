<?php

namespace Yoast\WP\Woocommerce\Tests\Doubles;

use WPSEO_WooCommerce_Yoast_Tab;

/**
 * Class WPSEO_WooCommerce_Yoast_Tab_Double
 */
class Yoast_Tab_Double extends WPSEO_WooCommerce_Yoast_Tab {

	/**
	 * Make sure the data is safe to save.
	 *
	 * @param string $value The value we're testing.
	 *
	 * @return bool True when safe, false when it's not.
	 */
	public function validate_data( $value ) {
		return parent::validate_data( $value );
	}

	/**
	 * Displays an input field for an identifier.
	 *
	 * @param string $type  Type of identifier, used for input name.
	 * @param string $label Label for the identifier input.
	 * @param string $value Current value of the identifier.
	 *
	 * @return void
	 */
	public function input_field_for_identifier( $type, $label, $value ) {
		parent::input_field_for_identifier( $type, $label, $value );
	}
}
