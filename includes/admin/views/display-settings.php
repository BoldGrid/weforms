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
    <tr class="wpuf-modal-form">
        <th><?php _e( 'Modal Form', 'weforms' ); ?></th>
        <td>
            <label>
                <input type="checkbox" v-model="settings.modal_form" :true-value="'true'" :false-value="'false'">
                <?php _e( 'Enable modal form', 'weforms' ); ?>
            </label>

            <p class="description">
                <?php _e( 'The form will apper in modal.', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-appearance" v-show="settings.modal_form == 'true'">
        <th><?php _e( 'appearance', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.appearance">
                <?php
                $appearance_options = array(
                    'button' => __( 'Button', 'weforms' ),
                    'link' => __( 'Link', 'weforms' )
                );

                foreach ($appearance_options as $to => $label) {
                    printf('<option value="%s"%s>%s</option>', $to, '', $label );
                }
                ?>
            </select>
            <p class="description">
                <?php _e( 'The modal form will display upon click on this button / link', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-same-page" v-show="settings.modal_form == 'true'">
        <th><?php _e( 'Button / Link text', 'weforms' ); ?></th>
        <td>
            <input type="text" value="" v-model="settings.modal_text">
        </td>
    </tr>

</table>
