<?php

/**
 * Blank form template
 */
class WeForms_Template_My_Directory_Information extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'My Directory Information', 'weforms' );
        $this->description = __( 'You can do far more. Earn more clients and grow your business.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/my_directory.png';
        $this->category    = 'others';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {

        $all_fields  = $this->get_available_fields();
        $form_fields = array(

            array_merge( $all_fields['name_field']->get_field_props(), array(
                'required'   => 'yes',
                'format'     => 'first-last',

                'first_name' => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'First', 'weforms' )
                ),

                'last_name'       => array(
                    'placeholder' => '',
                    'default'     => '',
                    'sub'         => __( 'Last', 'weforms' )
                ),
                'hide_subs'       => false,
                'name'            => 'format',
            ) ),

            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => 'Username',
                'name'       => 'username',
            ) ),

            array_merge( $all_fields['website_url']->get_field_props(), array(
                'required'   => 'yes',
                'label'      => 'Website',
                'name'       => 'website',
            ) ),
        );

        return $form_fields;
    }

}
