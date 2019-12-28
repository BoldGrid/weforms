<table class="form-table">

    <tr class="wpuf-label-position">
        <th><?php esc_html_e( 'Label Position', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.label_position">
                <?php
                $positions = [
                    'above'  => __( 'Above Element', 'weforms' ),
                    'left'   => __( 'Left of Element', 'weforms' ),
                    'right'  => __( 'Right of Element', 'weforms' ),
                    'hidden' => __( 'Hidden', 'weforms' ),
                ];

                foreach ($positions as $to => $label) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $to ), '', esc_attr( $label ) );
                }
                ?>
            </select>

            <p class="description">
                <?php esc_html_e( 'Where the labels of the form should display', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-use-theme-css">
        <th><?php esc_html_e( 'Use Theme CSS', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.use_theme_css">
                <?php
                $options = [
                    'wpuf-theme-style'  => __( 'Yes', 'weforms' ),
                    'wpuf-style'        => __( 'No', 'weforms' ),
                ];

                foreach ($options as $key => $label) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), '', esc_attr( $label ) );
                }
                ?>
            </select>

            <p class="description">
                <?php wp_kses_post( __( "Selecting <strong>Yes</strong> will use your theme's style for form fields.", "weforms" ) ) ?>
            </p>
        </td>
    </tr>

</table>
