<?php

namespace Yoast\WP\Woocommerce\Tests\Unit\Classes\Integrations;

use Brain\Monkey;
use Mockery;
use stdClass;
use Yoast_WooCommerce_TranslationsPress;
use Yoast\WP\SEO\Helpers\Date_Helper;
use Yoast\WP\Local\Integrations\TranslationsPress;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Class TranslationsPress_Test.
 *
 * @coversDefaultClass \Yoast_WooCommerce_TranslationsPress
 *
 * @group integrations
 */
class TranslationsPress_Test extends TestCase {

	/**
	 * The Date Helper object.
	 *
	 * @var Mockery\MockInterface & Date_Helper
	 */
	protected $date_helper;

	/**
	 * Represents the instance to test.
	 *
	 * @var Yoast_WooCommerce_TranslationsPress
	 */
	protected $instance;

	/**
	 * Sets an instance for test purposes.
	 */
	public function set_up() {
		parent::set_up();

		$this->date_helper = Mockery::mock( Date_Helper::class );
		$this->instance    = Mockery::mock( Yoast_WooCommerce_TranslationsPress::class, [ $this->date_helper ] )->makePartial();
	}

	/**
	 * Tests if the expected conditionals are in place.
	 *
	 * @covers ::__construct
	 */
	public function test_construct() {
		$this->assertSame(
			'yoast-woo-seo',
			$this->getPropertyValue( $this->instance, 'slug' )
		);
		$this->assertSame(
			'yoast_translations_yoast-woo-seo',
			$this->getPropertyValue( $this->instance, 'transient_key' )
		);
		$this->assertSame(
			'https://packages.translationspress.com/yoast/yoast-woo-seo/packages.json',
			$this->getPropertyValue( $this->instance, 'api_url' )
		);

		$this->assertSame(
			$this->date_helper,
			$this->getPropertyValue( $this->instance, 'date_helper' )
		);
	}

	/**
	 * Tests the registration of the hooks.
	 *
	 * @covers ::register_hooks
	 */
	public function test_register_hooks() {
		Monkey\Actions\expectAdded( 'init' )
			->with( [ $this->instance, 'register_clean_translations_cache' ], \PHP_INT_MAX );

		Monkey\Filters\expectAdded( 'translations_api' )
			->with( [ $this->instance, 'translations_api' ], 10, 3 );

		Monkey\Filters\expectAdded( 'site_transient_update_plugins' )
			->with( [ $this->instance, 'site_transient_update_plugins' ] );

		$this->instance->register_hooks();
	}

	/**
	 * Tests the successful call to the translations_api method.
	 *
	 * @covers ::translations_api
	 */
	public function test_translations_api() {
		$result         = [];
		$requested_type = 'plugins';
		$args           = [
			'slug' => 'yoast-woo-seo',
		];

		$this->instance->expects( 'get_translations' );

		$this->instance->translations_api( $result, $requested_type, $args );
	}

	/**
	 * Tests the unsuccessful call to the translations_api method when the requested type is not 'plugins'.
	 *
	 * @covers ::translations_api
	 */
	public function test_translations_api_not_plugins() {
		$result         = [ 'an', 'array' ];
		$requested_type = 'themes';
		$args           = [
			'slug' => 'yoast-woo-seo',
		];

		$this->instance->expects( 'get_translations' )->never();

		$this->assertSame( $result, $this->instance->translations_api( $result, $requested_type, $args ) );
	}

	/**
	 * Tests the unsuccessful call to the translations_api method when the slug is not right.
	 *
	 * @covers ::translations_api
	 */
	public function test_translations_api_wrong_slug() {
		$result         = [ 'an', 'array' ];
		$requested_type = 'plugins';
		$args           = [
			'slug' => 'wordpress-seo',
		];

		$this->instance->expects( 'get_translations' )->never();

		$this->assertSame( $result, $this->instance->translations_api( $result, $requested_type, $args ) );
	}

	/**
	 * Tests the call to the site_transient_update_plugins method.
	 *
	 * @covers ::site_transient_update_plugins
	 * @dataProvider site_transient_update_plugins_provider
	 *
	 * @param bool|Object $value                  The current transient object.
	 * @param array       $translations           The translations array from the API call.
	 * @param array       $installed_translations The currently installed translations.
	 * @param array       $available_languages    The languages currently available in the WordPress installation.
	 * @param bool|Object $expected               The expected test result.
	 */
	public function test_site_transient_update_plugins( $value, $translations, $installed_translations, $available_languages, $expected ) {
		$this->instance
			->expects( 'get_translations' )
			->andReturn( $translations );

		Monkey\Functions\expect( '\wp_get_installed_translations' )
			->with( 'plugins' )
			->andReturn( $installed_translations );

		Monkey\Functions\expect( '\get_available_languages' )
			->andReturn( $available_languages );

		$this->assertEquals( $expected, $this->instance->site_transient_update_plugins( $value ) );
	}

	/**
	 * Data provider for the test_site_transient_update_plugins method.
	 *
	 * @return array[]
	 */
	public function site_transient_update_plugins_provider() {
		$expected               = new stdClass();
		$expected->translations = [];

		$expected_it               = new stdClass();
		$expected_it->translations = [
			[
				'language'   => 'it',
				'updated'    => '2021-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'yoast-woo-seo',
				'autoupdate' => true,
			],
		];

		$expected_it_es               = new stdClass();
		$expected_it_es->translations = [
			[
				'language'   => 'it',
				'updated'    => '2021-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'yoast-woo-seo',
				'autoupdate' => true,
			],
			[
				'language'   => 'es',
				'updated'    => '2021-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'yoast-woo-seo',
				'autoupdate' => true,
			],
		];

		$existing_value               = new stdClass();
		$existing_value->translations = [
			[
				'language'   => 'it',
				'updated'    => '2020-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'wordpress-seo',
				'autoupdate' => true,
			],
		];

		$expected_existing               = new stdClass();
		$expected_existing->translations = [
			[
				'language'   => 'it',
				'updated'    => '2020-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'wordpress-seo',
				'autoupdate' => true,
			],
			[
				'language'   => 'it',
				'updated'    => '2021-03-09T12:00:00+00:00',
				'type'       => 'plugin',
				'slug'       => 'yoast-woo-seo',
				'autoupdate' => true,
			],
		];

		return [
			'No API results' => [
				false,
				[],
				[],
				[],
				$expected,
			],
			'No translations in API results' => [
				false,
				[
					'yoast-woo-seo' => [],
				],
				[],
				[],
				$expected,
			],
			'Empty translations in API results' => [
				false,
				[
					'yoast-woo-seo' => [
						'translations' => [],
					],
				],
				[],
				[],
				$expected,
			],
			'Only update Italian' => [
				false,
				[
					'yoast-woo-seo' => [
						'translations' => [
							[
								'language' => 'it',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
							[
								'language' => 'nl',
								'updated'  => '2021-03-09T12:30:00+00:00',
							],
						],
					],
				],
				[
					'yoast-woo-seo' => [
						'it' => [
							'PO-Revision-Date'  => '2021-03-08T12:00:00+00:00',
						],
						'nl' => [
							'PO-Revision-Date'  => '2021-03-09T12:30:00+00:00',
						],
					],
				],
				[
					'it',
					'nl',
				],
				$expected_it,
			],
			'Update Italian, install Spanish' => [
				false,
				[
					'yoast-woo-seo' => [
						'translations' => [
							[
								'language' => 'it',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
							[
								'language' => 'nl',
								'updated'  => '2021-03-09T12:30:00+00:00',
							],
							[
								'language' => 'es',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
						],
					],
				],
				[
					'yoast-woo-seo' => [
						'it' => [
							'PO-Revision-Date'  => '2021-03-08T12:00:00+00:00',
						],
						'nl' => [
							'PO-Revision-Date'  => '2021-03-09T12:30:00+00:00',
						],
					],
				],
				[
					'it',
					'nl',
					'es',
				],
				$expected_it_es,
			],
			'Ignore uninstalled French language' => [
				false,
				[
					'yoast-woo-seo' => [
						'translations' => [
							[
								'language' => 'it',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
							[
								'language' => 'nl',
								'updated'  => '2021-03-09T12:30:00+00:00',
							],
							[
								'language' => 'es',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
							[
								'language' => 'fr',
								'updated'  => '2021-03-09T12:30:00+00:00',
							],
						],
					],
				],
				[
					'yoast-woo-seo' => [
						'it' => [
							'PO-Revision-Date'  => '2021-03-08T12:00:00+00:00',
						],
						'nl' => [
							'PO-Revision-Date'  => '2021-03-09T12:30:00+00:00',
						],
					],
				],
				[
					'it',
					'nl',
					'es',
				],
				$expected_it_es,
			],
			'Add Italian to existing transient value' => [
				$existing_value,
				[
					'yoast-woo-seo' => [
						'translations' => [
							[
								'language' => 'it',
								'updated'  => '2021-03-09T12:00:00+00:00',
							],
							[
								'language' => 'nl',
								'updated'  => '2021-03-09T12:30:00+00:00',
							],
						],
					],
				],
				[
					'yoast-woo-seo' => [
						'it' => [
							'PO-Revision-Date'  => '2021-03-08T12:00:00+00:00',
						],
						'nl' => [
							'PO-Revision-Date'  => '2021-03-09T12:30:00+00:00',
						],
					],
				],
				[
					'it',
					'nl',
				],
				$expected_existing,
			],
		];
	}

	/**
	 * Tests the register_clean_translations_cache call.
	 *
	 * @covers ::register_clean_translations_cache
	 */
	public function test_register_clean_translations_cache() {
		Monkey\Actions\expectAdded( 'set_site_transient_update_plugins' )
			->with( [ $this->instance, 'clean_translations_cache' ] );

		Monkey\Actions\expectAdded( 'delete_site_transient_update_plugins' )
			->with( [ $this->instance, 'clean_translations_cache' ] );

		$this->instance->register_clean_translations_cache();
	}

	/**
	 * Tests the successful call to the clean_translations_cache method.
	 *
	 * @covers ::clean_translations_cache
	 */
	public function test_clean_translations_cache() {
		$translations = [
			'_last_checked' => '1615131103',
		];

		$current_time = '1615303904';

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations );

		$this->date_helper
			->expects( 'current_time' )
			->andReturn( $current_time );

		Monkey\Functions\expect( '\delete_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) );

		$this->instance->clean_translations_cache();
	}

	/**
	 * Tests the call to the clean_translations_cache method when the transient does not exists.
	 *
	 * @covers ::clean_translations_cache
	 */
	public function test_clean_translations_cache_no_transient() {
		$translations = false;

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations );

		$this->date_helper
			->expects( 'current_time' )
			->never();

		Monkey\Functions\expect( '\delete_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->never();

		$this->instance->clean_translations_cache();
	}

	/**
	 * Tests the call to the clean_translations_cache method when the transient does not have a _last_checked value
	 *
	 * @covers ::clean_translations_cache
	 */
	public function test_clean_translations_cache_no_last_checked() {
		$translations = [];

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations );

		$this->date_helper
			->expects( 'current_time' )
			->never();

		Monkey\Functions\expect( '\delete_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->never();

		$this->instance->clean_translations_cache();
	}

	/**
	 * Tests the call to the clean_translations_cache method when the last check was less than 1 day ago.
	 *
	 * @covers ::clean_translations_cache
	 */
	public function test_clean_translations_cache_less_than_one_day_ago() {
		$translations = [
			'_last_checked' => '1615300904',
		];

		$current_time = '1615303904';

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations );

		$this->date_helper
			->expects( 'current_time' )
			->andReturn( $current_time );

		Monkey\Functions\expect( '\delete_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->never();

		$this->instance->clean_translations_cache();
	}

	/**
	 * Tests the successful call to the get_translations method.
	 *
	 * @covers ::get_translations
	 */
	public function test_get_translations() {
		$translations_from_transient = false;
		$result_json                 = '{"translations":[{"language":"ar","version":"1.0","updated":"2020-09-13T19:02:57+00:00","english_name":"Arabic","native_name":"\u0627\u0644\u0639\u0631\u0628\u064a\u0629","package":"https:\/\/packages.translationspress.com\/yoast\/yoast-woo-seo\/yoast-woo-seo-ar.zip","iso":["ar","ara"]},{"language":"bn_BD","version":"1.0","updated":"2020-09-13T19:03:08+00:00","english_name":"Bengali (Bangladesh)","native_name":"\u09ac\u09be\u0982\u09b2\u09be","package":"https:\/\/packages.translationspress.com\/yoast\/yoast-woo-seo\/yoast-woo-seo-bn_BD.zip","iso":["bn"]}]}';
		$current_time                = '1615303904';
		$translations                = [
			'yoast-woo-seo' => \json_decode( $result_json, true ),
			'_last_checked' => $current_time,
		];

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations_from_transient );

		Monkey\Functions\expect( '\wp_remote_get' )
			->with( $this->getPropertyValue( $this->instance, 'api_url' ) );

		Monkey\Functions\expect( '\wp_remote_retrieve_body' )
			->withAnyArgs()
			->andReturn( $result_json );

		$this->date_helper
			->expects( 'current_time' )
			->andReturn( $current_time );

		Monkey\Functions\expect( '\set_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ), $translations );

		$this->assertEquals( $translations, $this->instance->get_translations() );
	}

	/**
	 * Tests the call to the get_translations method when the transient is already set.
	 *
	 * @covers ::get_translations
	 */
	public function test_get_translations_transient_already_set() {
		$translations_from_transient = [ 'some', 'thing' ];

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations_from_transient );

		Monkey\Functions\expect( '\wp_remote_get' )
			->never();

		Monkey\Functions\expect( '\wp_remote_retrieve_body' )
			->never();

		$this->date_helper
			->expects( 'current_time' )
			->never();

		Monkey\Functions\expect( '\set_site_transient' )
			->never();

		$this->assertEquals( $translations_from_transient, $this->instance->get_translations() );
	}

	/**
	 * Tests the call to the get_translations method when the API call fails.
	 *
	 * @covers ::get_translations
	 */
	public function test_get_translations_api_call_failing() {
		$translations_from_transient = false;
		$result_json                 = '500 Error';
		$current_time                = '1615303904';
		$translations                = [
			'yoast-woo-seo' => [],
			'_last_checked' => $current_time,
		];

		Monkey\Functions\expect( '\get_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ) )
			->andReturn( $translations_from_transient );

		Monkey\Functions\expect( '\wp_remote_get' )
			->with( $this->getPropertyValue( $this->instance, 'api_url' ) );

		Monkey\Functions\expect( '\wp_remote_retrieve_body' )
			->withAnyArgs()
			->andReturn( $result_json );

		$this->date_helper
			->expects( 'current_time' )
			->andReturn( $current_time );

		Monkey\Functions\expect( '\set_site_transient' )
			->with( $this->getPropertyValue( $this->instance, 'transient_key' ), $translations );

		$this->assertEquals( $translations, $this->instance->get_translations() );
	}
}
