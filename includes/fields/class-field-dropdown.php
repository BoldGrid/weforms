<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Dropdown extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Dropdown', 'weforms' );
        $this->input_type = 'dropdown_field';
        $this->icon       = 'caret-square-o-down';
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
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $selected      = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';
        $name          = $field_settings['name']; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <?php do_action( 'weforms_dropdown_field_after_label', $field_settings ); ?>

            <div class="wpuf-fields">
                <select
                    class="<?php echo 'wpuf_'. esc_attr( $field_settings['name'] ) .'_'. esc_attr( $form_id ); ?>"
                    id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    name="<?php echo esc_attr( $name ); ?>"
                    data-required="<?php echo esc_attr( $field_settings['required'] ) ?>"
                    data-type="select"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                >
                    <?php if ( !empty( $field_settings['first'] ) ) { ?>
                        <option value=""><?php echo esc_attr( $field_settings['first'] ); ?></option>
                    <?php } ?>
                    <?php
                    if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                        foreach ($field_settings['options'] as $value => $option) {
                            $current_select = selected( $selected, $value, false );
                            ?>
                            <option value="<?php echo esc_attr( $value ); ?>"<?php echo esc_attr(  $current_select ); ?>><?php echo
                            esc_attr( $option ); ?></option>
                            <?php
                        }
                    } ?>
                </select>
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
        $default_options  = $this->get_default_option_settings();
        $dropdown_options = [
            $this->get_default_option_dropdown_settings(),
            [
                'name'          => 'first',
                'title'         => __( 'Select Text', 'weforms' ),
                'type'          => 'text',
                'section'       => 'basic',
                'priority'      => 13,
                'help_text'     => __( "First element of the select dropdown. Leave this empty if you don't want to show this field", 'weforms' ),
            ],
        ];

        $dropdown_options = apply_filters( 'weforms_dropdown_field_option_settings', $dropdown_options );

        return array_merge( $default_options, $dropdown_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'selected' => '',
            'options'  => [ 'Option' => __( 'Option', 'weforms' ) ],
            'first'    => __( '— Select —', 'weforms' ),
        ];

        $props = apply_filters( 'weforms_dropdown_field_props', $props );

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry
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
        $val  = $args[$field['name']];

        return isset( $field['options'][$val] ) ? $field['options'][$val] : $val;
    }
}
