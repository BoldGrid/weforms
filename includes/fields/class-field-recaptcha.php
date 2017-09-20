<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_reCaptcha extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'reCaptcha', 'weforms' );
        $this->input_type = 'recaptcha';
        $this->icon       = 'qrcode';
    }

    /**
     * Render the text field
     *
     * @param  array  $field_settings
     * @param  integer  $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <?php
            $enable_no_captcha = $enable_invisible_recaptcha = $public_key = '';
            $settings          = weforms_get_settings( 'recaptcha' );

            if ( isset( $settings->key ) ) {
                $public_key = $settings->key;
            }

            if ( ! $public_key ) {
                _e( 'reCaptcha API key is missing.');
            } else {

                if ( isset ( $field_settings['recaptcha_type'] ) ) {
                    $enable_invisible_recaptcha = $field_settings['recaptcha_type'] == 'invisible_recaptcha' ? true : false;
                    $enable_no_captcha          = $field_settings['recaptcha_type'] == 'enable_no_captcha' ? true : false;
                }

                if ( $enable_invisible_recaptcha ) { ?>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <div id='recaptcha' class="g-recaptcha" data-sitekey=<?php echo $public_key; ?>" data-callback="onSubmit" data-size="invisible"></div>
                <?php } else { ?>
                    <div class="wpuf-fields <?php echo ' wpuf_'.$field_settings['name'].'_'.$form_id; ?>">
                        <?php echo recaptcha_get_html( $public_key, $enable_no_captcha, null, is_ssl() ); ?>
                    </div>
                <?php } ?>
            <?php } ?>

        </li>
        <?php
    }

    /**
     * Custom validator
     *
     * @return array
     */
    public function get_validator() {
        return array(
            'callback'      => 'has_recaptcha_api_keys',
            'button_class'  => 'button-faded',
            'msg_title'     => __( 'Site key and Secret key', 'wpuf' ),
            'msg'           => sprintf(
                __( 'You need to set Site key and Secret key in <a href="%s" target="_blank">Settings</a> in order to use "Recaptcha" field. <a href="%s" target="_blank">Click here to get the these key</a>.', 'wpuf' ),
                admin_url( 'admin.php?page=weforms#/settings' ),
                'https://www.google.com/recaptcha/'
            ),
        );
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $settings = array(
            array(
                'name'          => 'label',
                'title'         => __( 'Title', 'wpuf' ),
                'type'          => 'text',
                'section'       => 'basic',
                'priority'      => 10,
                'help_text'     => __( 'Title of the section', 'wpuf' ),
            ),

            array(
                'name'          => 'recaptcha_type',
                'title'         => 'reCaptcha type',
                'type'          => 'radio',
                'options'       => array(
                    'enable_no_captcha'    => __( 'Enable noCaptcha', 'wpuf' ),
                    'invisible_recaptcha'  => __( 'Enable Invisible reCaptcha', 'wpuf' ),
                ),
                'default'       => 'enable_no_captcha',
                'section'       => 'basic',
                'priority'      => 11,
                'help_text'     => __( 'Select reCaptcha type', 'wpuf' ),
            )
        );

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = array(
            'template'    => $this->get_type(),
            'label'          => '',
            'meta_value'    => '',
            'is_meta'       => 'yes',
            'id'            => 0,
            'is_new'        => true,
            'wpuf_cond'     => null
        );

        return $props;
    }
}