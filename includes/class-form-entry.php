<?php

/**
 * Single Form Entry Class
 *
 * @since 1.1.0
 */
class WeForms_Form_Entry {

    /**
     * The form object
     *
     * @var \WeForms_Form
     */
    private $form;

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
     * The device of the user
     *
     * @var string
     */
    public $device = '';

    /**
     * Referer URL
     *
     * @var string
     */
    public $referer = '';

    /**
     * Entry creation date
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * Form fields
     *
     * @var array
     */
    public $fields = array();

    /**
     * The constructor
     *
     * @param integer $entry_id
     * @param \WeForms_Form $form
     */
    function __construct( $entry_id, $form ) {
        $this->id   = $entry_id;
        $this->form = $form;

        $this->populate_entry_data();
    }

    /**
     * Populate the class with data
     *
     * @return void
     */
    public function populate_entry_data() {
        global $wpdb;

        // return if we populated the already, ensures single db call
        if ( $this->form_id ) {
            return;
        }

        $values = array();

        $query = "SELECT * FROM {$wpdb->weforms_entries} as entry
                LEFT JOIN {$wpdb->weforms_entrymeta} AS meta ON entry.id = meta.weforms_entry_id
                WHERE entry.id = {$this->id}";

        $results = $wpdb->get_results( $query );

        if ( $results ) {
            $first_row = reset( $results );

            $this->form_id    = (int) $first_row->form_id;
            $this->user_id    = (int) $first_row->user_id;
            $this->ip_address = long2ip( $first_row->user_ip );
            $this->device     = $first_row->user_device;
            $this->referer    = $first_row->referer;
            $this->created    = $first_row->created_at;

            $this->fields     = $this->form->get_field_values();

            foreach ($results as $result) {

                if ( array_key_exists( $result->meta_key, $this->fields ) ) {

                    $field = $this->fields[ $result->meta_key ];
                    $value = $result->meta_value;

                    if ( $field['type'] == 'textarea_field' ) {

                        $value = weforms_format_text( $value );

                    } elseif ( $field['type'] == 'name_field' ) {

                        $value = implode( ' ', explode( WeForms::$field_separator, $value ) );

                    } elseif ( in_array( $field['type'], array( 'dropdown_field', 'radio_field' ) ) ) {

                        if ( isset( $field['options'] ) && $field['options'] ) {

                            if ( isset( $field['options'][ $value ] ) ) {
                                $value = $field['options'][ $value ];
                            }
                        }

                    } elseif ( in_array( $field['type'], array( 'multiple_select', 'checkbox_field' ) ) ) {
                        $value      = explode( WeForms::$field_separator, $value );
                        $temp_value = $value;

                        if ( is_array( $value ) && $value ) {

                            $new_array = array();

                            foreach ( $value as $option_key ) {
                                if ( is_array( $field['options'] ) && array_key_exists( $option_key, $field['options'] ) ) {
                                    $new_array[] = $field['options'][ $option_key ];
                                } else {
                                    $new_array[] = $option_key;
                                }
                            }

                            $value = $new_array;
                        }


                    } elseif ( in_array( $field['type'], array( 'image_upload', 'file_upload' ) ) ) {

                        $file_field = '';
                        $value      = maybe_unserialize( $value );

                        if ( is_array( $value ) && $value ) {

                            foreach ($value as $attachment_id) {

                                if ( $field['type'] == 'image_upload' ) {
                                    $thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                                } else {
                                    $thumb = get_post_field( 'post_title', $attachment_id );
                                }

                                $full_size = wp_get_attachment_url( $attachment_id );

                                $file_field .= sprintf( '<a href="%s" target="_blank">%s</a> ', $full_size, $thumb );
                            }
                        }

                        $value = $file_field;

                    } elseif ( $field['type'] == 'google_map' ) {
                        list( $lat, $long ) = explode( ',', $value );

                        $value = array( 'lat' => $lat, 'long' => $long );
                    }

                    $this->fields[ $result->meta_key ]['value'] = apply_filters( 'weforms_entry_meta_field', $value, $field );
                }
            }
        }
    }

    /**
     * Get entry fields
     *
     * @return array
     */
    public function get_fields() {
        return $this->fields;
    }

    /**
     * Get entry metadata
     *
     * @return array
     */
    public function get_metadata() {
        return array(
            'id'         => $this->id,
            'form_id'    => $this->form_id,
            'form_title' => $this->form->get_name(),
            'user'       => $this->user_id ? get_user_by( 'id', $this->user_id )->display_name : false,
            'ip_address' => $this->ip_address,
            'device'     => $this->device,
            'referer'    => $this->referer,
            'created'    => date_i18n( 'F j, Y g:i a', strtotime( $this->created ) )
        );
    }

}
