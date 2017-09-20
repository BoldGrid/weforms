<?php

/**
 * Text Field Class
 */
class WeForms_Form_Field_MultiDropdown extends WeForms_Form_Field_Dropdown {

    function __construct() {
        $this->name       = __( 'Multi Select', 'weforms' );
        $this->input_type = 'multiple_select';
        $this->icon       = 'list-ul';
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
        $selected = is_array( $selected ) ? $selected : array();
        $name     = $field_settings['name'] . '[]';
        ?>
        <li <?php $this->print_list_attributes( $field_settings ); ?>>
            <?php $this->print_label( $field_settings ); ?>

            <div class="wpuf-fields">
                <select multiple="multiple" class="multiselect <?php echo 'wpuf_'. $field_settings['name'] .'_'. $form_id; ?>" name="<?php echo $name; ?>" mulitple="multiple" data-required="<?php echo $field_settings['required'] ?>" data-type="multiselect">

                    <?php if ( !empty( $field_settings['first'] ) ) { ?>
                        <option value=""><?php echo $field_settings['first']; ?></option>
                    <?php } ?>

                    <?php
                    if ( $field_settings['options'] && count( $field_settings['options'] ) > 0 ) {
                        foreach ($field_settings['options'] as $value => $option) {
                            $current_select = selected( in_array( $value, $selected ), true, false );
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
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();
        $props    = array(
            'selected' => array(),
            'options'  => array( 'Option' => __( 'Option', 'wpuf' ) ),
            'first'    => __( '— Select —', 'wpuf' ),
        );

        return array_merge( $defaults, $props );
    }
}