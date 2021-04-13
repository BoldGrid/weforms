<table class="form-table">
    <tr class="wpuf-humanpresence">
        <th><?php esc_html_e( 'Human Presence', 'weforms' ); ?></th>
        <td>
            <label class="weforms-switch" v-if="has_humanpresence_installed()">
                <input id="humanpresence_toggle" type="checkbox" v-model="settings.humanpresence_enabled" :true-value="'true'" :false-value="'false'" @change="change_humanpresence">
                <span class="switch-slider round"></span>
                <?php esc_html_e( 'Enable HP Anti-Spam', 'weforms' ); ?>
            </label>
            <p v-else v-html="no_humanpresence_installed_msg()"></p>
        </td>
    </tr>
    <?php
    /**
     * @since 1.6.4
     */
    do_action( 'weforms_humanpresence_settings_form' );
    ?>
</table>