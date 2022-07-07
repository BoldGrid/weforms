<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Image extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Image Upload', 'weforms' );
        $this->input_type = 'image_upload';
        $this->icon       = 'file-image-o';
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
        $form_settings = weforms()->form->get( $form_id )->get_settings();
        $use_theme_css    = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $unique_id = sprintf( '%s-%d', $field_settings['name'], $form_id ); ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings, $form_id ); ?>
            <div class="wpuf-fields">
                <div id="wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                    <div class="wpuf-attachment-upload-filelist"  data-style="<?php echo esc_attr( $use_theme_css ); ?>" data-type="file" data-required="<?php echo esc_attr( $field_settings['required'] ); ?>" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                        <a id="wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles" data-style="<?php echo esc_attr( $use_theme_css ); ?>" data-form_id="<?php echo esc_attr( $form_id ); ?>" class="button file-selector <?php echo ' wpuf_' . esc_attr( $field_settings['name'] ) . '_' . esc_attr(
                            $form_id); ?>" href="#"><?php echo esc_attr ( $field_settings['button_label'] ); ?></a>
                        <ul class="wpuf-attachment-list thumbnails"></ul>
                    </div>
                </div><!-- .container -->

                <?php $this->help_text( $field_settings ); ?>

            </div> <!-- .wpuf-fields -->
        </li>
        <?php
        $uid = esc_attr( $unique_id );
        $count = esc_attr( $field_settings['count'] );
        $name = esc_attr( $field_settings['name'] );
        $max_size = esc_attr($field_settings['max_size']);

        $script = ";(function($) {
            $(document).ready( function() {
                var uploader = new WPUF_Uploader(
                    'wpuf-{$uid}-pickfiles',
                    'wpuf-{$uid}-upload-container',
                    {$count},
                    '{$name}',
                    'jpg,jpeg,gif,png,bmp',
                    {$max_size}
                );
                wpuf_plupload_items.push(uploader);
            });
        })(jQuery);";

        wp_add_inline_script( 'wpuf-form', $script );
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options      = $this->get_default_option_settings( true, ['dynamic', 'width'] ); // exclude dynamic

        $settings = [
            [
                'name'          => 'max_size',
                'title'         => __( 'Max. file size', 'weforms' ),
                'type'          => 'text',
                'section'       => 'advanced',
                'priority'      => 20,
                'help_text'     => __( 'Enter maximum upload size limit in KB', 'weforms' ),
            ],

            [
                'name'          => 'count',
                'title'         => __( 'Max. files', 'weforms' ),
                'type'          => 'text',
                'section'       => 'advanced',
                'priority'      => 21,
                'help_text'     => __( 'Number of images can be uploaded', 'weforms' ),
            ],
            [
                'name'          => 'button_label',
                'title'         => __( 'Button Label', 'weforms' ),
                'type'          => 'text',
                'default'       => __( 'Select Image', 'weforms' ),
                'section'       => 'basic',
                'priority'      => 22,
                'help_text'     => __( 'Enter a label for the Select button', 'weforms' ),
            ],
        ];

        return array_merge( $default_options, $settings );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = [
            'max_size'       => '1024',
            'count'          => '1',
            'button_label'   => __( 'Select Image', 'weforms' ),
        ];

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry
     *
     * @param $field
     *
     * @return @return mixed
     */
    public function prepare_entry( $field, $args = [] ) {
        if( empty( $_POST[ '_wpnonce' ] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpuf_form_add' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

       $args = ! empty( $args ) ? $args : weforms_clean( $_POST );

        return isset( $args['wpuf_files'][$field['name']] ) ? $args['wpuf_files'][$field['name']] : [];
    }
}
