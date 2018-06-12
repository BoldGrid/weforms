<h3 class="hndle"><?php _e( 'Privacy', 'weforms' ); ?></h3>

    <div class="inside">
        <table class="form-table">
            <tr>
                <th><?php _e( 'Export Payment Data', 'weforms' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" v-model="settings.privacy_payment_export">
                        <?php _e( 'Allow Exporting Payment Data of User.', 'weforms' ); ?>
                    </label>
                    <p class="help"></p>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Erase Payment Data', 'weforms' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" v-model="settings.privacy_payment_erase">
                        <?php _e( 'Allow Erasing Payment Data of User.', 'weforms' ); ?>
                    </label>
                    <p class="help"></p>
                </td>
            </tr>
        </table>
    </div>

<div class="submit-wrapper">
    <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php _e( 'Save Changes', 'weforms' ); ?></button>
</div>
