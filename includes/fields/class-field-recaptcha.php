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
     * @param  integer  $form_id
     * @param  array  $field_settings
     *
     * @return void
     */
    public function render( $form_id, $field_settings ) {
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
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings();
        $default_text_options = $this->get_default_text_option_settings();

        return array_merge( $default_options, $default_text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = array(
            'word_restriction' => '',
        );

        return array_merge( $defaults, $props );
    }
}