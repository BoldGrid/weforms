<?php

/**
 * Blank form template
 */
class WeForms_Template_Leave_Request extends WeForms_Form_Template {

    public function __construct() {
        parent::__construct();

        $this->enabled     = true;
        $this->title       = __( 'Request for Leave', 'weforms' );
        $this->description = __( 'Get an instant leave request from your employees with this easy to fill-out a request form and get details without any conflicts.', 'weforms' );
        $this->image       = WEFORMS_ASSET_URI . '/images/form-template/leave_request.png';
        $this->category    = 'employment';
    }

    /**
     * Get the form fields
     *
     * @return array
     */
    public function get_form_fields() {
        $all_fields    = $this->get_available_fields();

        $form_fields   = array(

            array_merge($all_fields['name_field']->get_field_props(), array(
                'requied'   => 'yes',
                'label'     => __('Name', 'weforms'),
                'format'    => 'first-last',
                'name'      => 'format',
            )),

            array_merge($all_fields['numeric_text_field']->get_field_props(), array(
                'requied'   => 'yes',
                'label'     => __('Employee ID', 'weforms'),
                'name'      => 'employee_id',
            )),

            array_merge($all_fields['numeric_text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Phone Number', 'weforms'),
                'name'      => 'phone_number',
            )),

            array_merge($all_fields['text_field']->get_field_props(), array(
                'label'     => __('Position', 'weforms'),
                'name'      => 'position',
            )),

            array_merge($all_fields['text_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Manager', 'weforms'),
                'name'      => 'manager',
            )),

            array_merge($all_fields['date_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Leave Start', 'weforms'),
                'name'      => 'leave_start',
            )),

            array_merge($all_fields['date_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Leave End', 'weforms'),
                'name'      => 'leave_end',
            )),

            array_merge($all_fields['radio_field']->get_field_props(), array(
                'required'  => 'yes',
                'label'     => __('Leave Type', 'weforms'),
                'name'      => 'leave_type',
                'options'   => array(
                    'vacation'  => 'Vacation',
                    'sick'      => 'Sick',
                    'quitting'  => 'Quitting',
                    'other'     => 'Other',
                ),
            )),

            array_merge($all_fields['textarea_field']->get_field_props(), array(
                'label'     => __('Comments', 'weforms'),
                'name'      => 'comment',
            )),
        );

        return $form_fields;
    }

}
