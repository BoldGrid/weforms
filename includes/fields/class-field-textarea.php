<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Textarea extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Textarea', 'weforms' );
        $this->input_type = 'textarea_field';
        $this->icon       = 'paragraph';
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
        $req_class     = ( $field_settings['required'] == 'yes' ) ? 'required' : 'rich-editor';
        $value         = $field_settings['default'];
        $textarea_id   = $field_settings['name'] ? $field_settings['name'] . '_' . $form_id : 'textarea_';
        $form_settings = weforms()->form->get( $form_id )->get_settings();
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>
            <?php if ( in_array( $field_settings['rich'], array( 'yes', 'teeny' ) ) ) { ?>
                <div
                    class="wpuf-fields wpuf-rich-validation <?php printf( wp_kses_post( 'wpuf_%s_%s', $field_settings['name'], $form_id ) ); ?>"
                    data-type="rich"
                    data-required="<?php echo esc_attr( $field_settings['required'] ); ?>"
                    data-id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                    data-name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                >
            <?php } else { ?>
                <div class="wpuf-fields">
            <?php } ?>

                <?php

                if ( $field_settings['rich'] == 'yes' ) {
                    $editor_settings = [
                        'textarea_rows' => $field_settings['rows'],
                        'quicktags'     => false,
                        'media_buttons' => false,
                        'editor_class'  => $req_class,
                        'textarea_name' => $field_settings['name'],
                    ];

                    $editor_settings = apply_filters( 'wpuf_textarea_editor_args', $editor_settings );
                    wp_editor( $value, $textarea_id, $editor_settings );
                } elseif ( $field_settings['rich'] == 'teeny' ) {
                    $editor_settings = [
                        'textarea_rows' => $field_settings['rows'],
                        'quicktags'     => false,
                        'media_buttons' => false,
                        'teeny'         => true,
                        'editor_class'  => $req_class,
                        'textarea_name' => $field_settings['name'],
                    ];

                    $editor_settings = apply_filters( 'wpuf_textarea_editor_args', $editor_settings );
                    wp_editor( $value, $textarea_id, $editor_settings );
                } else {
                    ?>
                    <textarea
                        class="textareafield <?php echo ' wpuf_'. esc_attr( $field_settings['name'] ).'_'. esc_attr( $form_id ); ?>"
                        id="<?php echo esc_attr( $field_settings['name'] ) . '_' . esc_attr( $form_id ); ?>"
                        name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                        data-required="<?php echo esc_attr( $field_settings['required'] ) ?>"
                        data-type="textarea"
                        placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                        rows="<?php echo esc_attr($field_settings['rows']); ?>"
                        cols="<?php echo esc_attr($field_settings['cols']); ?>"
                        data-style="<?php echo esc_attr( $use_theme_css ); ?>"
                    ><?php echo esc_textarea( $value ) ?></textarea>
                    <span class="wpuf-wordlimit-message wpuf-help"></span>

                <?php
                } ?>

                <?php
                $this->help_text( $field_settings );

        if ( isset( $field_settings['word_restriction'] ) && $field_settings['word_restriction'] ) {
            $this->check_word_restriction_func(
                        $field_settings['word_restriction'],
                        $field_settings['rich'],
                        $field_settings['name'] . '_' . $form_id
                      );
        } ?>
        </div></li>
        <?php
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings();
        $default_text_options = $this->get_default_textarea_option_settings();

        return array_merge( $default_options, $default_text_options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'word_restriction' => '',
            'rows'             => 5,
            'cols'             => 25,
            'rich'             => 'no',
        ];

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
        if( empty( $_POST['_wpnonce'] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        return wp_kses_post( $args[$field['name']] );
    }
}
