<?php

/**
 * Email Notification Class
 */
class WeForms_Notification {

    private $merge_tags = array();
    private $args       = array();

    /**
     * Init the class
     *
     * @param array $args
     */
    public function __construct( $args = array() ) {
        $defaults = array(
            'form_id'  => 0,
            'entry_id' => 0,
            'page_id'  => 0
        );

        $this->args = wp_parse_args( $args, $defaults );
    }

    /**
     * Send notifications
     *
     * @return void
     */
    public function send_notifications() {
        $notifications = $this->get_active_notifications();

        if ( !$notifications ) {
            return;
        }

        $this->set_merge_tags();

        foreach ($notifications as $notification) {
            $this->send_notification( $notification );
        }
    }

    /**
     * Send a single notification
     *
     * @param  array $notification
     *
     * @return void
     */
    public function send_notification( $notification ) {
        $headers = array();

        $to          = $this->replace_tags( $notification['to'] );

        $subject     = $this->replace_tags( $notification['subject'] );
        $subject     = static::replace_name_tag( $subject, $this->args['entry_id'] );

        $message     = $this->replace_tags( $notification['message'] );
        $message     = static::replace_name_tag( $message, $this->args['entry_id'] );
        $message     = $this->replace_all_fields( $message );
        $message     = wpautop( $message );

        $fromName    = $this->replace_tags( $notification['fromName'] );
        $fromAddress = $this->replace_tags( $notification['fromAddress'] );
        $replyTo     = $this->replace_tags( $notification['replyTo'] );
        $cc          = $this->replace_tags( $notification['cc'] );
        $bcc         = $this->replace_tags( $notification['bcc'] );

        if ( $fromName || $fromAddress ) {
            $headers[] = sprintf( 'From: %s <%s>', $fromName, $fromAddress );
        }

        if ( $cc ) {
            $headers[] = sprintf( 'CC: %s', $cc );
        }

        if ( $bcc ) {
            $headers[] = sprintf( 'BCC: %s', $bcc );
        }

        if ( $replyTo ) {
            $headers[] = sprintf( 'Reply-To: %s', $replyTo );
        }

        // content type to text/html
        $headers[]  = 'Content-Type: text/html; charset=UTF-8';
        $email_body = apply_filters( 'weforms_email_message', $this->get_formatted_body( $message ), $notification['message'], $headers );

        wp_mail( $to, $subject, $email_body, $headers );
    }

    /**
     * Get active notifications of a form
     *
     * @param  int $form_id
     *
     * @return array|boolean
     */
    public function get_active_notifications() {
        $notifications = wpuf_get_form_notifications( $this->args['form_id'] );

        if ( $notifications ) {
            $notifications = array_filter( $notifications, function($notification) {
                return $notification['active'] == true;
            } );

            return $notifications;
        }

        return false;
    }

    /**
     * Get formatted HTML email
     *
     * @param  string $message
     *
     * @return string
     */
    public function get_formatted_body( $message ) {
        $css    = '';
        $header = apply_filters( 'weforms_email_header', '' );
        $footer = apply_filters( 'weforms_email_footer', '' );

        if ( empty( $header ) ) {
            ob_start();
            include WEFORMS_INCLUDES . '/emails/common/header.php';
            $header = ob_get_clean();
        }

        if ( empty( $footer ) ) {
            ob_start();
            include WEFORMS_INCLUDES . '/emails/common/footer.php';
            $footer = ob_get_clean();
        }

        ob_start();
        include WEFORMS_INCLUDES . '/emails/common/styles.php';
        $css = apply_filters( 'weforms_email_styles', ob_get_clean() );

        $content = $header . $message . $footer;

        if ( ! class_exists( 'Emogrifier' ) ) {
            require_once WEFORMS_INCLUDES . '/library/Emogrifier.php';
        }

        try {

            // apply CSS styles inline for picky email clients
            $emogrifier = new Emogrifier( $content, $css );
            $content = $emogrifier->emogrify();

        } catch ( Exception $e ) {

            echo $e->getMessage();
        }

        return $content;
    }

    /**
     * Populate an key/value pair of search/replace array
     *
     * @return array
     */
    public function set_merge_tags() {

        // return early we had a previous array call
        if ( $this->merge_tags ) {
            return $this->merge_tags;
        }

        // populate the key/value array for the first time
        $tags          = weforms_get_merge_tags();
        $replace_array = array();

        foreach ($tags as $section => $child) {

            if ( !$child['tags'] ) {
                continue;
            }

            foreach ($child['tags'] as $search_key => $label) {
                $replace_array[ '{' . $search_key . '}' ] = $this->get_merge_value( $search_key );
            }
        }

        $this->merge_tags = $replace_array;
    }

    /**
     * Get a merge tag value based on a tag
     *
     * @param  string $tag
     *
     * @return string
     */
    public function get_merge_value( $tag ) {

        switch ( $tag ) {
            case 'entry_id':
                return $this->args['entry_id'];
                break;

            case 'form_id':
                return $this->args['form_id'];
                break;

            case 'form_name':
                return get_post_field( 'post_title', $this->args['form_id'] );
                break;

            case 'admin_email':
                return get_option( 'admin_email' );
                break;

            case 'date':
                return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
                break;

            case 'site_name':
                return get_bloginfo( 'name' );
                break;

            case 'site_url':
                return site_url( '/' );
                break;

            case 'page_title':
                return get_post_field( 'post_title', $this->args['page_id'] );
                break;

            case 'ip_address':
                return wpuf_get_client_ip();
                break;

            case 'user_id':
                return get_current_user_id();
                break;

            case 'first_name':
                return $this->get_user_prop( 'first_name' );
                break;

            case 'last_name':
                return $this->get_user_prop( 'last_name' );
                break;

            case 'display_name':
                return $this->get_user_prop( 'display_name' );
                break;

            case 'user_email':
                return $this->get_user_prop( 'user_email' );
                break;

            case 'url_page':
                return get_permalink( $this->args['page_id'] );
                break;

            case 'url_referer':
                return $_SERVER['HTTP_REFERER'];
                break;

            case 'url_login':
                return wp_login_url();
                break;

            case 'url_logout':
                return wp_logout_url();
                break;

            case 'url_register':
                return wp_registration_url();
                break;

            case 'url_lost_password':
                return wp_lostpassword_url();
                break;

            default:
                return apply_filters( 'weforms_merge_tag_value', '', $tag, $this->args );
                break;
        }
    }

    /**
     * Parse out the custom fields with entry meta values
     *
     * @param  string $text
     *
     * @return string
     */
    public static function replace_field_tags( $text, $entry_id ) {
        $pattern = '/{field:(\w*)}/';

        preg_match_all( $pattern, $text, $matches );

        // bail out if nothing found to be replaced
        if ( !$matches ) {
            return $text;
        }

        foreach ($matches[1] as $index => $meta_key) {
            $meta_value = weforms_get_entry_meta( $entry_id, $meta_key, true );
            $text       = str_replace( $matches[0][$index], $meta_value, $text );
        }

        return $text;
    }

    /**
     * Replace name tag with required value
     *
     * @param  string $text
     *
     * @return string
     */
    public static function replace_name_tag( $text, $entry_id ) {
        $pattern = '/{name-(full|first|middle|last):(\w*)}/';

        preg_match_all( $pattern, $text, $matches );

        // bail out if nothing found to be replaced
        if ( !$matches[0] ) {
            return $text;
        }

        list( $search, $fields, $meta_key ) = $matches;

        $meta_value = weforms_get_entry_meta( $entry_id, $meta_key[0], true );
        $replace    = explode( WPUF_Render_Form::$separator, $meta_value );

        foreach ($search as $index => $search_key) {

            if ( 'first' == $fields[ $index ] ) {

                $text = str_replace( $search_key, $replace[0], $text );

            } elseif ( 'middle' == $fields[ $index ] ) {

                $text = str_replace( $search_key, $replace[1], $text );

            } elseif ( 'last' == $fields[ $index ] ) {

                $text = str_replace( $search_key, $replace[2], $text );

            } else {

                $text = str_replace( $search_key, implode(' ', $replace ), $text );
            }

        }

        return $text;
    }

    /**
     * Get property of a user object with failsafe check
     *
     * @param  string $property
     *
     * @return string
     */
    public function get_user_prop( $property ) {
        $user = wp_get_current_user();

        if ( $user->ID != 0 ) {
            return $user->{$property};
        }

        return '';
    }

    /**
     * Replace text with merge tags
     *
     * @param  string $text
     *
     * @return text
     */
    public function replace_tags( $text = '' ) {
        $merge_keys   = array_keys( $this->merge_tags );
        $merge_values = array_values( $this->merge_tags );

        $text         = str_replace( $merge_keys, $merge_values, $text );
        $text         = static::replace_field_tags( $text, $this->args['entry_id'] );

        return $text;
    }

    /**
     * Replace {all_fields} if found
     *
     * @param  string $text
     *
     * @return string
     */
    public function replace_all_fields( $text = '' ) {

        // check if {all_fields} exists
        if ( false === strpos( $text, '{all_fields}' ) ) {
            return $text;
        }

        $data   = array();
        $fields = weforms_get_form_field_labels( $this->args['form_id'] );

        if ( !$fields ) {
            return $text;
        }

        foreach ($fields as $meta_key => $field ) {
            $value = weforms_get_entry_meta( $this->args['entry_id'], $meta_key, true );

            if ( empty( $value ) ) {
                $value = '&mdash;';
            }

            if ( $field['type'] == 'textarea' ) {
                $data[ $meta_key ] = weforms_format_text( $value );
            } elseif( $field['type'] == 'name' ) {
                $data[ $meta_key ] = implode( ' ', explode( WPUF_Render_Form::$separator, $value ) );
            } else {
                $data[ $meta_key ] = $value;
            }
        }

        $table = '<table width="600" cellpadding="0" cellspacing="0">';
            $table .= '<tbody>';

                foreach ($data as $key => $value) {
                    $table .= '<tr class="field-label">';
                        $table .= '<th><strong>' . $fields[$key]['label'] . '</strong></th>';
                    $table .= '</tr>';
                    $table .= '<tr class="field-value">';
                        $table .= '<td>';
                            $table .= $value;
                        $table .= '</td>';
                    $table .= '</tr>';
                }

            $table .= '</tbody>';
        $table .= '</table>';

        $text = str_replace( '{all_fields}', $table, $text );

        return $text;
    }
}
