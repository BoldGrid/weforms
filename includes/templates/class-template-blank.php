<?php

/**
 * Blank form template
 */
class WeForms_Template_Blank extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Blank Form', 'weforms' );
        $this->description = __( 'Start from a blank slate', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/contact.png';
        $this->category       = 'default';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        return __return_empty_array();
    }

}
