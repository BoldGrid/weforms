<div>
    <!-- <pre>{{ notifications.length }}</pre> -->
    <a href="#" class="button button-secondary add-notification" v-on:click.prevent="addNew"><span class="dashicons dashicons-plus-alt"></span> <?php _e( 'Add Notification', 'wpuf-contact-form' ); ?></a>

    <div :class="[editing ? 'editing' : '', 'notification-wrap']">
    <!-- notification-wrap -->

        <div class="notification-table-wrap">
            <table class="wp-list-table widefat fixed striped posts wpuf-cf-notification-table">
                <thead>
                    <tr>
                        <th class="col-toggle">&nbsp;</th>
                        <th class="col-name"><?php _e( 'Name', 'wpuf-contact-form' ); ?></th>
                        <th class="col-subject"><?php _e( 'Subject', 'wpuf-contact-form' ); ?></th>
                        <th class="col-action">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(notification, index) in notifications">
                        <td class="col-toggle">
                            <a href="#" v-on:click.prevent="toggelNotification(index)">
                                <img v-if="notification.active" src="<?php echo WPUF_CONTACT_FORM_ASSET_URI; ?>/images/active.png" width="24" alt="status">
                                <img v-else src="<?php echo WPUF_CONTACT_FORM_ASSET_URI; ?>/images/inactive.png" width="24" alt="status">
                            </a>
                        </td>
                        <td class="col-name"><a href="#" v-on:click.prevent="editItem(index)">{{ notification.name }}</a></td>
                        <td class="col-subject">{{ notification.subject }}</td>
                        <td class="col-action">
                            <a href="#" v-on:click.prevent="duplicate(index)" title="<?php esc_attr_e( 'Duplicate', 'wpuf-contact-form' ); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                            <a href="#" v-on:click.prevent="editItem(index)" title="<?php esc_attr_e( 'Settings', 'wpuf-contact-form' ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
                        </td>
                    </tr>
                    <tr v-if="!notifications.length">
                        <td colspan="4">No notifications found</td>
                    </tr>
                </tbody>
            </table>
        </div><!-- .notification-table-wrap -->

        <div class="notification-edit-area" v-if="notifications[editingIndex]">

            <div class="notification-head">
                <input type="text" name="" v-model="notifications[editingIndex].name" v-on:keyup.enter="editDone()" value="Admin Notification">
            </div>

            <div class="form-fields">
                <div class="notification-row">
                    <div class="row-one-half notification-field first">
                        <label for="notification-title">To</label>
                        <input type="text" v-model="notifications[editingIndex].to">
                        <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="to"></wpuf-merge-tags>
                    </div>

                    <div class="row-one-half notification-field">
                        <label for="notification-title">Reply To</label>
                        <input type="email" v-model="notifications[editingIndex].replyTo">
                        <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="replyTo"></wpuf-merge-tags>
                    </div>
                </div>

                <div class="notification-row notification-field">
                    <label for="notification-title">Subject</label>
                    <input type="text" v-model="notifications[editingIndex].subject">
                    <wpuf-merge-tags v-on:insert="insertValue" field="subject"></wpuf-merge-tags>
                </div>

                <div class="notification-row notification-field">
                    <label for="notification-title">Email Message</label>
                    <textarea name="" rows="6" v-model="notifications[editingIndex].message"></textarea>
                    <wpuf-merge-tags v-on:insert="insertValue" field="message"></wpuf-merge-tags>
                </div>

                <section class="advanced-fields">
                    <a href="#" class="field-toggle" v-on:click.prevent="toggleAdvanced()"><span class="dashicons dashicons-arrow-right"></span> Advanced</a>

                    <div class="advanced-field-wrap">
                        <div class="notification-row">
                            <div class="row-one-half notification-field first">
                                <label for="notification-title">From Name</label>
                                <input type="text" v-model="notifications[editingIndex].fromName">
                                <wpuf-merge-tags v-on:insert="insertValue" field="fromName"></wpuf-merge-tags>
                            </div>

                            <div class="row-one-half notification-field">
                                <label for="notification-title">From Address</label>
                                <input type="email" name="" v-model="notifications[editingIndex].fromAddress">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="fromAddress"></wpuf-merge-tags>
                            </div>
                        </div>

                        <div class="notification-row">
                            <div class="row-one-half notification-field first">
                                <label for="notification-title">CC</label>
                                <input type="email" name="" v-model="notifications[editingIndex].cc">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="cc"></wpuf-merge-tags>
                            </div>

                            <div class="row-one-half notification-field">
                                <label for="notification-title">BCC</label>
                                <input type="email" name="" v-model="notifications[editingIndex].bcc">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="bcc"></wpuf-merge-tags>
                            </div>
                        </div>
                    </div>
                </section><!-- .advanced-fields -->
            </div>

            <div class="submit-area">
                <a href="#" v-on:click.prevent="deleteItem(editingIndex)" title="<?php esc_attr_e( 'Delete', 'wpuf-contact-form' ); ?>"><span class="dashicons dashicons-trash"></span></a>
                <button class="button button-secondary" v-on:click.prevent="editDone()">Done</button>
            </div>
        </div><!-- .notification-edit-area -->

    </div><!-- .notification-wrap -->
</div>