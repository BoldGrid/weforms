<?php

/**
 * Form field manager class
 *
 * @since 1.1.0
 */
class WeForms_Field_Manager {

    /**
     * Get all the registered fields
     *
     * @return array
     */
    public function get_fields() {

        require_once dirname( __FILE__ ) . '/fields/class-abstract-fields.php';
        require_once dirname( __FILE__ ) . '/fields/class-field-text.php';

        $fields = array(
            'text_field' => new WeForms_Form_Field_Text()
        );

        return apply_filters( 'weforms_form_fields', $fields );
    }
}