<table class="form-table">
    <tr class="wpuf-redirect-to">
        <th><?php _e( 'Redirect To', 'best-contact-form' ); ?></th>
        <td>
            <select v-model="settings.redirect_to">
                <?php
                $redirect_options = array(
                    'same' => __( 'Same Page', 'best-contact-form' ),
                    'page' => __( 'To a page', 'best-contact-form' ),
                    'url'  => __( 'To a custom URL', 'best-contact-form' )
                );

                foreach ($redirect_options as $to => $label) {
                    printf('<option value="%s"%s>%s</option>', $to, '', $label );
                }
                ?>
            </select>
            <p class="description">
                <?php _e( 'After successfull submit, where the page will redirect to', 'best-contact-form' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-same-page" v-show="settings.redirect_to == 'same'">
        <th><?php _e( 'Message to show', 'best-contact-form' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-page-id" v-show="settings.redirect_to == 'page'">
        <th><?php _e( 'Page', 'best-contact-form' ); ?></th>
        <td>
            <?php $dropdown = wp_dropdown_pages( array(
                'name'             => 'wpuf_settings[page_id]',
                'show_option_none' => __( '&mdash; Select a page &mdash;', 'best-contact-form' ),
                'echo'             => false
            ) );

            echo str_replace('<select', '<select v-model="settings.page_id"', $dropdown );
            ?>
        </td>
    </tr>

    <tr class="wpuf-url" v-show="settings.redirect_to == 'url'">
        <th><?php _e( 'Custom URL', 'best-contact-form' ); ?></th>
        <td>
            <input type="url" v-model="settings.url">
        </td>
    </tr>

    <tr class="wpuf-submit-text">
        <th><?php _e( 'Submit Button text', 'best-contact-form' ); ?></th>
        <td>
            <input type="text" v-model="settings.submit_text">
        </td>
    </tr>
</table>