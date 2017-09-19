<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_Dropdown extends WeForms_Field_Contract {

    function __construct() {
        $this->name       = __( 'Dropdown', 'weforms' );
        $this->input_type = 'dropdown_field';
        $this->icon       = 'caret-square-o-down';
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
        $selected = isset( $field_settings['selected'] ) ? $field_settings['selected'] : '';
        $name      = $field_settings['name'];
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields">
                <select class="<?php echo 'wpuf_'. $field_settings['name'] .'_'. $form_id; ?>" name="<?php echo $name; ?>" data-required="<?php echo $field_settings['required'] ?>" data-type="select">

                    <?php if ( !empty( $field_settings['first'] ) ) { ?>
                        <option value=""><?php echo $field_settings['first']; ?></option>
                    <?php } ?>

                    <?php
                    if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                        foreach ($field_settings['options'] as $value => $option) {
                            $current_select = selected( $selected, $value, false );
                            ?>
                            <option value="<?php echo esc_attr( $value ); ?>"<?php echo $current_select; ?>><?php echo $option; ?></option>
                            <?php
                        }
                    }
                    ?>
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