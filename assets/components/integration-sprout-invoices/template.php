<div>

    <?php if (!function_exists( 'sprout_invoices_load' )) { ?>

        <p>
            <?php printf( __( '<a href="%s" target="_blank">Sprout Invoices</a> plugin is not installed. Please install the plugin first.', 'weforms' ), 'https://wordpress.org/plugins/sprout-invoices/' ); ?>
        </p>

    <?php } else { ?>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field-label">
                <label for="sprout-invoices-doctype"><?php _e( 'Create New', 'weforms' ); ?></label>
            </div>
            <div class="wpuf-int-field">
                <select v-model="settings.doctype">
                    <option value="estimate"><?php _e( 'Quote', 'weforms' ) ?></option>
                    <option value="invoice"><?php _e( 'Invoice', 'weforms' ) ?></option>
                    <option value="client"><?php _e( 'Client (only)', 'weforms' ) ?></option>
                </select>
            </div>
        </div>


        <fieldset>
            <legend><?php _e( 'Mapping Fields', 'weforms' ); ?></legend>

            <p class="description" style="padding: 0 0 10px 0;">
                <?php _e( 'Please map the form input fields with Sprout Invoices fields', 'weforms' ); ?>
            </p>

            <div class="wpuf-int-form-row mapping-fields">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Invoice/Quote Title', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="email" class="regular-text" v-model="settings.fields.subject">
                        <wpuf-merge-tags v-on:insert="insertValue" field="subject"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

            <div class="wpuf-int-form-row mapping-fields">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Client Name', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="email" class="regular-text" v-model="settings.fields.client_name">
                        <wpuf-merge-tags v-on:insert="insertValue" field="client_name"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

            <div class="wpuf-int-form-row mapping-fields">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Email Address', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="email" class="regular-text" v-model="settings.fields.email">
                        <wpuf-merge-tags filter="email_address" v-on:insert="insertValue"
                                         field="email"></wpuf-merge-tags>
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

            <?php if (class_exists( 'WeForms_Pro' )) : ?>

                <div class="wpuf-int-form-row">
                    <div class="wpuf-int-field-label">
                        <label><?php _e( 'Address', 'default' ); ?></label>
                    </div>
                    <div class="wpuf-int-field">
                        <div class="wpuf-int-field-small">
                            <input type="text" class="regular-text" v-model="settings.fields.address">
                            <wpuf-merge-tags v-on:insert="insertValue" field="address"></wpuf-merge-tags>
                        </div>
                    </div>
                </div>

            <?php endif ?>

            <div class="wpuf-int-form-row">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Notes', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.notes">
                        <wpuf-merge-tags v-on:insert="insertValue" field="notes"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

            <div class="wpuf-int-form-row">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Due Date', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.duedate">
                        <wpuf-merge-tags filter="date_field" v-on:insert="insertValue"
                                         field="duedate"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

            <div class="wpuf-int-form-row">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'Quote/Invoice #', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.number">
                        <wpuf-merge-tags v-on:insert="insertValue" field="number"></wpuf-merge-tags>
                    </div>
                </div>
            </div>


            <div class="wpuf-int-form-row">
                <div class="wpuf-int-field-label">
                    <label><?php _e( 'VAT Number', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.vat">
                        <wpuf-merge-tags v-on:insert="insertValue" field="vat"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

            <div class="wpuf-int-form-row">
                <div class="wpuf-int-field-label">
                    <label><?php  _e( 'Line Items', 'default' ); ?></label>
                </div>
                <div class="wpuf-int-field">
                    <div class="wpuf-int-field-small">
                        <input type="text" class="regular-text" v-model="settings.fields.line_items">
                        <wpuf-merge-tags v-on:insert="insertValue"
                                         field="line_items"></wpuf-merge-tags>
                    </div>
                </div>
            </div>

        </fieldset>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field">
                <label for="create-client-and-user" class="weforms-switch">
                    <input type="checkbox" id="create-client-and-user" v-model="settings.create_user_and_client">
                    <span class="switch-slider round"></span>
                    <?php _e( 'Create Client and User', 'default' ); ?>
                </label>
            </div>
        </div>

        <div class="wpuf-int-form-row">
            <div class="wpuf-int-field">
                <label for="redirect" class="weforms-switch">
                    <input type="checkbox" id="redirect" v-model="settings.redirect">
                    <span class="switch-slider round"></span>
                    <?php _e( 'Redirect to Quote/Invoice after Submission', 'default' ); ?>
                </label>
            </div>
        </div>

    <?php } ?>
</div>
