<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Name extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Name', 'weforms' );
        $this->input_type = 'name_field';
        $this->icon       = 'user';
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

            <div class="wpuf-fields">
                <div class="wpuf-name-field-wrap format-<?php echo $field_settings['format']; ?>">
                    <div class="wpuf-name-field-first-name">
                        <input
                            name="<?php echo $field_settings['name'] ?>[first]"
                            type="text"
                            placeholder="<?php echo esc_attr( $field_settings['first_name']['placeholder'] ); ?>"
                            value="<?php echo esc_attr( $field_settings['first_name']['default'] ); ?>"
                            size="40"
                            data-required="<?php echo $field_settings['required'] ?>"
                            data-type="text"
                            class="textfield wpuf_<?php echo $field_settings['name']; ?>_<?php echo $form_id; ?>"
                        >

                        <?php if ( ! $field_settings['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label"><?php _e( 'First', 'weforms' ); ?></label>
                        <?php endif; ?>
                    </div>

                    <?php if ( $field_settings['format'] != 'first-last' ) : ?>
                        <div class="wpuf-name-field-middle-name">
                            <input
                                name="<?php echo $field_settings['name'] ?>[middle]"
                                type="text" class="textfield"
                                placeholder="<?php echo esc_attr( $field_settings['middle_name']['placeholder'] ); ?>"
                                value="<?php echo esc_attr( $field_settings['middle_name']['default'] ); ?>"
                                size="40"
                            >

                            <?php if ( ! $field_settings['hide_subs'] ) : ?>
                                <label class="wpuf-form-sub-label"><?php _e( 'Middle', 'weforms' ); ?></label>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="<?php echo $field_settings['name'] ?>[middle]" value="">
                    <?php endif; ?>

                    <div class="wpuf-name-field-last-name">
                        <input
                            name="<?php echo $field_settings['name'] ?>[last]"
                            type="text" class="textfield"
                            placeholder="<?php echo esc_attr( $field_settings['last_name']['placeholder'] ); ?>"
                            value="<?php echo esc_attr( $field_settings['last_name']['default'] ); ?>"
                            size="40"
                        >
                        <?php if ( ! $field_settings['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label"><?php _e( 'Last', 'weforms' ); ?></label>
                        <?php endif; ?>
                    </div>
                </div>
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