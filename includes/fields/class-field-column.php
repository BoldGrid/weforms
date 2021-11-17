<?php

/**
 * Column Field Class
 */
class WeForms_Form_Field_Column extends WeForms_Field_Contract {

    public function __construct() {
        $this->name       = __( 'Columns', 'weforms' );
        $this->input_type = 'column_field';
        $this->icon       = 'columns';
    }

    /**
     * Render the Column field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        $use_theme_css = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';
        $i             = 1;
        $columns       = $field_settings['columns'];
        $columns_size  = $field_settings['inner_columns_size'];
        $column_space  = $field_settings['column_space'];
        $inner_fields  = $field_settings['inner_fields'];
        $atts          = []; ?>
        <li class="wpuf-el">
            <div class="wpuf-field-columns <?php echo 'has-columns-' . esc_attr( $columns ); ?>" data-style="<?php echo esc_attr( $use_theme_css ); ?>">
                <div class="wpuf-column-field-inner-columns">
                    <div class="wpuf-column">
                        <?php while ( $i <= $columns ) { ?>
                            <div class="<?php echo 'column-' . esc_attr( $i ) . ' items-of-column-' . esc_attr( $columns ); ?> wpuf-column-inner-fields">
                                <ul class="wpuf-column-fields">
                                    <?php weforms()->fields->render_fields( $inner_fields['column-' . $i], $form_id, $atts, $type, $post_id ); ?>
                                </ul>
                            </div>

                            <?php $i++; ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </li>
        <?php
    }

    /**
     * It's a full width block
     *
     * @return bool
     */
    public function is_full_width() {
        return true;
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $options = [
            [
                'name'      => 'columns',
                'title'     => __( 'Number of Columns', 'weforms' ),
                'type'      => 'range',
                'section'   => 'basic',
                'priority'  => 10,
                'help_text' => __( 'Title of the section', 'weforms' ),
            ],
            [
                'name'      => 'column_space',
                'title'     => __( 'Space Between Columns', 'weforms' ),
                'type'      => 'text',
                'section'   => 'basic',
                'priority'  => 11,
                'help_text' => __( 'Add padding space between columns. e.g: 10', 'weforms' ),
            ],
            [
                'name'      => 'css',
                'title'     => __( 'CSS Class Name', 'weforms' ),
                'type'      => 'text',
                'section'   => 'advanced',
                'priority'  => 22,
                'help_text' => __( 'Provide a container class name for this field. Available classes: wpuf-col-half, wpuf-col-half-last, wpuf-col-one-third, wpuf-col-one-third-last', 'weforms' ),
            ],
        ];

        return apply_filters( 'wpuf_text_field_option_settings', $options );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $props = [
            'input_type'        => 'column_field',
            'template'          => $this->get_type(),
            'id'                => 0,
            'is_new'            => true,
            'is_meta'           => 'no',
            'columns'           => 3,
            'min_column'        => 1,
            'max_column'        => 3,
            'column_space'      => '5',
            'inner_fields'      => [
                'column-1'   => [],
                'column-2'   => [],
                'column-3'   => [],
            ],
            'inner_columns_size' => [
                'column-1'   => '33.33%',
                'column-2'   => '33.33%',
                'column-3'   => '33.33%',
            ],
        ];

        return $props;
    }
}
