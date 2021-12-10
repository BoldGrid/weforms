<?php

/**
 * The Entry Manager Class
 *
 * @since 1.1.0
 */
class WeForms_Form_Entry_Manager {

	/**
	 * The form id
	 *
	 * @var int
	 */
	private $id = 0;

	/**
	 * The form object
	 *
	 * @var \WeForms_Form
	 */
	private $form;

	/**
	 * The constructor
	 *
	 * @param int           $form_id
	 * @param \WeForms_Form $form
	 */
	public function __construct( $form_id, $form ) {
		$this->id   = $form_id;
		$this->form = $form;
	}

	/**
	 * Get all the form entries
	 *
	 * @return array
	 */
	public function all() {
		return weforms_get_form_entries( $this->id );
	}

	/**
	 * Get a single entry
	 *
	 * @param int $entry_id
	 *
	 * @return mixed
	 */
	public function get( $entry_id ) {
		return new WeForms_Form_Entry( $entry_id, $this->form );
	}

	/**
	 * Format Entry Value.
	 *
	 * @param  array $field Form Field data.
	 *
	 * @return array $field Formatted field data values.
	 */
	public static function format_entry_value( $field ) {
		switch ( $field['template'] ) {

			case 'radio_field':
				$value          = array_search( $field['value'], $field['options'], true );
				$field['value'] = esc_html( 'Option: ' . $field['value'] . ' - ' . 'Value: ' . $value );

				break;

			case 'checkbox_field':
				$field_formatted = array();
				foreach ( $field['value'] as $option ) {
					$value             = array_search( $option, $field['options'], true );
					$field_formatted[] = esc_html( 'Option: ' . $option . ' - ' . 'Value: ' . $value );
				}
				$field['value'] = array_replace( $field['value'], $field_formatted );

				break;

			case 'multiple_select':
				$field_formatted = array();
				foreach ( $field['value'] as $option ) {
					$value             = array_search( $option, $field['options'], true );
					$field_formatted[] = esc_html( 'Option: ' . $option . ' - ' . 'Value: ' . $value );
				}
				$field['value'] = array_replace( $field['value'], $field_formatted );

				break;

			case 'dropdown_field':
				$value          = array_search( $field['value'], $field['options'], true );
				$field['value'] = esc_html( 'Option: ' . $field['value'] . ' - ' . 'Value: ' . $value );

				break;

			default:
				// Do nothing if value format does not need to be changed.
				break;
		}
		return $field;
	}
}
