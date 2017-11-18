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
     * @var integer
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
     * @param integer       $form_id
     * @param \WeForms_Form $form
     */
    function __construct( $form_id, $form ) {
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
     * @param  integer $entry_id
     *
     * @return mixed
     */
    public function get( $entry_id ) {
        return new WeForms_Form_Entry( $entry_id, $this->form );
    }
}
