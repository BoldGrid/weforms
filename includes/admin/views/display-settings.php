<table class="form-table">

    <tr class="wpuf-label-position">
        <th><?php _e( 'Label Position', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.label_position">
                <?php
                $positions = array(
                    'above'  => __( 'Above Element', 'weforms' ),
                    'left'   => __( 'Left of Element', 'weforms' ),
                    'right'  => __( 'Right of Element', 'weforms' ),
                    'hidden' => __( 'Hidden', 'weforms' ),
                );

                foreach ($positions as $to => $label) {
                    printf('<option value="%s"%s>%s</option>', $to, '', $label );
                }
                ?>
            </select>

            <p class="description">
                <?php _e( 'Where the labels of the form should display', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-use-theme-css">
        <th><?php _e( 'Use Theme CSS', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.use_theme_css">
                <?php
                $options = array(
                    'wpuf-theme-style'  => __( 'Yes', 'weforms' ),
                    'wpuf-style'        => __( 'No', 'weforms' ),
                );

                foreach ($options as $key => $label) {
                    printf('<option value="%s"%s>%s</option>', $key, '', $label );
                }
                ?>
            </select>

            <p class="description">
                <?php _e( "Selecting <strong>Yes</strong> will use your theme's style for form fields.", "weforms" ) ?>
            </p>
        </td>
    </tr>

</table>
