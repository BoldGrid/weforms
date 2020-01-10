<table class="form-table">
    <tr class="wpuf-redirect-to">
        <th><?php esc_html_e( 'Redirect To', 'weforms' ); ?></th>
        <td>
            <select v-model="settings.redirect_to">
                <?php
                $redirect_options = [
                    'same' => __( 'Same Page', 'weforms' ),
                    'page' => __( 'To a page', 'weforms' ),
                    'url'  => __( 'To a custom URL', 'weforms' ),
                ];
                foreach ($redirect_options as $to => $label) {
                    printf( '<option value="%s"%s>%s</option>', esc_html( $to ), '', esc_html( $label )  );
                }
                ?>
            </select>
            <p class="description">
                <?php esc_html_e( 'After successful submit, where the page will redirect to. This redirect option will not work if Show Report in Frontend option is enabled.', 'weforms' ) ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-same-page" v-show="settings.redirect_to == 'same'">
        <th><?php esc_html_e( 'Message to show', 'weforms' ); ?></th>
        <td>
            <textarea rows="3" cols="40" v-model="settings.message"></textarea>
        </td>
    </tr>

    <tr class="wpuf-page-id" v-show="settings.redirect_to == 'page'">
        <th><?php esc_html_e( 'Page', 'weforms' ); ?></th>
        <td>
            <?php $dropdown = wp_dropdown_pages( [
                'name'             => 'wpuf_settings[page_id]',
                'show_option_none' => wp_kses_post( __('&mdash; Select a page &mdash;', 'weforms') ),
                'echo'             => false
            ] );

             echo str_replace('<select', '<select v-model="settings.page_id"', $dropdown ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

            ?>
        </td>
    </tr>

    <tr class="wpuf-url" v-show="settings.redirect_to == 'url'">
        <th><?php esc_html_e( 'Custom URL', 'weforms' ); ?></th>
        <td>
            <input type="url" v-model="settings.url" class="regular-text">
        </td>
    </tr>

    <tr class="wpuf-submit-text">
        <th><?php esc_html_e( 'Submit Button text', 'weforms' ); ?></th>
        <td>
            <input type="text" v-model="settings.submit_text" class="regular-text">
        </td>
    </tr>

    <?php
    /**
     * @since 1.1.0
     */
    do_action( 'weforms_form_settings_form' );
    ?>
</table>
