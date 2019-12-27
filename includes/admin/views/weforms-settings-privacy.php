<h3 class="hndle"><?php esc_html_e( 'Privacy', 'weforms' ); ?></h3>

    <div class="inside">
        <table class="form-table">
            <tr>
                <th><?php esc_html_e( 'Export Payment Data', 'weforms' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" v-model="settings.privacy_payment_export">
                        <?php esc_html_e( 'Allow Exporting Payment Data of User.', 'weforms' ); ?>
                    </label>
                    <p class="help"></p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Erase Payment Data', 'weforms' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" v-model="settings.privacy_payment_erase">
                        <?php esc_html_e( 'Allow Erasing Payment Data of User.', 'weforms' ); ?>
                    </label>
                    <p class="help"></p>
                </td>
            </tr>
        </table>
    </div>

<div class="submit-wrapper">
    <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php esc_html_e( 'Save Changes', 'weforms' ); ?></button>
</div>
