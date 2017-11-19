<?php
/**
 * A simple contact form for sending message
 */
class WeForms_Template_Comment_Rating extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled        = class_exists( 'WeForms_Pro' );
        $this->title          = __( 'Comment & Rating Form', 'weforms' );
        $this->description    = '';
        $this->category       = 'default';
        $this->image          = WEFORMS_ASSET_URI . '/images/form-template/comment-and-rating.png';
        $this->category    = 'feedback';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields = $this->get_available_fields();

        $form_fields = array(
            array_merge( $all_fields['ratings']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Rating',
                'name'     => 'rating',
            ) ),
            array_merge( $all_fields['textarea_field']->get_field_props(), array(
                'label'    => __( 'Comment', 'weforms' ),
                'name'     => 'comment',
            ) ),
            array_merge( $all_fields['text_field']->get_field_props(), array(
                'required' => 'yes',
                'label'    => 'Name',
                'name'     => 'name',
            ) ),
        );

        return $form_fields;
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_form_settings() {
        $defaults = $this->get_default_settings();

        return array_merge( $defaults, array(
            'message'           => __( 'Thanks for your rating!', 'weforms' ),
            'submit_text'       => __( 'Submit', 'weforms' ),
            'label_position'    => 'left',
        ) );
    }

}
