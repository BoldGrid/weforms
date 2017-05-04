<?php

/**
 * Get contact form templates
 *
 * @return array
 */
function wpuf_cf_get_form_templates() {
    require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/form-templates/contact-form.php';
    require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/form-templates/support-form.php';
    require_once WPUF_CONTACT_FORM_INCLUDES . '/admin/form-templates/event-registration-form.php';

    $integrations = array(
        'WPUF_Contact_Form_Template_Contact'            => new WPUF_Contact_Form_Template_Contact(),
        'WPUF_Contact_Form_Template_Support'            => new WPUF_Contact_Form_Template_Support(),
        'WPUF_Contact_Form_Template_Event_Registration' => new WPUF_Contact_Form_Template_Event_Registration(),
    );

    return apply_filters( 'wpuf_contact_form_templates', $integrations );
}

/**
 * Get entries by a form_id
 *
 * @param  int $form_id
 * @param  array  $args
 *
 * @return Object
 */
function wpuf_cf_get_form_entries( $form_id, $args = array() ) {
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
            FROM ' . $wpdb->wpuf_cf_entries .
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
function wpuf_cf_get_entry( $entry_id ) {
    global $wpdb;

    $query = 'SELECT id, form_id, user_id, user_device, referer, INET_NTOA( user_ip ) as ip_address, created_at
             FROM ' . $wpdb->wpuf_cf_entries . '
             WHERE id = %d';

    return $wpdb->get_row( $wpdb->prepare( $query, $entry_id ) );
}

/**
 * Insert a new entry
 *
 * @param  array $args
 * @param  array  $fields
 *
 * @return WP_Error|integer
 */
function wpuf_cf_insert_entry( $args, $fields = array() ) {
    global $wpdb;

    $browser = wpuf_cf_get_browser();

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
        return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'best-contact-form' ) );
    }

    if ( !$fields ) {
        return new WP_Error( 'no-fields', __( 'No form fields were found.', 'best-contact-form' ) );
    }

    $success = $wpdb->insert( $wpdb->wpuf_cf_entries, $r );

    if ( is_wp_error( $success ) || !$success ) {
        return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'best-contact-form' ) );
    }

    $entry_id = $wpdb->insert_id;

    foreach ($fields as $key => $value) {
        wpuf_cf_add_entry_meta( $entry_id, $key, $value );
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
function wpuf_cf_change_entry_status( $entry_id, $status ) {
    global $wpdb;

    return $wpdb->update( $wpdb->wpuf_cf_entries,
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
function wpuf_cf_delete_entry( $entry_id ) {
    global $wpdb;

    return $wpdb->delete( $wpdb->wpuf_cf_entries, array( 'id' => $entry_id ), array( '%d' ) );
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
function wpuf_cf_add_entry_meta( $entry_id, $meta_key, $meta_value, $unique = false ) {
    return add_metadata( 'wpuf_cf_entry', $entry_id, $meta_key, $meta_value, $unique);
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
function wpuf_cf_get_entry_meta( $entry_id, $key = '', $single = false ) {
    return get_metadata( 'wpuf_cf_entry', $entry_id, $key, $single );
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
function wpuf_cf_update_entry_meta( $entry_id, $meta_key, $meta_value, $prev_value = '' ) {
    return update_metadata( 'wpuf_cf_entry', $entry_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Delete everything from entry meta matching meta key.
 *
 * @param string $entry_meta_key Key to search for when deleting.
 * @return bool Whether the entry meta key was deleted from the database.
 */
function wpuf_cf_delete_entry_meta_by_key( $meta_key ) {
    return delete_metadata( 'wpuf_cf_entry', null, $meta_key, '', true );
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
function wpuf_cf_get_entry_custom( $entry_id = 0 ) {
    $entry_id = absint( $entry_id );

    return wpuf_cf_get_entry_meta( $entry_id );
}

/**
 * Retrieve meta field names for a entry.
 *
 * If there are no meta fields, then nothing (null) will be returned.
 *
 * @param int $entry_id Optional.
 * @return array|void Array of the keys, if retrieved.
 */
function wpuf_cf_get_entry_custom_keys( $entry_id = 0 ) {
    $custom = wpuf_cf_get_entry_custom( $entry_id );

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
function wpuf_cf_count_form_entries( $form_id, $status = 'publish' ) {
    global $wpdb;

    return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT count(id) FROM ' . $wpdb->wpuf_cf_entries . ' WHERE form_id = %d AND status = %s', $form_id, $status ) );
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
function wpuf_cf_get_entry_columns( $form_id ) {
    $fields  = wpuf_get_form_fields( $form_id );

    // filter by input types
    $fields = array_filter( $fields, function($item) {
        return in_array( $item['input_type'], array( 'text', 'name' ) );
    } );

    if ( $fields ) {
        foreach ($fields as $field) {
            $columns[ $field['name'] ] = $field['label'];
        }
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
function wpuf_cf_get_form_field_labels( $form_id ) {
    $fields  = wpuf_get_form_fields( $form_id );

    if ( ! $fields ) {
        return false;
    }

    $data = array();
    foreach ($fields as $field) {
        if ( empty( $field['name'] ) ) {
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
 * Format a text and apply WP function callbacks
 *
 * @param  string $content
 *
 * @return string
 */
function wpuf_cf_format_text( $content ) {
    $content = wptexturize( $content );
    $content = convert_smilies( $content );
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
function wpuf_cf_get_browser() {
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
function wpuf_cf_get_merge_tags() {
    $tags = array(
        'form' => array(
            'title' => __( 'Form', 'best-contact-form' ),
            'tags'  => array(
                'entry_id'  => __( 'Entry ID', 'best-contact-form' ),
                'form_id'   => __( 'Form ID', 'best-contact-form' ),
                'form_name' => __( 'Form Name', 'best-contact-form' )
            )
        ),
        'system' => array(
            'title' => __( 'System', 'best-contact-form' ),
            'tags'  => array(
                'admin_email' => __( 'Site Administrator Email', 'best-contact-form' ),
                'date'        => __( 'Date', 'best-contact-form' ),
                'site_name'   => __( 'Site Title', 'best-contact-form' ),
                'site_url'    => __( 'Site URL', 'best-contact-form' ),
                'page_title'  => __( 'Embedded Page Title', 'best-contact-form' ),
            )
        ),
        'user' => array(
            'title' => __( 'User', 'best-contact-form' ),
            'tags'  => array(
                'ip_address'   => __( 'IP Address', 'best-contact-form' ),
                'user_id'      => __( 'User ID', 'best-contact-form' ),
                'first_name'   => __( 'First Name', 'best-contact-form' ),
                'last_name'    => __( 'Last Name', 'best-contact-form' ),
                'display_name' => __( 'Display Name', 'best-contact-form' ),
                'user_email'   => __( 'Email', 'best-contact-form' ),
            )
        ),
        'urls' => array(
            'title' => __( 'URL\'s', 'best-contact-form' ),
            'tags'  => array(
                'url_page'          => __( 'Embeded Page URL', 'best-contact-form' ),
                'url_referer'       => __( 'Referer URL', 'best-contact-form' ),
                'url_login'         => __( 'Login URL', 'best-contact-form' ),
                'url_logout'        => __( 'Logout URL', 'best-contact-form' ),
                'url_register'      => __( 'Register URL', 'best-contact-form' ),
                'url_lost_password' => __( 'Lost Password URL', 'best-contact-form' ),
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
function wpuf_cf_track_form_view( $form_id ) {
    // don't track administrators
    if ( current_user_can( 'administrator' ) ) {
        return;
    }

    // ability to turn this off if someone doesn't like this tracking
    $is_enabled = apply_filters( 'wpuf_cf_track_form_view', true );

    if ( !$is_enabled ) {
        return;
    }

    // increase the count
    $meta_key = '_wpuf_cf_view_count';
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
function wpuf_cf_get_form_views( $form_id ) {
    return (int) get_post_meta( $form_id, '_wpuf_cf_view_count', true );
}
