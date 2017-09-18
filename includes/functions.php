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
    $form = get_post( $form_id );

    if ( ! $form ) {
        return false;
    }

    if ( $form->post_type != 'wpuf_contact_form' ) {
        return false;
    }

    return $form;
}

/**
 * Get contact form templates
 *
 * @return array
 */
function weforms_get_form_templates() {
    require_once WEFORMS_INCLUDES . '/admin/form-templates/contact-form.php';
    require_once WEFORMS_INCLUDES . '/admin/form-templates/support-form.php';
    require_once WEFORMS_INCLUDES . '/admin/form-templates/event-registration-form.php';

    $integrations = array(
        'WPUF_Contact_Form_Template_Contact'            => new WPUF_Contact_Form_Template_Contact(),
        'WPUF_Contact_Form_Template_Support'            => new WPUF_Contact_Form_Template_Support(),
        'WPUF_Contact_Form_Template_Event_Registration' => new WPUF_Contact_Form_Template_Event_Registration(),
    );

    return apply_filters( 'weforms_form_templates', $integrations );
}

/**
 * Get entries by a form_id
 *
 * @param  int $form_id
 * @param  array  $args
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
            ' WHERE form_id = ' . $form_id . ' AND status = \'' . $r['status'] . '\''.
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
 * @param  array  $fields
 *
 * @return WP_Error|integer
 */
function weforms_insert_entry( $args, $fields = array() ) {
    global $wpdb;

    $browser = weforms_get_browser();

    $defaults = array(
        'form_id'     => 0,
        'user_id'     => get_current_user_id(),
        'user_ip'     => ip2long( wpuf_get_client_ip() ),
        'user_device' => $browser['name'] . '/' . $browser['platform'],
        'referer'     => $_SERVER['HTTP_REFERER'],
        'created_at'  => current_time( 'mysql' )
    );

    $r = wp_parse_args( $args, $defaults );

    if ( !$r['form_id'] ) {
        return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'weforms' ) );
    }

    if ( !$fields ) {
        return new WP_Error( 'no-fields', __( 'No form fields were found.', 'weforms' ) );
    }

    $success = $wpdb->insert( $wpdb->weforms_entries, $r );

    if ( is_wp_error( $success ) || !$success ) {
        return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'weforms' ) );
    }

    $entry_id = $wpdb->insert_id;

    foreach ($fields as $key => $value) {
        weforms_add_entry_meta( $entry_id, $key, $value );
    }

    return $entry_id;
}

/**
 * Change an entry status
 *
 * @param  int $entry_id
 * @param  string $status
 *
 * @return int|boolean
 */
function weforms_change_entry_status( $entry_id, $status ) {
    global $wpdb;

    return $wpdb->update( $wpdb->weforms_entries,
        array( 'status' => $status ),
        array( 'id' => $entry_id ),
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

    return $wpdb->delete( $wpdb->weforms_entries, array( 'id' => $entry_id ), array( '%d' ) );
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
    return add_metadata( 'weforms_entry', $entry_id, $meta_key, $meta_value, $unique);
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
    return update_metadata( 'weforms_entry', $entry_id, $meta_key, $meta_value, $prev_value);
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

    if ( !is_array( $custom ) ) {
        return false;
    }

    if ( $keys = array_keys($custom) ) {
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
 * Get table column heads for a form
 *
 * For now, return only text type fields
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_entry_columns( $form_id, $limit = 6 ) {
    $fields  = wpuf_get_form_fields( $form_id );
    $columns = array();

    // filter by input types
    if ( $limit ) {

        $fields = array_filter( $fields, function($item) {
            return in_array( $item['input_type'], array( 'text', 'name', 'select', 'radio', 'email', 'url' ) );
        } );
    }

    if ( $fields ) {
        foreach ($fields as $field) {
            $columns[ $field['name'] ] = $field['label'];
        }
    }

    // if passed 0/false, return all collumns
    if ( ! $limit ) {
        return $columns;
    }

    return array_slice( $columns, 0, 6 ); // max 6 columns
}

/**
 * Get fields from a form by it's meta key
 *
 * @param  int $form_id
 *
 * @return array
 */
function weforms_get_form_field_labels( $form_id ) {
    $fields  = wpuf_get_form_fields( $form_id );
    $exclude = array( 'step_start', 'section_break', 'recaptcha', 'shortcode', 'action_hook' );

    if ( ! $fields ) {
        return false;
    }

    $data = array();
    foreach ($fields as $field) {
        if ( empty( $field['name'] ) ) {
            continue;
        }

        // exclude the fields
        if ( in_array( $field['input_type'], $exclude ) ) {
            continue;
        }

        $data[ $field['name'] ] = array(
            'label' => $field['label'],
            'type'  => $field['input_type']
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

    foreach ($fields as $meta_key => $field ) {
        $value = weforms_get_entry_meta( $entry_id, $meta_key, true );

        if ( $field['type'] == 'textarea' ) {

            $data[ $meta_key ] = weforms_format_text( $value );

        } elseif ( $field['type'] == 'name' ) {

            $data[ $meta_key ] = implode( ' ', explode( WPUF_Render_Form::$separator, $value ) );

        } elseif ( in_array( $field['type'], array( 'image_upload', 'file_upload' ) ) ) {

            $data[ $meta_key ] = '';

            if ( is_array( $value ) && $value ) {

                foreach ($value as $attachment_id) {

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

            $data[ $meta_key ] = explode( WPUF_Render_Form::$separator, $value );

        } elseif ( $field['type'] == 'map' ) {

            list( $lat, $long ) = explode( ',', $value );

            $data[ $meta_key ] = array( 'lat' => $lat, 'long' => $long );

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
    $version  = "";

    // first get the platform
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'MAC OS';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }

    // next get the name of the useragent yes seperately and for good reason
    if ( preg_match('/MSIE/i',$u_agent) && !preg_match( '/Opera/i',$u_agent ) ) {
        $bname = 'Internet Explorer';
        $ub    = "MSIE";
    } elseif(preg_match('/Trident/i',$u_agent)) {
        // this condition is for IE11
        $bname = 'Internet Explorer';
        $ub = "rv";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    // Added "|:"
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
     ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);

    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
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

    if ( !$is_enabled ) {
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
 * @param mixed $default
 *
 * @return mixed
 */
function weforms_get_settings( $key = '', $default = '' ) {
    $settings = get_option( 'weforms_settings', array() );

    if ( empty( $key ) ) {
        return $settings;
    }

    if ( isset( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }

    return $default;
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
