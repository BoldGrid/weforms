<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_MultiDropdown extends WeForms_Form_Field_Dropdown {

    public function __construct() {
        $this->name       = __( 'Multi Select', 'weforms' );
        $this->input_type = 'multiple_select';
        $this->icon       = 'list-ul';
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
        $selected      = is_array( $selected ) ? $selected : [];
        $name          = $field_settings['name'] . '[]'; ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>

            <?php do_action( 'weforms_multidropdown_field_after_label', $field_settings ); ?>

            <div class="wpuf-fields">
                <select
                    multiple="multiple"
                    class="multiselect <?php echo 'wpuf_'. esc_attr( $field_settings['name'] ) .'_'. esc_attr( $form_id ); ?>"
                    id="<?php echo esc_attr($field_settings['name']) . '_' . esc_attr($form_id); ?>"
                    name="<?php echo esc_attr($name); ?>"
                    mulitple="multiple"
                    data-required="<?php echo esc_attr($field_settings['required']) ?>"
                    data-type="multiselect"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                >

                    <?php if ( !empty( $field_settings['first'] ) ) { ?>
                        <option value=""><?php echo esc_attr($field_settings['first']); ?></option>
                    <?php } ?>

                    <?php
                    if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                        foreach ($field_settings['options'] as $value => $option) {
                            $current_select = selected( in_array( $value, $selected ), true, false );
                            ?>
                            <option value="<?php echo esc_attr( $value ); ?>"<?php echo esc_attr( $current_select ); ?>><?php echo esc_html( $option ); ?></option>
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
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'selected' => [],
            'options'  => [ 'Option' => __( 'Option', 'weforms' ) ],
            'first'    => __( '— Select —', 'weforms' ),
        ];

        $props = apply_filters( 'weforms_multidropdown_field_props', $props );

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
        if( empty( $_POST[ '_wpnonce' ] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );
        $entry_value = ( is_array( $args[$field['name']] ) && $args[$field['name']] ) ? $args[$field['name']] : array();

        if ( $entry_value ) {
            $new_val = [];

            foreach ( $entry_value as $option_key ) {
                $new_val[] = isset( $field['options'][$option_key] ) ? $field['options'][$option_key] : $option_key;
            }

            $entry_value = implode( WeForms::$field_separator, $new_val );
        } else {
            $entry_value = '';
        }

        return $entry_value;
    }
}
