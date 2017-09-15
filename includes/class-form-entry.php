<?php

/**
 * Single Form Entry Class
 *
 * @since 1.1.0
 */
class WeForms_Form_Entry {

    /**
     * Entry id
     *
     * @var integer
     */
    public $id = 0;

    /**
     * The form id
     *
     * @var integer
     */
    public $form_id = 0;

    /**
     * The user id
     *
     * @var integer
     */
    public $user_id = 0;

    /**
     * IP Address
     *
     * @var string
     */
    public $ip_address = '127.0.0.1';

    /**
     * Entry creation date
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * The constructor
     *
     * @param integer $entry_id
     */
    function __construct( $entry_id ) {
        $this->id = $entry_id;

        $entry = weforms_get_entry( $entry_id );

        if ( $entry ) {
            $this->id         = $entry_id;
            $this->form_id    = (int) $entry->form_id;
            $this->user_id    = (int) $entry->user_id;
            $this->ip_address = $entry->ip_address;
            $this->created    = $entry->created_at;
        }
    }

}
