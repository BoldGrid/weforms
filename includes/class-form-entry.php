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
     * Form fields raw data
     *
     * @var array
     */
    public $raw_fields = array();

    /**
     * The constructor
     *
     * @param integer       $entry_id
     * @param \WeForms_Form $form
     */
    function __construct( $entry_id, $form ) {
        $this->id   = $entry_id;
        $this->form = $form;

        $this->populate_entry_data();
    }

    /**
     *
     * Populate the class with data
     *
     * @TODO: Abstract this
     *
     * @return void
     */
    public function populate_entry_data() {
        global $wpdb;

        // return if we populated the already, ensures single db call
        if ( $this->form_id ) {
            return;
        }

        $grid_css_added = false;
        $grid_css       = '<style>.wpufTable {display: table; width: 100%; } .wpufTableRow {display: table-row; } .wpufTableRow:nth-child(even) {background-color: #f5f5f5; } .wpufTableHeading {background-color: #eee; display: table-header-group; font-weight: bold; } .wpufTableCell, .wpufTableHead {border: none; display: table-cell; padding: 3px 10px; } .wpufTableFoot {background-color: #eee; display: table-footer-group; font-weight: bold; } .wpufTableBody {display: table-row-group; }</style>';

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
            $this->raw_fields = $this->fields;

            foreach ( $results as $result ) {

                if ( array_key_exists( $result->meta_key, $this->fields ) ) {

                    $field = $this->fields[ $result->meta_key ];
                    $value = $result->meta_value;

                    $this->raw_fields[ $result->meta_key ]['value'] = $value;

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

                            foreach ( $value as $attachment_id ) {

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

                        $value = array(
							'lat' => $lat,
							'long' => $long
						);

                    } elseif ( $field['type'] == 'multiple_product' ) {

                        $field_value = unserialize( $value );

                        $serialized_value = array();

                        if ( is_array( $field_value ) ) {

                            foreach ( $field_value as $key => $sfv ) {

                                if ( is_array( $sfv ) ) {

                                    $v = array();

                                    foreach ( $sfv as $key => $sv ) {

                                        $sv = str_replace( array( '_', '-' ), ' ', $key ) . ': ' . $sv;
                                        $sv = ucwords( $sv );
                                        $v[] = $sv;
                                    }

                                    $serialized_value[] = implode( '<br> ', $v );

                                }
                            }

                            $value = implode( '<br> <br> ', $serialized_value );
                        }
                    } elseif ( $field['type'] == 'checkbox_grid' ) {

                        $entry_value = unserialize( $value );

                        if ( $entry_value ) {

                            $return = '';
                            $check = '';

                            if ( ! $grid_css_added ) {
                                $return = $grid_css;
                                $grid_css_added = true;
                            }

                            $new_val = array();

                            foreach ( $entry_value as $key => $option_value ) {
                                $new_val[ $key ] = $option_value;
                            }

                            if ( $field['grid_rows'] && count( $field['grid_rows'] ) > 0 && $field['grid_columns'] && count( $field['grid_columns'] ) > 0 ) {

                                $return .= '<div class="wpufTable">
                                    <div class="wpufTableHeading">
                                        <div class="wpufTableRow">
                                            <div class="wpufTableHead">&nbsp;</div>';

								foreach ( $field['grid_columns'] as $column ) {
									$return .= '<div class="wpufTableHead">' . $column . '</div>';
								}

                                        $return .= '</div>
                                    </div>
                                    <div class="wpufTableBody">';

								foreach ( $field['grid_rows'] as $row_key => $row_value ) {

									$return .= '<div class="wpufTableRow">
                                                <div class="wpufTableHead">' . $row_value . '</div>';

									foreach ( $field['grid_columns'] as $column_key => $column_value ) {
										if ( isset( $new_val[ $row_key ] ) ) {
											$check = ( in_array( $column_value, $new_val[ $row_key ] ) ) ? 'checked ' : '';
										}

										$return .= '<div class="wpufTableCell">
                                                        <label class="wpuf-radio-inline">
                                                        <input
                                                            name="' . $field['name'] . '[' . $row_key . '][]"
                                                            class="wpuf_' . $field['name'] . '_' . $this->form_id . '"
                                                            type="checkbox"
                                                            value="' . esc_attr( $column_value ) . '"'
												. $check . 'disabled
                                                        />
                                                    </label>
                                                    </div>';

									}

									$return .= '</div>';

								}

                                    $return .= '</div>
                                </div>';

                            }

                            $value = $return;
                        }
                    } elseif ( $field['type'] == 'multiple_choice_grid' ) {

                        $entry_value = unserialize( $value );

                        if ( $entry_value ) {

                            $return = '';
                            $check = '';

                            if ( ! $grid_css_added ) {
                                $return = $grid_css;
                                $grid_css_added = true;
                            }

                            $new_val = array();

                            foreach ( $entry_value as $key => $option_value ) {
                                $new_val[ $key ] = $option_value;
                            }

                            if ( $field['grid_rows'] && count( $field['grid_rows'] ) > 0 && $field['grid_columns'] && count( $field['grid_columns'] ) > 0 ) {

                                $return .= '<div class="wpufTable">
                                    <div class="wpufTableHeading">
                                        <div class="wpufTableRow">
                                            <div class="wpufTableHead">&nbsp;</div>';

								foreach ( $field['grid_columns'] as $column ) {
									$return .= '<div class="wpufTableHead">' . $column . '</div>';
								}

                                        $return .= '</div>
                                    </div>
                                    <div class="wpufTableBody">';

								foreach ( $field['grid_rows'] as $row_key => $row_value ) {

									$return .= '<div class="wpufTableRow">
                                                <div class="wpufTableHead">' . $row_value . '</div>';

									foreach ( $field['grid_columns'] as $column_key => $column_value ) {
										if ( isset( $new_val[ $row_key ] ) ) {
											$check = ( $new_val[ $row_key ] == $column_value ) ? 'checked ' : '';
										}

										$return .= '<div class="wpufTableCell">
                                                        <label class="wpuf-radio-inline">
                                                        <input
                                                            name="' . $field['name'] . '[' . $row_key . ']"
                                                            class="wpuf_' . $field['name'] . '_' . $this->form_id . '"
                                                            type="radio"
                                                            value="' . esc_attr( $column_value ) . '"'
												. $check . 'disabled
                                                        />
                                                    </label>
                                                    </div>';

									}

									$return .= '</div>';

								}

                                    $return .= '</div>
                                </div>';

                            }

                            $value = $return;
                        }
					} elseif ( $field['type'] == 'address_field' || is_serialized( $value ) ) {

                        $field_value = unserialize( $value );

                        $serialized_value = array();

                        if ( is_array( $field_value ) ) {

                            foreach ( $field_value as $key => $sfv ) {

                                $sfv = str_replace( array( '_', '-' ), ' ', $key ) . ': ' . $sfv;
                                $sfv = ucwords( $sfv );
                                $serialized_value[] = $sfv;
                            }

                            $value = implode( '<br> ', $serialized_value );
                        }
                    } elseif ( $field['type'] == 'signature_field' ) {
                        $url = content_url() . $value;
                        $value = $url;

                        if ( $_REQUEST['action'] != 'weforms_pdf_download' ) {
                            $value = sprintf( '<img src="%s">', $url );
                            $value .= sprintf( '<a style="margin-left: -200px" href="%s">Download</a>', $url );
                        }

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
     * Get entry fields
     *
     * @return array
     */
    public function get_raw_fields() {
        return $this->raw_fields;
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

    /**
     * Get entry metadata
     *
     * @return array
     */
    public function get_payment_data() {
        global $wpdb;

        if ( ! class_exists( 'WeForms_Payment' ) ) {
            return;
        }

        return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}weforms_payments WHERE entry_id = {$this->id} " );
    }

}
