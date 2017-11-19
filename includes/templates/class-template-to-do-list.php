<?php

/**
 * Blank form template
 */
class WeForms_Template_Todo_List extends WeForms_Form_Template {

	public function __construct() {
		parent::__construct();

		$this->enabled = class_exists('WeForms_Pro');
		$this->title = __('Todo List Form', 'weforms');
		$this->description = __('Get to do reminders with this online form to remember and track your to do list with the help of your form users.', 'weforms');
		$this->image = WEFORMS_ASSET_URI . '/images/form-template/to-do-list.png';
        $this->category    = 'others';
	}

	/**
	 * Get the form fields
	 *
	 * @return array
	 */
	public function get_form_fields() {

		$all_fields = $this->get_available_fields();

		$form_fields = array(

			array_merge($all_fields['text_field']->get_field_props(), array(
				'required' => 'yes',
				'label'    => __('Item Name', 'weforms'),
				'name'     => 'item_name',
			)),

			array_merge($all_fields['textarea_field']->get_field_props(), array(
				'label'		=> __('Additional Notes', 'weforms'),
				'name'		=> 'additional_notes',
			)),

			array_merge($all_fields['date_field']->get_field_props(), array(
				'required' => 'yes',
				'label'    => __('Due Date', 'weforms'),
				'name'     => 'due_date',
			)),

			array_merge($all_fields['dropdown_field']->get_field_props(), array(
				'required' => 'yes',
				'label'    => __('Priority', 'weforms'),
				'name'     => 'priority',
				'options'  => array(
					'normal'	=> 'Normal',
					'urgent'	=> 'Urgent',
				),
				'selected' => 'normal',
			)),
		);

		return $form_fields;
	}

	/**
	 * Get default settings
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array(
			'redirect_to'	=> 'same',
			'message'		=> __('Thanks for contacting us! We will get in touch with you shortly.', 'weforms'),
			'page_id'		=> '',
			'url'			=> '',
			'submit_text'	=> __('Submit', 'weforms'),
		);
	}

}
