<?php
/**
 * Notification {field:}, {value:}, and {product:} tag replacement.
 *
 * @package weForms
 */

/**
 * Tests for WeForms_Notification::replace_field_tags().
 */
class WeForms_Notification_Field_Tags_Test extends WP_UnitTestCase {

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * Install tables and a form with checkbox and multiple product fields.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$_SERVER['HTTP_USER_AGENT'] = 'Firefox/120.0';

		weforms()->wpdb_table_shortcuts();
		weforms()->fields->get_fields();

		require_once WEFORMS_INCLUDES . '/class-installer.php';

		$installer = new WeForms_Installer();
		$installer->create_tables();

		$this->form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => 'publish',
				'post_title'  => 'Order form',
			)
		);

		$this->create_field(
			array(
				'template' => 'checkbox_field',
				'name'     => 'toppings',
				'label'    => 'Toppings',
				'options'  => array(
					'cheese' => 'Cheese',
					'olives' => 'Olives',
				),
			)
		);

		$this->create_field(
			array(
				'template' => 'multiple_product',
				'name'     => 'items',
				'label'    => 'Items',
				'options'  => array(
					'T-Shirt'    => '10',
					'Sweatshirt' => '20',
					'Shoes'      => '30',
				),
			)
		);
	}

	/**
	 * Create a form field.
	 *
	 * @param array $field Field settings.
	 *
	 * @return void
	 */
	private function create_field( $field ) {
		self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $this->form_id,
				'post_content' => maybe_serialize( $field ),
			)
		);
	}

	/**
	 * Create an entry.
	 *
	 * @param array $fields Entry meta.
	 *
	 * @return int
	 */
	private function create_entry( $fields ) {
		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			$fields
		);

		$this->assertIsInt( $entry_id );

		return $entry_id;
	}

	/**
	 * A checkbox value renders as joined text, not "Array".
	 *
	 * @return void
	 */
	public function test_field_tag_joins_array_values() {
		$entry_id = $this->create_entry(
			array(
				'toppings' => array( 'Cheese', 'Olives' ),
			)
		);

		$text = WeForms_Notification::replace_field_tags( 'Toppings: {field:toppings}', $entry_id );

		$this->assertSame( 'Toppings: Cheese' . WeForms::$field_separator . 'Olives', $text );
		$this->assertStringNotContainsString( 'Array', $text );
	}

	/**
	 * A scalar value, and a tag with no stored value, still render.
	 *
	 * @return void
	 */
	public function test_field_tag_scalar_and_missing_values() {
		$entry_id = $this->create_entry(
			array(
				'toppings' => 'Cheese',
			)
		);

		$this->assertSame( 'Cheese|', WeForms_Notification::replace_field_tags( '{field:toppings}|{field:nothing}', $entry_id ) );
	}

	/**
	 * The product tag lists only the selected products.
	 *
	 * @return void
	 */
	public function test_product_tag_lists_selected_products() {
		$entry_id = $this->create_entry(
			array(
				'items' => array(
					array(
						'product'  => 'T-Shirt',
						'quantity' => 2,
						'price'    => 10,
					),
					array(
						'product'  => 'Shoes',
						'quantity' => 1,
						'price'    => 30,
					),
				),
			)
		);

		$this->assertSame(
			'T-Shirt' . WeForms::$field_separator . 'Shoes',
			WeForms_Notification::replace_field_tags( '{product:items}', $entry_id )
		);
	}

	/**
	 * Rows with a quantity below one, or a name that is not an option, are skipped.
	 *
	 * @return void
	 */
	public function test_product_tag_skips_zero_quantity_and_unknown_products() {
		$entry_id = $this->create_entry(
			array(
				'items' => array(
					array(
						'product'  => 'T-Shirt',
						'quantity' => 0,
					),
					array(
						'product'  => 'Hacked <b>Product</b>',
						'quantity' => 3,
					),
					array(
						'product'  => 'Sweatshirt',
						'quantity' => '2',
					),
				),
			)
		);

		$this->assertSame( 'Sweatshirt', WeForms_Notification::replace_field_tags( '{product:items}', $entry_id ) );
	}

	/**
	 * A row without a quantity is still listed when its name is an option.
	 *
	 * @return void
	 */
	public function test_product_tag_row_without_quantity_is_listed() {
		$entry_id = $this->create_entry(
			array(
				'items' => array(
					array(
						'product' => 'Shoes',
					),
				),
			)
		);

		$this->assertSame( 'Shoes', WeForms_Notification::replace_field_tags( '{product:items}', $entry_id ) );
	}

	/**
	 * An unknown product is never mapped to the first option.
	 *
	 * @return void
	 */
	public function test_product_tag_unknown_product_does_not_fall_back_to_first_option() {
		$entry_id = $this->create_entry(
			array(
				'items' => array(
					array(
						'product'  => 'Does Not Exist',
						'quantity' => 1,
					),
				),
			)
		);

		$this->assertSame( '[]', WeForms_Notification::replace_field_tags( '[{product:items}]', $entry_id ) );
	}

	/**
	 * Missing fields, missing options, and non-array values render an empty string without warnings.
	 *
	 * @return void
	 */
	public function test_product_tag_handles_bad_input() {
		$entry_id = $this->create_entry(
			array(
				'items'    => 'not-an-array',
				'toppings' => array( 'Cheese' ),
			)
		);

		// Non-array value.
		$this->assertSame( '[]', WeForms_Notification::replace_field_tags( '[{product:items}]', $entry_id ) );
		// Field that does not exist on the form.
		$this->assertSame( '[]', WeForms_Notification::replace_field_tags( '[{product:missing}]', $entry_id ) );
		// Field without a product option list (a checkbox's options do not match a stored row).
		$this->assertSame( '[]', WeForms_Notification::replace_field_tags( '[{product:toppings}]', $entry_id ) );
		// Entry that does not exist.
		$this->assertSame( '[]', WeForms_Notification::replace_field_tags( '[{product:items}]', 999999 ) );
	}

	/**
	 * Entries of a different shape in the product rows are ignored.
	 *
	 * @return void
	 */
	public function test_product_tag_ignores_malformed_rows() {
		$entry_id = $this->create_entry(
			array(
				'items' => array(
					'T-Shirt',
					array( 'quantity' => 1 ),
					array(
						'product'  => array( 'T-Shirt' ),
						'quantity' => 1,
					),
					array(
						'product'  => 'Shoes',
						'quantity' => 1,
					),
				),
			)
		);

		$this->assertSame( 'Shoes', WeForms_Notification::replace_field_tags( '{product:items}', $entry_id ) );
	}

	/**
	 * The replace-field-tags filter receives the replaced text and entry ID.
	 *
	 * @return void
	 */
	public function test_replace_field_tags_filter_runs() {
		$entry_id = $this->create_entry(
			array(
				'toppings' => 'Cheese',
			)
		);

		$seen = array();

		add_filter(
			'weforms_notification_replace_field_tags',
			static function ( $text, $filtered_entry_id ) use ( &$seen ) {
				$seen = array( $text, $filtered_entry_id );

				return $text . '!';
			},
			10,
			2
		);

		$this->assertSame( 'Cheese!', WeForms_Notification::replace_field_tags( '{field:toppings}', $entry_id ) );
		$this->assertSame( array( 'Cheese', $entry_id ), $seen );
	}
}
