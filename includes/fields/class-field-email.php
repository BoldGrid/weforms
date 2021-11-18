<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Email extends WeForms_Form_Field_Text {

    public function __construct() {
        $this->name       = __( 'Email Address', 'weforms' );
        $this->input_type = 'email_address';
        $this->icon       = 'envelope-o';
    }

    /**
     * Render the text field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id ) {

        // let's not show the email field if user choose to auto populate for logged users
        if ( isset( $field_settings['auto_populate'] ) && $field_settings['auto_populate'] == 'yes' && is_user_logged_in() ) {
            return;
        }
        $form_settings = weforms()->form->get( $form_id )->get_settings();
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $value         = $field_settings['default']; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <input
                    id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    type="email"
                    class="email <?php echo ' wpuf_'. esc_attr( $field_settings['name'] ).'_'. esc_attr( $form_id ); ?>"
                    data-duplicate="<?php echo isset( $field_settings['duplicate'] ) ? esc_attr( $field_settings['duplicate'] ) : 'no'; ?>"
                    data-required="<?php echo esc_attr( $field_settings['required'] ) ?>"
                    data-type="email"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                    name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                    autocomplete="email"
                />
                <?php $this->help_text( $field_settings ); ?>
            </div>
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
        $check_duplicate      = [
            [
                'name'          => 'duplicate',
                'title'         => 'No Duplicates',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'no'   => __( 'Unique Values Only', 'weforms' ),
                ],
                'default'       => '',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Select this option to limit user input to unique values only. This will require that a value entered in a field does not currently exist in the entry database for that field.', 'weforms' ),
            ],
            [
                'name'          => 'auto_populate',
                'title'         => 'Auto-populate email for logged users',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'yes'   => __( 'Auto-populate Email', 'weforms' ),
                ],
                'default'       => '',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'If a user is logged into the site, this email field will be auto-populated with his email. And form\'s email field will be hidden.', 'weforms' ),
            ],
        ];

        return array_merge( $default_options, $default_text_options, $check_duplicate );
    }

    /**
     * Prepare entry default, can be replaced through field classes
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field, $args = [] ) {
        if( empty( $_POST['_wpnonce'])) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        if ( isset( $field['auto_populate'] ) && $field['auto_populate'] == 'yes' && is_user_logged_in() ) {
            $user = wp_get_current_user();

            if ( !empty( $user->user_email ) ) {
                return $user->user_email;
            }
        }

        $value = !empty( $args[ $field[ 'name' ] ] ) ? $args[ $field[ 'name' ] ] : '';

        return sanitize_text_field( trim( $value ) );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults              = $this->default_attributes();
        $defaults['duplicate'] = '';

        return $defaults;
    }
}
