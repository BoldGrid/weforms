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
            <?php $this->print_label( $field_settings, $form_id ); ?>

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
                            autocomplete="given-name"
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
                                autocomplete="additional-name"
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
                            autocomplete="family-name"
                        >
                        <?php if ( ! $field_settings['hide_subs'] ) : ?>
                            <label class="wpuf-form-sub-label"><?php _e( 'Last', 'weforms' ); ?></label>
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
        $default_options = $this->get_default_option_settings();

        $name_settings = array(
            array(
                'name'      => 'format',
                'title'     => __( 'Format', 'weforms' ),
                'type'      => 'radio',
                'options'   => array(
                    'first-last'        => __( 'First and Last name', 'weforms' ),
                    'first-middle-last' => __( 'First, Middle and Last name', 'weforms' )
                ),
                'selected'  => 'first-last',
                'section'   => 'advanced',
                'priority'  => 20,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ),
            array(
                'name'      => 'sub-labels',
                'title'     => __( 'Label', 'weforms' ),
                'type'      => 'name',
                'section'   => 'advanced',
                'priority'  => 21,
                'help_text' => __( 'Select format to use for the name field', 'weforms' ),
            ),
            array(
                'name'          => 'hide_subs',
                'title'         => '',
                'type'          => 'checkbox',
                'is_single_opt' => true,
                'options'       => array(
                    'true'   => __( 'Hide Sub Labels', 'weforms' )
                ),
                'section'       => 'advanced',
                'priority'      => 23,
                'help_text'     => '',
            ),
        );

        return array_merge( $default_options, $name_settings );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = array(
            'format'     => 'first-last',
            'first_name' => array(
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'First', 'weforms' )
            ),
            'middle_name' => array(
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'Middle', 'weforms' )
            ),
            'last_name' => array(
                'placeholder' => '',
                'default'     => '',
                'sub'         => __( 'Last', 'weforms' )
            ),
            'hide_subs'        => false,
        );

        return array_merge( $defaults, $props );
    }
}