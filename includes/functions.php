<?php

/**
 * Get a single form
 *
 * @since 1.0.3
 *
 * @param  int $form_id
 *
 * @return false|WP_Post
 */
function weform_get_form( $form_id ) {
    return weforms()->form->get( $form_id );
}

/**
 * Get contact form templates
 *
 * @return array
 */
function weforms_get_form_template_categories() {

    $categories    = array(
        'default'      => array(
            'name'         => 'Default Templates',
            'icon'         => 'fa fa-bars',
        ),
        'registration' => array(
            'name'         => 'Registration Templates',
            'icon'         => 'fa fa-user-plus',
        ),
        'application'        => array(
            'name'         => 'Application Templates',
            'icon'         => 'fa fa-address-card-o',
        ),
        'request'        => array(
            'name'         => 'Request Templates',
            'icon'         => 'fa fa-hand-paper-o',
        ),
        'event'        => array(
            'name'         => 'Event Templates',
            'icon'         => 'fa fa-calendar',
        ),
        'feedback'        => array(
            'name'         => 'Feedback Templates',
            'icon'         => 'fa fa-comments',
        ),
        'employment'        => array(
            'name'         => 'Employment Templates',
            'icon'         => 'fa fa-suitcase',
        ),
        'payment'           =>  array(
            'name'         =>  'Payment Templates',
            'icon'         =>   'fa fa-money',
        ),
        'reservation'       =>  array(
            'name'         =>   'Reservation Templates',
            'icon'         =>   'fa fa-bandcamp',
        ),
        'others'        => array(
            'name'         => 'Others Templates',
            'icon'         => 'fa fa-info-circle',
        ),
    );

    return apply_filters( 'weforms_form_template_categories', $categories );
}

/**
 * Get entries by a form_id
 *
 * @param  int   $form_id
 * @param  array $args
 *
 * @return Object
 */
function weforms_get_form_entries( $form_id, $args = array() ) {
    global $wpdb;

    $defaults = array(
        'number'  => 10,
        'offset'  => 0,
        'orderby' => 'created_at',
        'status'  => 'publish',
        'order'   => 'DESC',
    );

    $r = wp_parse_args( $args, $defaults );

    $query = 'SELECT id, form_id, user_id, INET_NTOA( user_ip ) as ip_address, created_at
            FROM ' . $wpdb->weforms_entries .
            ' WHERE form_id = ' . $form_id . ' AND status = \'' . $r['status'] . '\'' .
            ' ORDER BY ' . $r['orderby'] . ' ' . $r['order'];

    if ( ! empty( $r['offset'] ) && ! empty( $r['number'] ) ) {
        $query .= ' LIMIT ' . $r['offset'] . ', ' . $r['number'];
    }

    $results = $wpdb->get_results( $query );

    return $results;
}

/**
 * Get payments by a form_id
 *
 * @param  int   $form_id
 * @param  array $args
 *
 * @return Object
 */
function weforms_get_form_payments( $form_id, $args = array() ) {
    global $wpdb;

    $defaults = array(
        'number'  => 10,
        'offset'  => 0,
        'orderby' => 'created_at',
        'order'   => 'DESC',
    );

    $r = wp_parse_args( $args, $defaults );

    $query = 'SELECT * FROM ' . $wpdb->prefix . 'weforms_payments' .
            ' WHERE form_id = ' . $form_id .
            ' ORDER BY ' . $r['orderby'] . ' ' . $r['order'] .
            ' LIMIT ' . $r['offset'] . ', ' . $r['number'];

    $results = $wpdb->get_results( $query );

    return $results;
}

/**
 * Get an entry by id
 *
 * @param  int $entry_id
 *
 * @return Object
 */
function weforms_get_entry( $entry_id ) {
    global $wpdb;

    $cache_key = 'weforms-entry-' . $entry_id;
    $entry     = wp_cache_get( $cache_key, 'weforms' );

    if ( false === $entry ) {
        $query = 'SELECT id, form_id, user_id, user_device, referer, INET_NTOA( user_ip ) as ip_address, created_at
            FROM ' . $wpdb->weforms_entries . '
            WHERE id = %d';

        $entry = $wpdb->get_row( $wpdb->prepare( $query, $entry_id ) );
        wp_cache_set( $cache_key, $entry );
    }

    return $entry;
}

/**
 * Insert a new entry
 *
 * @param  array $args
 * @param  array $fields
 *
 * @return WP_Error|integer
 */
function weforms_insert_entry( $args, $fields = array() ) {
    global $wpdb;

    $browser = weforms_get_browser();

    $defaults = array(
        'form_id'     => 0,
        'user_id'     => get_current_user_id(),
        'user_ip'     => ip2long( weforms_get_client_ip() ),
        'user_device' => $browser['name'] . '/' . $browser['platform'],
        'referer'     => $_SERVER['HTTP_REFERER'],
        'created_at'  => current_time( 'mysql' )
    );

    $r = wp_parse_args( $args, $defaults );

    if ( ! $r['form_id'] ) {
        return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'weforms' ) );
    }

    if ( ! $fields ) {
        return new WP_Error( 'no-fields', __( 'No form fields were found.', 'weforms' ) );
    }

    $success = $wpdb->insert( $wpdb->weforms_entries, $r );

    if ( is_wp_error( $success ) || ! $success ) {
        return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'weforms' ) );
    }

    $entry_id = $wpdb->insert_id;

    foreach ( $fields as $key => $value ) {
        weforms_add_entry_meta( $entry_id, $key, $value );
    }

    return $entry_id;
}

/**
 * Change an entry status
 *
 * @param  int    $entry_id
 * @param  string $status
 *
 * @return int|boolean
 */
function weforms_change_entry_status( $entry_id, $status ) {
    global $wpdb;

    return $wpdb->update(
        $wpdb->weforms_entries,
        array(
            'status' => $status
        ),
        array(
            'id' => $entry_id
        ),
        array( '%s' ),
        array( '%d' )
    );
}

/**
 * Delete an entry
 *
 * @param  int $entry_id
 *
 * @return int|boolean
 */
function weforms_delete_entry( $entry_id ) {
    global $wpdb;

    $deleted = $wpdb->delete(
        $wpdb->weforms_entries, array(
            'id' => $entry_id
        ), array( '%d' )
    );

    if ( $deleted ) {
        $wpdb->delete(
            $wpdb->weforms_entrymeta, array(
                'weforms_entry_id' => $entry_id
            ), array( '%d' )
        );
    }

    return $deleted;
}

/**
 * Add meta data field to an entry.
 *
 * @param int    $entry_id   Entry ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function weforms_add_entry_meta( $entry_id, $meta_key, $meta_value, $unique = false ) {
    return add_metadata( 'weforms_entry', $entry_id, $meta_key, $meta_value, $unique );
}

/**
 * Retrieve entry meta field for a entry.
 *
 * @param int    $entry_id Entry ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns
 *                        data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value. Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data
 *               field if $single is true.
 */
function weforms_get_entry_meta( $entry_id, $key = '', $single = false ) {
    return get_metadata( 'weforms_entry', $entry_id, $key, $single );
}

/**
 * Update entry meta field based on entry ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and entry ID.
 *
 * If the meta field for the entry does not exist, it will be added.
 *
 * @param int    $entry_id   entry ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure.
 */
function weforms_update_entry_meta( $entry_id, $meta_key, $meta_value, $prev_value = '' ) {
    return update_metadata( 'weforms_entry', $entry_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Delete everything from entry meta matching meta key.
 *
 * @param string $entry_meta_key Key to search for when deleting.
 * @return bool Whether the entry meta key was deleted from the database.
 */
function weforms_delete_entry_meta_by_key( $meta_key ) {
    return delete_metadata( 'weforms_entry', null, $meta_key, '', true );
}

/**
 * Retrieve entry meta fields, based on entry ID.
 *
 * The entry meta fields are retrieved from the cache where possible,
 * so the function is optimized to be called more than once.
 *
 * @since 1.2.0
 *
 * @param int $entry_id Optional.
 * @return array entry meta for the given entry.
 */
function weforms_get_entry_custom( $entry_id = 0 ) {
    $entry_id = absint( $entry_id );

    return weforms_get_entry_meta( $entry_id );
}

/**
 * Retrieve meta field names for a entry.
 *
 * If there are no meta fields, then nothing (null) will be returned.
 *
 * @param int $entry_id Optional.
 * @return array|void Array of the keys, if retrieved.
 */
function weforms_get_entry_custom_keys( $entry_id = 0 ) {
    $custom = weforms_get_entry_custom( $entry_id );

    if ( ! is_array( $custom ) ) {
        return false;
    }

    if ( $keys = array_keys( $custom ) ) {
        return $keys;
    }
}

/**
 * Get the number of entries count on a form
 *
 * @param  int $form_id
 *
 * @return int
 */
function weforms_count_form_entries( $form_id, $status = 'publish' ) {
    global $wpdb;

    return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT count(id) FROM ' . $wpdb->weforms_entries . ' WHERE form_id = %d AND status = %s', $form_id, $status ) );
}

/**
 * Get the number of entries count on a form
 *
 * @param  int $form_id
 *
 * @return int
 */
function weforms_count_form_payments( $form_id ) {
    global $wpdb;

    if ( ! class_exists( 'WeForms_Payment' ) ) {
        return 0;
    }

    return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT count(id) FROM ' . $wpdb->prefix . 'weforms_payments' . ' WHERE form_id = %d', $form_id ) );
}

/**
 * Get table column heads for a form
 *
 * For now, return only text type fields
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_entry_columns( $form_id, $limit = 6 ) {
    $fields  = weforms()->form->get( $form_id )->get_fields();
    $columns = array();

    // filter by input types
    if ( $limit ) {

        $fields = array_filter( $fields, function( $item ) {
            return in_array( $item['template'], array( 'text_field', 'name_field', 'dropdown_field', 'radio_field', 'email_address', 'url_field' ) );
        });
    }

    if ( $fields ) {
        foreach ( $fields as $field ) {
            $columns[ $field['name'] ] = $field['label'];
        }
    }

    // if passed 0/false, return all collumns
    if ( $limit && sizeof( $columns ) > $limit ) {
        $columns = array_slice( $columns, 0, $limit ); // max 6 columns
    }

    return apply_filters( 'weforms_get_entry_columns', $columns, $form_id );
}

/**
 * Get table column heads for a form
 *
 * For now, return only text type fields
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_payment_columns( $form_id, $limit = 6 ) {
    $fields  = weforms()->form->get( $form_id )->get_fields();
    $columns = array();

    // filter by input types
    if ( $limit ) {

        $fields = array_filter( $fields, function( $item ) {
            return in_array( $item['template'], array( 'text_field', 'name_field', 'dropdown_field', 'radio_field', 'email_address', 'url_field' ) );
        });
    }

    if ( $fields ) {
        foreach ( $fields as $field ) {
            $columns[ $field['name'] ] = $field['label'];
        }
    }

    // if passed 0/false, return all collumns
    if ( $limit && sizeof( $columns ) > $limit ) {
        $columns = array_slice( $columns, 0, $limit ); // max 6 columns
    }

    return apply_filters( 'weforms_get_entry_columns', $columns, $form_id );
}

/**
 * Get fields from a form by it's meta key
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_form_field_labels( $form_id ) {
    $fields  = weforms()->form->get( $form_id )->get_fields();
    $exclude = array( 'step_start', 'section_break', 'recaptcha', 'shortcode', 'action_hook' );

    if ( ! $fields ) {
        return false;
    }

    $data = array();
    foreach ( $fields as $field ) {
        if ( empty( $field['name'] ) ) {
            continue;
        }

        // exclude the fields
        if ( in_array( $field['template'], $exclude ) ) {
            continue;
        }

        $data[ $field['name'] ] = array(
            'label' => $field['label'],
            'type'  => $field['template']
        );
    }

    return $data;
}

/**
 * Get data from an entry
 *
 * @param  int $entry_id
 * @param  int $form_id
 *
 * @return false|array
 */
function weforms_get_entry_data( $entry_id ) {
    $data   = array();
    $entry  = weforms_get_entry( $entry_id );
    $fields = weforms_get_form_field_labels( $entry->form_id );

    if ( ! $fields ) {
        return false;
    }

    foreach ( $fields as $meta_key => $field ) {
        $value = weforms_get_entry_meta( $entry_id, $meta_key, true );

        if ( $field['type'] == 'textarea' ) {

            $data[ $meta_key ] = weforms_format_text( $value );

        } elseif ( $field['type'] == 'name' ) {

            $data[ $meta_key ] = implode( ' ', explode( WeForms::$field_separator, $value ) );

        } elseif ( in_array( $field['type'], array( 'image_upload', 'file_upload' ) ) ) {

            $data[ $meta_key ] = '';

            if ( is_array( $value ) && $value ) {

                foreach ( $value as $attachment_id ) {

                    if ( $field['type'] == 'image_upload' ) {
                        $thumb = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                    } else {
                        $thumb = get_post_field( 'post_title', $attachment_id );
                    }

                    $full_size = wp_get_attachment_url( $attachment_id );

                    $data[ $meta_key ] .= sprintf( '<a href="%s" target="_blank">%s</a> ', $full_size, $thumb );
                }
            }
        } elseif ( in_array( $field['type'], array( 'checkbox', 'multiselect' ) ) ) {

            $data[ $meta_key ] = explode( WeForms::$field_separator, $value );

        } elseif ( $field['type'] == 'map' ) {

            list( $lat, $long ) = explode( ',', $value );

            $data[ $meta_key ] = array(
                'lat' => $lat,
                'long' => $long
            );

        } else {
            $data[ $meta_key ] = $value;
        }
    }

    return array(
        'fields' => $fields,
        'data'   => $data
    );
}

/**
 * Format a text and apply WP function callbacks
 *
 * @param  string $content
 *
 * @return string
 */
function weforms_format_text( $content ) {
    $content = wptexturize( $content );
    $content = convert_smilies( $content );
    $content = wpautop( $content );
    $content = make_clickable( $content );

    return $content;
}

/**
 * Get User Agent browser and OS type
 *
 * @return array
 */
function weforms_get_browser() {
    $u_agent  = $_SERVER['HTTP_USER_AGENT'];
    $bname    = 'Unknown';
    $platform = 'Unknown';
    $version  = '';

    // first get the platform
    if ( preg_match( '/linux/i', $u_agent ) ) {
        $platform = 'Linux';
    } elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
        $platform = 'MAC OS';
    } elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
        $platform = 'Windows';
    }

    // next get the name of the useragent yes seperately and for good reason
    if ( preg_match( '/MSIE/i',$u_agent ) && ! preg_match( '/Opera/i',$u_agent ) ) {
        $bname = 'Internet Explorer';
        $ub    = 'MSIE';
    } elseif ( preg_match( '/Trident/i',$u_agent ) ) {
        // this condition is for IE11
        $bname = 'Internet Explorer';
        $ub = 'rv';
    } elseif ( preg_match( '/Firefox/i',$u_agent ) ) {
        $bname = 'Mozilla Firefox';
        $ub = 'Firefox';
    } elseif ( preg_match( '/Chrome/i',$u_agent ) ) {
        $bname = 'Google Chrome';
        $ub = 'Chrome';
    } elseif ( preg_match( '/Safari/i',$u_agent ) ) {
        $bname = 'Apple Safari';
        $ub = 'Safari';
    } elseif ( preg_match( '/Opera/i',$u_agent ) ) {
        $bname = 'Opera';
        $ub = 'Opera';
    } elseif ( preg_match( '/Netscape/i',$u_agent ) ) {
        $bname = 'Netscape';
        $ub = 'Netscape';
    }

    // finally get the correct version number
    // Added "|:"
    $known = array( 'Version', $ub, 'other' );
    $pattern = '#(?<browser>' . join( '|', $known ) .
     ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count( $matches['browser'] );

    if ( $i != 1 ) {
        // we will have two since we are not using 'other' argument yet
        // see if version is before or after the name
        if ( strripos( $u_agent,'Version' ) < strripos( $u_agent,$ub ) ) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ( $version == null || $version == '' ) {
        $version = '';
    }

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'   => $pattern
    );
}

/**
 * Get form notification merge tags
 *
 * @since 1.0
 *
 * @return array
 */
function weforms_get_merge_tags() {
    $tags = array(
        'form' => array(
            'title' => __( 'Form', 'weforms' ),
            'tags'  => array(
                'entry_id'  => __( 'Entry ID', 'weforms' ),
                'form_id'   => __( 'Form ID', 'weforms' ),
                'form_name' => __( 'Form Name', 'weforms' )
            )
        ),
        'system' => array(
            'title' => __( 'System', 'weforms' ),
            'tags'  => array(
                'admin_email' => __( 'Site Administrator Email', 'weforms' ),
                'date'        => __( 'Date', 'weforms' ),
                'site_name'   => __( 'Site Title', 'weforms' ),
                'site_url'    => __( 'Site URL', 'weforms' ),
                'page_title'  => __( 'Embedded Page Title', 'weforms' ),
            )
        ),
        'user' => array(
            'title' => __( 'User', 'weforms' ),
            'tags'  => array(
                'ip_address'   => __( 'IP Address', 'weforms' ),
                'user_id'      => __( 'User ID', 'weforms' ),
                'first_name'   => __( 'First Name', 'weforms' ),
                'last_name'    => __( 'Last Name', 'weforms' ),
                'display_name' => __( 'Display Name', 'weforms' ),
                'user_email'   => __( 'Email', 'weforms' ),
            )
        ),
        'urls' => array(
            'title' => __( 'URL\'s', 'weforms' ),
            'tags'  => array(
                'url_page'          => __( 'Embeded Page URL', 'weforms' ),
                'url_referer'       => __( 'Referer URL', 'weforms' ),
                'url_login'         => __( 'Login URL', 'weforms' ),
                'url_logout'        => __( 'Logout URL', 'weforms' ),
                'url_register'      => __( 'Register URL', 'weforms' ),
                'url_lost_password' => __( 'Lost Password URL', 'weforms' ),
            )
        ),
    );

    return apply_filters( 'wpuf_cf_merge_tags', $tags );
}

/**
 * Record a form view count
 *
 * @param  int $form_id
 *
 * @return void
 */
function weforms_track_form_view( $form_id ) {
    // don't track administrators
    if ( current_user_can( 'administrator' ) ) {
        return;
    }

    // ability to turn this off if someone doesn't like this tracking
    $is_enabled = apply_filters( 'weforms_track_form_view', true );

    if ( ! $is_enabled ) {
        return;
    }

    // increase the count
    $meta_key = '_weforms_view_count';
    $number   = (int) get_post_meta( $form_id, $meta_key, true );

    update_post_meta( $form_id, $meta_key, ( $number + 1 ) );
}

/**
 * Get form view count of a form
 *
 * @param  int $form_id
 *
 * @return int
 */
function weforms_get_form_views( $form_id ) {
    return (int) get_post_meta( $form_id, '_weforms_view_count', true );
}

/**
 * Get the weForms global settings
 *
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function weforms_get_settings( $key = '', $default = '' ) {

    $settings = get_option( 'weforms_settings', array() );
    $settings = apply_filters( 'weforms_get_settings', $settings );

    if ( empty( $key ) ) {
        return $settings;
    }

    if ( isset( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }

    return $default;
}


/**
 * Update Settings
 *
 * @param array $updated_settings
 *
 * @return array
 */
function weforms_update_settings( $updated_settings = array() ) {

    $previuos_settings = weforms_get_settings();

    $settings = array_merge( $previuos_settings, $updated_settings );

    update_option( 'weforms_settings', $settings );

    return $settings;
}

/**
 * Form access capability for forms
 *
 * @since 1.0.5
 *
 * @return string
 */
function weforms_form_access_capability() {
    return apply_filters( 'weforms_form_access_capability', 'manage_options' );
}

/**
 * Form access capability for forms
 *
 * @since 1.2.4
 *
 * @return string
 */
function weforms_log_file_path() {
    return apply_filters( 'weforms_log_file_path', WP_CONTENT_DIR . '/weforms.log' );
}

/**
 * Clear the buffer
 *
 * prevents ajax breakage and endless loading icon. A LIFE SAVER!!!
 *
 * @since 1.1.0
 *
 * @return void
 */
function weforms_clear_buffer() {
    if ( ob_get_length() > 0 ) {
        ob_clean();
    }
}

/**
 * Get the client IP address
 *
 * @since 1.1.0
 *
 * @return string
 */
function weforms_get_client_ip() {
    $ipaddress = '';

    if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

/**
 * Save form fields
 *
 * @since 1.1.0
 *
 * @param int   $form_id
 * @param array $field
 * @param int   $field_id
 * @param int   $order
 *
 * @return int ID of updated or inserted post
 */
function weforms_insert_form_field( $form_id, $field = array(), $field_id = null, $order = 0 ) {

    $args = array(
        'post_type'    => 'wpuf_input',
        'post_parent'  => $form_id,
        'post_status'  => 'publish',
        'post_content' => maybe_serialize( wp_unslash( $field ) ),
        'menu_order'   => $order
    );

    if ( $field_id ) {
        $args['ID'] = $field_id;
    }

    if ( $field_id ) {
        return wp_update_post( $args );
    } else {
        return wp_insert_post( $args );
    }
}

/**
 * Allowed upload extensions
 *
 * @return array
 */
function weforms_allowed_extensions() {
    $extesions = array(
        'images' => array(
            'ext'   => 'jpg,jpeg,gif,png,bmp',
            'label' => __( 'Images', 'weforms' )
        ),
        'audio'  => array(
            'ext'   => 'mp3,wav,ogg,wma,mka,m4a,ra,mid,midi',
            'label' => __( 'Audio', 'weforms' )
        ),
        'video'  => array(
            'ext'   => 'avi,divx,flv,mov,ogv,mkv,mp4,m4v,divx,mpg,mpeg,mpe',
            'label' => __( 'Videos', 'weforms' )
        ),
        'pdf'    => array(
            'ext'   => 'pdf',
            'label' => __( 'PDF', 'weforms' )
        ),
        'office' => array(
            'ext'   => 'doc,ppt,pps,xls,mdb,docx,xlsx,pptx,odt,odp,ods,odg,odc,odb,odf,rtf,txt',
            'label' => __( 'Office Documents', 'weforms' )
        ),
        'zip'    => array(
            'ext'   => 'zip,gz,gzip,rar,7z',
            'label' => __( 'Zip Archives', 'weforms' )
        ),
        'exe'    => array(
            'ext'   => 'exe',
            'label' => __( 'Executable Files', 'weforms' )
        ),
        'csv'    => array(
            'ext'   => 'csv',
            'label' => __( 'CSV', 'weforms' )
        )
    );

    return apply_filters( 'weforms_allowed_extensions', $extesions );
}

/**
 * Get form integration settings
 *
 * @since 1.1.0
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_form_integrations( $form_id ) {
    $integrations = get_post_meta( $form_id, 'integrations', true );

    if ( ! $integrations ) {
        return array();
    }

    return $integrations;
}


/**
 * Check if an integration is active
 *
 * @since 1.1.0
 *
 * @param  int    $form_id
 * @param  string $integration_id
 *
 * @return boolean
 */
function weforms_is_integration_active( $form_id, $integration_id ) {
    $integrations = weforms_get_form_integrations( $form_id );

    if ( ! $integrations ) {
        return false;
    }

    foreach ( $integrations as $id => $integration ) {
        if ( $integration_id == $id && $integration->enabled == true ) {
            return $integration;
        }
    }

    return false;
}


/**
 * Get Flat UI Colors
 *
 * @return array
 */
function weforms_get_flat_ui_colors() {
    return array( '#1abc9c', '#2ecc71', '#3498db', '#9b59b6', '#34495e' );
}

/**
 * Get default form settings
 *
 * @return array
 */
function weforms_get_default_form_settings() {
    return apply_filters(
        'weforms_get_default_form_settings', array(
            'redirect_to'                => 'same',
            'message'                    => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'weforms' ),
            'page_id'                    => '',
            'url'                        => '',
            'submit_text'                => __( 'Submit Query', 'weforms' ),
            'schedule_form'              => 'false',
            'schedule_start'             => '',
            'schedule_end'               => '',
            'sc_pending_message'         => __( 'Form submission hasn\'t been started yet', 'weforms' ),
            'sc_expired_message'         => __( 'Form submission is now closed.', 'weforms' ),
            'require_login'              => 'false',
            'req_login_message'          => __( 'You need to login to submit a query.', 'weforms' ),
            'limit_entries'              => 'false',
            'limit_number'               => '100',
            'limit_message'              => __( 'Sorry, we have reached the maximum number of submissions.', 'weforms' ),
            'label_position'             => 'above',
            'use_theme_css'              => 'wpuf-style',
            'quiz_form'                  => 'no',
            'shuffle_question_order'     => 'no',
            'release_grade'              => 'after_submission',
            'respondent_can_see'         => array( 'missed_questions', 'correct_answers', 'point_values' ),
            'total_points'               => 0,
            'enable_multistep'           => false,
            'multistep_progressbar_type' => 'progressive',

            // payment
            'payment_paypal_images'      => 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg',

            'payment_paypal_label'       => __( 'PayPal', 'weforms' ),
            'payment_stripe_label'       => __( 'Credit Card', 'weforms' ),
            'payment_stripe_images'      => array( 'visa','mastercard','amex','discover' ),

            'payment_stripe_deactivate'  => '',
            'stripe_mode'                => 'live',
            'stripe_page_id'             => '',

            'stripe_override_keys'       => '',
            'stripe_email'               => '',
            'stripe_key'                 => '',
            'stripe_secret_key'          => '',
            'stripe_key_test'            => '',
            'stripe_secret_key_test'     => '',

            'stripe_prefill_email'       => '',
            'stripe_user_email_field'    => '',

            'payment_paypal_deactivate'  => '',
            'paypal_mode'                => 'live',
            'paypal_type'                => '_cart',

            'paypal_override'            => '',
            'paypal_email'               => '',

            'paypal_page_id'             => '',

            'paypal_prefill_email'       => '',
            'paypal_user_email_field'    => '',
        )
    );
}

/**
 * Get default form settings
 *
 * @return array
 */
function weforms_get_default_form_notification() {
    return apply_filters(
        'weforms_get_default_form_notification', array(
            'active'       => 'true',

            'type'         => 'email',
            'smsTo'        => '',
            'smsText'      => '[{form_name}] ' . __( 'New Form Submission', 'weforms' ) . ' #{entry_id}',

            'name'         => __( 'Admin Notification', 'weforms' ),
            'subject'      => '[{form_name}] ' . __( 'New Form Submission', 'weforms' ) . ' #{entry_id}',
            'to'           => '{admin_email}',
            'replyTo'      => '{field:email}',
            'message'      => '{all_fields}',
            'fromName'     => '{site_name}',
            'fromAddress'  => '{admin_email}',
            'cc'           => '',
            'bcc'          => '',
            'weforms_cond' => array(
                'condition_status' => 'no',
                'cond_logic'       => 'any',
                'conditions'       => array(
                    array(
                        'name'             => '',
                        'operator'         => '=',
                        'option'           => ''
                    )
                )
            )
        )
    );
}

/**
 * weforms_get_pain_text
 *
 * @param $value mixed
 *
 * @return string
 **/
function weforms_get_pain_text( $value ) {

    if ( is_serialized( $value ) ) {
        $value = unserialize( $value );
    }

    if ( is_array( $value ) ) {

        $string_value = array();

        if ( is_array( $value ) ) {

            foreach ( $value as $key => $single_value ) {

                if ( is_array( $single_value ) || is_serialized( $single_value ) ) {
                    $single_value = weforms_get_pain_text( $single_value );
                }

                $single_value = ucwords( str_replace( array( '_', '-' ), ' ', $key ) ) . ': ' . ucwords( $single_value );

                $string_value[] = $single_value;
            }

            $value = implode( WeForms::$field_separator , $string_value );
        }
    }

    $value = trim( strip_tags( $value ) );

    return $value;
}
