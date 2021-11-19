<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Name extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Name', 'weforms' );
        $this->input_type = 'name_field';
        $this->icon       = 'user';
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

        // let's not show the name field if user choose to auto populate for logged users
        if ( isset( $field_settings['auto_populate'] ) && $field_settings['auto_populate'] == 'yes' && is_user_logged_in() ) {
            return;
        }
        $form_settings = weforms()->form->get( $form_id )->get_settings();

        $use_theme_css    = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <div class="wpuf-fields">
                <div class="wpuf-name-field-wrap format-<?php echo esc_attr( $field_settings['format'] ); ?>" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                    <div class="wpuf-name-field-first-name">
                        <input
                            name="<?php echo esc_attr( $field_settings['name'] ) ?>[first]"
                            type="text"
                            placeholder="<?php echo esc_attr( $field_settings['first_name']['placeholder'] ); ?>"
                            value="<?php echo esc_attr( $field_settings['first_name']['default'] ); ?>"
                            size="40"
                            data-required="<?php echo esc_attr( $field_settings['required'] ) ?>"
                            data-type="text"
                            data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                            class="textfield wpuf_<?php echo esc_attr( $field_settings['name'] ); ?>_<?php echo esc_attr( $form_id); ?>"
                            autocomplete="given-name"
                        >
                        <?php if ( ! $field_settings['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label" data-style="<?php echo esc_attr( $use_theme_css ); ?>"><?php esc_html_e( 'First', 'weforms' ); ?></label>
                        <?php endif; ?>
                    </div>

                    <?php if ( $field_settings['format'] != 'first-last' ) { ?>
                        <div class="wpuf-name-field-middle-name">
                            <input
                                name="<?php echo esc_attr( $field_settings['name']) ?>[middle]"
                                type="text" class="textfield"
                                placeholder="<?php echo esc_attr( $field_settings['middle_name']['placeholder'] ); ?>"
                                value="<?php echo esc_attr( $field_settings['middle_name']['default'] ); ?>"
                                size="40"
                                autocomplete="additional-name"
                                data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                            >
                            <?php if ( ! $field_settings['hide_subs'] ) : ?>
                                <label class="wpuf-form-sub-label" data-style="<?php echo esc_attr( $use_theme_css ); ?>"><?php esc_html_e( 'Middle', 'weforms' ); ?></label>
                            <?php endif; ?>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" name="<?php echo esc_attr( $field_settings['name'] ) ?>[middle]" value="">
                    <?php } ?>

                    <div class="wpuf-name-field-last-name">
                        <input
                            name="<?php echo esc_attr( $field_settings['name'] ) ?>[last]"
                            type="text" class="textfield"
                            placeholder="<?php echo esc_attr( $field_settings['last_name']['placeholder'] ); ?>"
                            value="<?php echo esc_attr( $field_settings['last_name']['default'] ); ?>"
                            size="40"
                            autocomplete="family-name"
                            data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                        >
                        <?php if ( ! $field_settings['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label" data-style="<?php echo esc_attr( $use_theme_css ); ?>"><?php esc_html_e( 'Last', 'weforms' ); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
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
        $default_options = $this->get_default_option_settings( true, [ 'width' ] );

        $name_settings = [
            [
                'name'      => 'format',
                'title'     => __( 'Format', 'weforms' ),
                'type'      => 'radio',
                'options'   => [
                    'first-last'        => __( 'First and Last name', 'weforms' ),
                    'first-middle-last' => __( 'First, Middle and Last name', 'weforms' ),
                ],
                'selected'  => 'first-last',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ],
            [
                'name'          => 'auto_populate',
                'title'         => 'Auto-populate name for logged users',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'yes'   => __( 'Auto-populate Name', 'weforms' ),
                ],
                'default'       => '',
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'If a user is logged into the site, this name field will be auto-populated with his first-last/display name. And form\'s name field will be hidden.', 'weforms' ),
            ],
            [
                'name'      => 'sub-labels',
                'title'     => __( 'Label', 'weforms' ),
                'type'      => 'name',
                'section'   => 'advanced',
                'priority'  => 21,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ],
            [
                'name'          => 'hide_subs',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => [
                    'true'   => __( 'Hide Sub Labels', 'weforms' ),
                ],
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => '',
            ],
            [
                'name'          => 'inline',
                'title'         => __( 'Show in inline list', 'weforms' ),
                'type'          => 'radio',
                'options'       => [
                    'yes'   => __( 'Yes', 'weforms' ),
                    'no'    => __( 'No', 'weforms' ),
                ],
                'default'       => 'no',
                'inline'        => true,
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => __( 'Show this option in an inline list', 'weforms' ),
            ],
        ];

        return array_merge( $default_options, $name_settings );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'format'     => 'first-last',
            'first_name' => [
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'First', 'weforms' ),
            ],
            'middle_name' => [
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'Middle', 'weforms' ),
            ],
            'last_name' => [
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'Last', 'weforms' ),
            ],
            'inline'           => 'yes',
            'hide_subs'        => false,
        ];

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry default, can be replaced through field classes
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field, $args = [] ) {
        if( empty( $_POST[ '_wpnonce' ] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        // $args = ! empty( $args ) ? $args : sanitize_text_field( wp_unslash( $_POST ) );
        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        if ( isset( $field['auto_populate'] ) && $field['auto_populate'] == 'yes' && is_user_logged_in() ) {
            $user = wp_get_current_user();

            if ( !empty( $user->ID ) ) {
                if ( $user->first_name || $user->last_name ) {
                    $name   = [];
                    $name[] = $user->first_name;
                    $name[] = $user->last_name;

                    return implode( WeForms::$field_separator, $name );
                } else {
                    return $user->display_name;
                }
            }
        }

        $value = !empty( $args[$field['name']] ) ? $args[$field['name']] : '';

        if ( is_array( $value ) ) {
            $entry_value = sanitize_text_field( trim( implode( WeForms::$field_separator, $args[$field['name']] ) ) );
        } else {
            $entry_value = sanitize_text_field( trim( $value  ) );
        }

        return $entry_value;
    }
}
