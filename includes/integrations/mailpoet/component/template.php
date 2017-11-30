<div>
    <?php if ( class_exists( 'WYSIJA' ) ) { ?>
    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailpoet-list-id"><?php _e( 'List', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <select v-model="settings.list" id="mailpoet-list-id">
                <option value=""><?php _e( '&mdash; Select List &mdash;', 'weforms' ); ?></option>
                <option v-for="list in lists" :value="list.list_id">{{ list.name }}</option>
            </select>

            <span class="description"><?php _e( 'Select your mailpoet list for subscription', 'weforms' ); ?></span>
        </div>
    </div>

    <fieldset>
        <legend><?php _e( 'Mapping Fields', 'weforms' ); ?></legend>

        <p class="description" style="padding: 0 0 10px 0;">
            <?php _e( 'Please map the form input fields with mailpoet required fields', 'weforms' ); ?>
        </p>

        <div class="wpuf-int-form-row mapping-fields">
            <div class="wpuf-int-field-label">
                <label for="mailpoet-list-id"><?php _e( 'Email Address', 'weforms' ); ?> <span class="required">*</span></label>
            </div>
            <div class="wpuf-int-field">
                <div class="wpuf-int-field-small">
                    <input type="email" class="regular-text" v-model="settings.fields.email">
                    <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="email"></wpuf-merge-tags>
                </div>
            </div>
        </div>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field-label">
                <label for="mailpoet-list-id"><?php _e( 'First Name', 'weforms' ); ?></label>
            </div>
            <div class="wpuf-int-field">
                <div class="wpuf-int-field-small">
                    <input type="text" class="regular-text" v-model="settings.fields.first_name">
                    <wpuf-merge-tags v-on:insert="insertValue" field="first_name"></wpuf-merge-tags>
                </div>
            </div>
        </div>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field-label">
                <label for="mailpoet-list-id"><?php _e( 'Last Name', 'weforms' ); ?></label>
            </div>
            <div class="wpuf-int-field">
                <div class="wpuf-int-field-small">
                    <input type="text" class="regular-text" v-model="settings.fields.last_name">
                    <wpuf-merge-tags v-on:insert="insertValue" field="last_name"></wpuf-merge-tags>
                </div>
            </div>
        </div>
    </fieldset>
    <?php } else {
        printf( __( '%sMailPoet Newsletters%s plugin does not exists. Please install the plugin .', 'weforms' ), '<a href="https://wordpress.org/plugins/wysija-newsletters/" target="_blank" >', '</a>' );
    } ?>
</div>