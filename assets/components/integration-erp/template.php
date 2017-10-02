<div>

    <?php if ( !function_exists( 'erp_crm_get_contact_groups' ) ) : ?>

        <p>
            <?php printf( __( '<a href="%s" target="_blank">WP ERP</a> plugin is not installed. Please install the plugin first.', 'weforms' ), 'https://wordpress.org/plugins/erp/' ); ?>
        </p>

    <?php else : ?>
        <?php
        $erp_contact_groups = erp_crm_get_contact_groups( array(
            'number'  => -1,
            'orderby' => 'name',
            'order'   => 'ASC',
        ) );
        ?>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field-label">
                <label for="erp-group-id"><?php _e( 'Contact Group', 'weforms' ); ?></label>
            </div>
            <div class="wpuf-int-field">
                <ul style="margin: 0;">
                    <?php foreach ( $erp_contact_groups as $group ): ?>
                        <li>
                            <label>
                                <input type="checkbox" v-model="settings.group" class="checkbox" value="<?php echo $group->id; ?>">
                                <?php echo $group->name; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field-label">
                <label for="erp-group-id"><?php _e( 'Life Stage', 'weforms' ); ?></label>
            </div>
            <div class="wpuf-int-field">
                <select v-model="settings.stage">
                    <?php echo erp_crm_get_life_stages_dropdown(); ?>
                </select>
            </div>
        </div>

        <fieldset>
            <legend><?php _e( 'Mapping Fields', 'weforms' ); ?></legend>

            <p class="description" style="padding: 0 0 10px 0;">
                <?php _e( 'Please map the form input fields with ERP required fields', 'weforms' ); ?>
            </p>

            <div class="wpuf-int-form-row mapping-fields">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Email Address', 'default' ); ?> <span class="required">*</span></label>
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
                    <label><?php _e( 'First Name', 'default' ); ?></label>
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
                    <label><?php _e( 'Last Name', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.last_name">
                        <wpuf-merge-tags v-on:insert="insertValue" field="last_name"></wpuf-merge-tags>
                    </div>
                </div>
            </div>
        </fieldset>

    <?php endif; ?>
</div>