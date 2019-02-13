<script type="text/x-template" id="tmpl-wpuf-dynamic-field">
<div>

    <div class="panel-field-opt panel-field-opt-text">
        <label>
            <?php _e( 'Dynamic value population', 'weforms' ); ?>
            <help-text text="<?php _e( "Field value or options can be populated dynamically through filter hook or query string", 'weforms' ) ?>"></help-text>
        </label>

        <ul>
            <li>
                <label>
                    <input type="checkbox" value="yes" v-model="dynamic.status"> <?php _e( 'Allow field to be populated dynamically', 'weforms' ); ?>
                </label>
            </li>
        </ul>
    </div>


	<template v-if="dynamic.status">

		<div class="panel-field-opt panel-field-opt-text"><label>
            <?php _e( 'Parameter Name', 'weforms' ); ?>
	        <help-text text="<?php _e( "Enter a Parameter Name, using that the field value can be populated through filter hook or query string", 'weforms' ) ?>"></help-text>
	         <input type="text" v-model="dynamic.param_name">
	     	</label>
     	</div>
	</template>

</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-field-name">
<div>
    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'First Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.first_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'first_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.first_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'first_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix" v-if="editing_form_field.format !== 'first-last'">

        <label>
            <?php _e( 'Middle Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.middle_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'middle_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.middle_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'middle_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'Last Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">

            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.last_name.placeholder">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'last_name', type: 'placeholder'}">
                </wpuf-merge-tags>
            </div>

            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <div class="name-merge-tag-holder">
                <input type="text" v-model="editing_form_field.last_name.default">
                <wpuf-merge-tags
                    filter="no_fields"
                    v-on:insert="insertValue"
                    :field="{name: 'last_name', type: 'default'}">
                </wpuf-merge-tags>
            </div>
            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-field-text-with-tag">
<div v-if="met_dependencies" class="panel-field-opt panel-field-opt-text field-text-with-tag">
    <label>
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>

        <input
            v-if="option_field.variation && 'number' === option_field.variation"
            type="number"
            v-model="value"
            @focusout="on_focusout"
            @keyup="on_keyup"
        >

        <input
            v-if="!option_field.variation"
            type="text"
            v-model="value"
            @focusout="on_focusout"
            @keyup="on_keyup"
        >

       <wpuf-merge-tags :filter="option_field.tag_filter" v-on:insert="insertValue"></wpuf-merge-tags>
    </label>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-date_field">
<div class="wpuf-fields">
    <input
        type="text"
        :class="class_names('datepicker')"
        :placeholder="field.format"
        :value="field.default"
        :size="field.size"
    >
    <span v-if="field.help" class="wpuf-help">{{ field.help }}</span>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-name_field">
<div class="wpuf-fields">

    <div :class="['wpuf-name-field-wrap', 'format-' + field.format]">
        <div class="wpuf-name-field-first-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.first_name.placeholder"
                :value="field.first_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.first_name.sub }}</label>
        </div>

        <div class="wpuf-name-field-middle-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.middle_name.placeholder"
                :value="field.middle_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.middle_name.sub }}</label>
        </div>

        <div class="wpuf-name-field-last-name">
            <input
                type="text"
                :class="class_names('textfield')"
                :placeholder="field.last_name.placeholder"
                :value="field.last_name.default"
                :size="field.size"
            >
            <label class="wpuf-form-sub-label" v-if="!field.hide_subs">{{ field.last_name.sub }}</label>
        </div>
    </div>

    <span v-if="field.help" class="wpuf-help">{{ field.help }}</span>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-notification">
<div>
    <!-- <pre>{{ notifications.length }}</pre> -->
    <a href="#" class="button button-secondary add-notification" v-on:click.prevent="addNew"><span class="dashicons dashicons-plus-alt"></span> <?php _e( 'Add Notification', 'weforms' ); ?></a>

    <div :class="[editing ? 'editing' : '', 'notification-wrap']">
    <!-- notification-wrap -->

        <div class="notification-table-wrap">
            <table class="wp-list-table widefat fixed striped posts wpuf-cf-notification-table">
                <thead>
                    <tr>
                        <th class="col-toggle">&nbsp;</th>
                        <th class="col-name"><?php _e( 'Name', 'weforms' ); ?></th>
                        <th class="col-subject"><?php _e( 'Subject', 'weforms' ); ?></th>
                        <th class="col-action">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(notification, index) in notifications">
                        <td class="col-toggle">
                            <a href="#" v-on:click.prevent="toggelNotification(index)">
                                <img v-if="notification.active" src="<?php echo WPUF_ASSET_URI; ?>/images/active.png" width="24" alt="status">
                                <img v-else src="<?php echo WPUF_ASSET_URI; ?>/images/inactive.png" width="24" alt="status">
                            </a>
                        </td>
                        <td class="col-name"><a href="#" v-on:click.prevent="editItem(index)">{{ notification.name }}</a></td>
                        <td class="col-subject">{{ notification.type === 'email' ? notification.subject : notification.smsText }}</td>
                        <td class="col-action">
                            <a href="#" v-on:click.prevent="duplicate(index)" title="<?php esc_attr_e( 'Duplicate', 'weforms' ); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                            <a href="#" v-on:click.prevent="editItem(index)" title="<?php esc_attr_e( 'Settings', 'weforms' ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
                        </td>
                    </tr>
                    <tr v-if="!notifications.length">
                        <td colspan="4"><?php _e( 'No notifications found', 'weforms' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- .notification-table-wrap -->

        <div class="notification-edit-area" v-if="notifications[editingIndex]">

            <div class="notification-head">
                <input type="text" name="" v-model="notifications[editingIndex].name" v-on:keyup.enter="editDone()" value="Admin Notification">
            </div>

            <div class="form-fields">

                <div class="notification-row notification-field">
                    <label for="notification-title"><?php _e( 'Type', 'weforms' ); ?></label>
                    <select type="text" v-model="notifications[editingIndex].type">
                        <option value="email"><?php _e( 'Email Notification', 'weforms' ) ?></option>
                        <option value="sms"><?php _e( 'SMS Notification', 'weforms' ) ?></option>
                    </select>
                </div>

                <template v-if="notifications[editingIndex].type == 'email' ">
                    <div class="notification-row">
                        <div class="row-one-half notification-field first">
                            <label for="notification-title"><?php _e( 'To', 'weforms' ); ?></label>
                            <input type="text" v-model="notifications[editingIndex].to">
                            <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="to"></wpuf-merge-tags>
                        </div>

                        <div class="row-one-half notification-field">
                            <label for="notification-title"><?php _e( 'Reply To', 'weforms' ); ?></label>
                            <input type="email" v-model="notifications[editingIndex].replyTo">
                            <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="replyTo"></wpuf-merge-tags>
                        </div>
                    </div>

                    <div class="notification-row notification-field">
                        <label for="notification-title"><?php _e( 'Subject', 'weforms' ); ?></label>
                        <input type="text" v-model="notifications[editingIndex].subject">
                        <wpuf-merge-tags v-on:insert="insertValue" field="subject"></wpuf-merge-tags>
                    </div>

                    <div class="notification-row notification-field">
                        <label for="notification-title"><?php _e( 'Email Message', 'weforms' ); ?></label>

                        <weforms-text-editor
                            v-model="notifications[editingIndex].message"
                            :i18n="i18n"
                            :editingIndex="editingIndex"
                        >
                        </weforms-text-editor>

                        <wpuf-merge-tags v-on:insert="insertValueEditor" field="message"></wpuf-merge-tags>
                    </div>
                </template>
                <template v-else>
                    <template v-if="has_sms">
                        <div class="notification-row notification-field">
                            <label for="notification-title"><?php _e( 'To', 'weforms' ); ?></label>
                            <input type="text" v-model="notifications[editingIndex].smsTo">
                            <wpuf-merge-tags v-on:insert="insertValue" field="smsTo"></wpuf-merge-tags>
                        </div>
                        <div class="notification-row notification-field">
                            <label for="notification-title"><?php _e( 'SMS Text', 'weforms' ); ?></label>
                            <textarea name="" rows="6" v-model="notifications[editingIndex].smsText"></textarea>
                            <wpuf-merge-tags v-on:insert="insertValue" field="smsText"></wpuf-merge-tags>
                        </div>
                    </template>
                    <template v-else>
                        <p>
                            <label class="wpuf-pro-text-alert">
                                <a :href="pro_link" target="_blank"><?php _e( 'SMS notification moule not found', 'wpuf' ); ?></a>
                            </label>
                        </p>
                    </template>
                </template>

                <section class="advanced-fields">
                    <a href="#" class="field-toggle" v-on:click.prevent="toggleAdvanced()"><span class="dashicons dashicons-arrow-right"></span><?php _e( ' Advanced', 'weforms' ); ?></a>

                    <div class="advanced-field-wrap">
                        <p class="wpuf-pro-text-alert"><?php _e( 'Make sure that your mail server is configured properly for the following "From" fields',  'weforms' ); ?></p>
                        <template v-if="notifications[editingIndex].type == 'email' ">
                            <div class="notification-row">
                                <div class="row-one-half notification-field first">
                                    <label for="notification-title"><?php _e( 'From Name', 'weforms' ); ?></label>
                                    <input type="text" v-model="notifications[editingIndex].fromName">
                                    <wpuf-merge-tags v-on:insert="insertValue" field="fromName"></wpuf-merge-tags>
                                </div>

                                <div class="row-one-half notification-field">
                                    <label for="notification-title"><?php _e( 'From Address', 'weforms' ); ?></label>
                                    <input type="email" name="" v-model="notifications[editingIndex].fromAddress">
                                    <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="fromAddress"></wpuf-merge-tags>
                                </div>
                            </div>

                            <div class="notification-row">
                                <div class="row-one-half notification-field first">
                                    <label for="notification-title"><?php _e( 'CC', 'weforms' ); ?></label>
                                    <input type="email" name="" v-model="notifications[editingIndex].cc">
                                    <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="cc"></wpuf-merge-tags>
                                </div>

                                <div class="row-one-half notification-field">
                                    <label for="notification-title"><?php _e( 'BCC', 'weforms' ); ?></label>
                                    <input type="email" name="" v-model="notifications[editingIndex].bcc">
                                    <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="bcc"></wpuf-merge-tags>
                                </div>
                            </div>
                        </template>

                        <div class="notification-row notification-field">
                            <template v-if="is_pro">

                                <notification-conditional-logics
                                    :notification="notifications[editingIndex]"
                                >
                                </notification-conditional-logics>

                            </template>
                            <template v-else>
                                <label class="wpuf-pro-text-alert">
                                    <a :href="pro_link" target="_blank"><?php _e( 'Conditional Logics available in Pro Version', 'wpuf' ); ?></a>
                                </label>
                            </template>
                        </div>
                    </div>
                </section><!-- .advanced-fields -->
            </div>

            <div class="submit-area">
                <a href="#" v-on:click.prevent="deleteItem(editingIndex)" title="<?php esc_attr_e( 'Delete', 'weforms' ); ?>"><span class="dashicons dashicons-trash"></span></a>
                <button class="button button-secondary" v-on:click.prevent="editDone()"><?php _e( 'Done', 'weforms' ); ?></button>
            </div>
        </div><!-- .notification-edit-area -->

    </div><!-- .notification-wrap -->
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-integration">
<div class="wpuf-integrations-wrap">

    <template v-if="hasIntegrations">
        <div :class="['wpuf-integration', isAvailable(integration.id) ? '' : 'collapsed']" v-for="integration in integrations">
            <div :class="['wpuf-panel', isActive(integration.id) ? 'panel-checked' : '']">
                <div class="wpuf-panel-body">
                    <span class="premium-badge" v-if="!isAvailable(integration.id)"><?php _e( 'Premium', 'weforms' ); ?></span>
                    <img class="icon" :src="integration.icon" :alt="integration.title">
                </div>
                <div class="wpuf-panel-footer">
                    <div class="wpuf-setting">
                        <a href="#" @click.prevent="openModal($event.target)" title="<?php _e('Settings', 'weforms'); ?>">
                            <svg width="21px" height="21px" viewBox="0 0 21 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Individual-Form-Integration-Page-Design---weForms" transform="translate(-203.000000, -290.000000)" fill="#CCCCCC" fill-rule="nonzero">
                                    <g id="Group-4" transform="translate(183.000000, 116.000000)">
                                        <path d="M40.9342889,183.334097 C40.9010806,183.038903 40.5568864,182.817077 40.259262,182.817077 C39.2970029,182.817077 38.4431201,182.252081 38.0850175,181.378295 C37.7191793,180.483334 37.9550754,179.439603 38.6722182,178.781783 C38.8979565,178.575428 38.9253826,178.229992 38.7360563,177.990038 C38.2435578,177.364644 37.6837832,176.799726 37.0725944,176.310131 C36.8332603,176.118075 36.4821119,176.144797 36.2745796,176.374592 C35.648701,177.06773 34.5244636,177.325342 33.6557347,176.962873 C32.7516878,176.582588 32.1815991,175.666531 32.237389,174.683199 C32.2557512,174.374331 32.030013,174.10578 31.7220744,174.069916 C30.9377337,173.979201 30.1465168,173.976388 29.3598319,174.063665 C29.0554096,174.097263 28.8296713,174.359485 28.8399073,174.664447 C28.8741314,175.63809 28.2971666,176.53813 27.4021055,176.90474 C26.5437688,177.255333 25.4274233,176.999909 24.8027948,176.312944 C24.5963565,176.086664 24.2509121,176.058848 24.0104059,176.246294 C23.3810893,176.740031 22.8087346,177.305417 22.3117041,177.925655 C22.1178459,178.16678 22.1463659,178.516279 22.3743701,178.723728 C23.105187,179.385534 23.3411612,180.438328 22.9614927,181.343602 C22.5990145,182.206684 21.7027813,182.762929 20.6767623,182.762929 C20.3438199,182.752224 20.1066736,182.975691 20.0702617,183.278387 C19.9779036,184.06724 19.9768096,184.871017 20.0657297,185.666278 C20.0987036,185.962723 20.4533682,186.182595 20.7542743,186.182595 C21.6686353,186.159233 22.5465063,186.725323 22.9147667,187.621456 C23.2818551,188.516417 23.0458809,189.559522 22.3276441,190.217889 C22.1029998,190.424245 22.0744797,190.769134 22.2638061,191.009087 C22.7516945,191.630496 23.3115472,192.195961 23.9249239,192.689619 C24.1655082,192.883473 24.5155626,192.856126 24.7241107,192.62633 C25.3523334,191.931473 26.4764927,191.67433 27.3417836,192.037503 C28.2480965,192.416615 28.8181852,193.332594 28.7623952,194.316473 C28.7441893,194.625498 28.9710214,194.894517 29.2777098,194.929835 C29.6789441,194.976638 30.0826006,195 30.4873511,195 C30.8715515,195 31.25583,194.978903 31.6400304,194.936164 C31.9445309,194.902565 32.1701129,194.640344 32.1598769,194.334835 C32.1246371,193.361739 32.7026176,192.461699 33.5965067,192.095713 C34.4606255,191.742777 35.5722828,192.001092 36.1969894,192.687353 C36.4045998,192.913164 36.7476219,192.940433 36.9894564,192.753612 C37.617601,192.261048 38.1887836,191.69613 38.6881582,191.074173 C38.8819384,190.833595 38.8545904,190.483549 38.6254141,190.276178 C37.8945972,189.614373 37.657451,188.561423 38.0371194,187.656773 C38.39405,186.805177 39.2569967,186.233383 40.185188,186.233383 L40.3150519,186.236743 C40.6161144,186.2612 40.8931106,186.029294 40.9296007,185.721988 C41.0221151,184.932432 41.023209,184.129358 40.9342889,183.334097 Z M30.5166525,188.024555 C28.5852583,188.024555 27.0142326,186.453568 27.0142326,184.522222 C27.0142326,182.590953 28.5852583,181.019888 30.5166525,181.019888 C32.4479686,181.019888 34.0189943,182.590953 34.0189943,184.522222 C34.0189943,186.453568 32.4479686,188.024555 30.5166525,188.024555 Z" id="Shape" transform="translate(30.500000, 184.500000) scale(-1, 1) translate(-30.500000, -184.500000) "></path>
                                    </g>
                                </g>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <div :class="['wpuf-toggle-switch', 'big', isActive(integration.id) ? 'checked' : '']" v-on:click="toggleState(integration.id, $event.target)">
                        <span class="toggle-indicator"></span>
                    </div>
                </div>
            </div>

            <div :id="integration.id" class="wf-modal" role="dialog">
                <div class="wf-modal-dialog">
                    <div class="wf-modal-content">
                        <div class="wf-modal-header">
                            <div class="modal-header-left">
                                <div :class="['wpuf-toggle-switch', 'big', isActive(integration.id) ? 'checked' : '']" v-on:click="toggleState(integration.id, $event.target)">
                                    <span class="toggle-indicator"></span>
                                </div>
                                <img class="icon" height="30px" :src="integration.icon" :alt="integration.title">
                            </div>
                            <span class="modal-close" @click.prevent="hideModal($event.target)">x</span>
                        </div>
                        <div class="wf-modal-body">
                            <div v-if="isAvailable(integration.id)">
                                <component :is="'wpuf-integration-' + integration.id" :id="integration.id"></component>
                            </div>
                            <div v-else>
                                <?php _e( 'This feature is available on the premium version only.', 'best-contact-form' ); ?>
                                <a class="button" :href="pro_link" target="_blank"><?php _e( 'Upgrade to Pro', 'best-contact-form' ); ?></a>
                            </div>
                        </div>
                        <div class="wf-modal-footer">
                            <button type="button" class="button button-primary" @click="save_form_builder($event.target)">
                                <?php _e( 'Save Form', 'weforms' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </template>

    <div v-else>
        <?php _e( 'No integration found.', 'best-contact-form' ); ?>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-integration-erp">
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
</div></script>

<script type="text/x-template" id="tmpl-wpuf-integration-slack">
<div>
    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailchimp-list-id"><?php _e( 'Slack Webhook URL', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <input type="url" class="regular-text" v-model="settings.url" placeholder="https://hooks.slack.com/services/...">
            <p class="help"><?php printf( __( '%sSlack webhook URL%s to send our JSON payloads (%sView documentation%s)', 'weforms' ), '<a href="https://api.slack.com/incoming-webhooks" target="_blank" >', '</a>', '<a href="https://wedevs.com/docs/weforms/integrations/slack/" target="_blank" >', '</a>' ); ?></p>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-merge-tags">
<div class="wpuf-merge-tag-wrap">
    <a href="#" v-on:click.prevent="toggleFields($event)" class="merge-tag-link" title="<?php echo esc_attr( 'Click to toggle merge tags', 'wpuf' ); ?>"><span class="dashicons dashicons-editor-code"></span></a>

    <!-- <pre>{{ form_fields.length }}</pre> -->

    <div class="wpuf-merge-tags">
        <div class="merge-tag-section" v-if="!filter || filter !== 'no_fields' ">
            <div class="merge-tag-head"><?php _e( 'Form Fields', 'wpuf' ); ?></div>

            <ul>
                <template v-if="form_fields.length">
                    <li v-for="field in form_fields">

                        <template v-if="field.template === 'name_field'">
                            <a href="#" v-on:click.prevent="insertField('name-full', field.name);">{{ field.label }}</a>
                            (
                            <a href="#" v-on:click.prevent="insertField('name-first', field.name);"><?php _e( 'first', 'wpuf' ); ?></a> |
                            <a href="#" v-on:click.prevent="insertField('name-middle', field.name);"><?php _e( 'middle', 'wpuf' ); ?></a> |
                            <a href="#" v-on:click.prevent="insertField('name-last', field.name);"><?php _e( 'last', 'wpuf' ); ?></a>
                            )
                        </template>

                        <template v-else-if="field.template === 'image_upload'">
                            <a href="#" v-on:click.prevent="insertField('image', field.name);">{{ field.label }}</a>
                        </template>

                        <template v-else-if="field.template === 'file_upload'">
                            <a href="#" v-on:click.prevent="insertField('file', field.name);">{{ field.label }}</a>
                        </template>

                        <a v-else href="#" v-on:click.prevent="insertField('field', field.name);">{{ field.label }} </a>

                    </li>
                </template>
                <li v-else>
                    <?php _e( 'No fields available', 'wpuf' ); ?>
                </li>
            </ul>
        </div><!-- .merge-tag-section -->

        <template v-if="!fieldsonly">

            <?php
            if ( function_exists( 'weforms_get_merge_tags' ) ) {

                $merge_tags = weforms_get_merge_tags();

                foreach ($merge_tags as $section_key => $section) {
                    ?>

                    <div class="merge-tag-section">
                        <div class="merge-tag-head"><?php echo $section['title'] ?></div>

                        <ul>
                            <?php foreach ($section['tags'] as $key => $value) { ?>
                                <li>
                                    <a href="#" v-on:click.prevent="insertField('<?php echo $key; ?>');"><?php echo $value; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div><!-- .merge-tag-section -->

                    <?php
                }
            }
            ?>
        </template>
    </div><!-- .merge-tags -->
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-modal">
<div>
    <div :class="['wpuf-form-template-modal', show ? 'show' : 'hide' ]">

        <span class="screen-reader-text"><?php _e( 'Modal window. Press escape to close.',  'wpuf'  ); ?></span>
        <a href="#" class="close" v-on:click.prevent="closeModal()">× <span class="screen-reader-text"><?php _e( 'Close modal window',  'wpuf'  ); ?></span></a>

        <header class="modal-header">
            <slot name="header"></slot>
        </header>

        <div :class="['content-container', this.$slots.footer ? 'modal-footer' : 'no-footer']">
            <div class="content">
                <slot name="body"></slot>
            </div>
        </div>

        <footer v-if="this.$slots.footer">
            <slot name="footer"></slot>
        </footer>
    </div>
    <div :class="['wpuf-form-template-modal-backdrop', show ? 'show' : 'hide' ]"></div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-template-modal">
<div class="wefroms-form-templates">
    <wpuf-modal :show.sync="show" :onClose="onClose">
        <h2 slot="header">
            <?php _e( 'Select a Template', 'weforms' ); ?>
            <small><?php printf( __( 'Select from a pre-defined template or from a <a href="#" %s>blank form</a>', 'weforms' ), '@click.prevent="blankForm()"' ); ?></small>

            <span class="choose-form-categroy">
                <?php _e( 'Select Category', 'weforms' ); ?> &nbsp;
                <select v-model="category">
                    <option value="all">All</option>
                    <?php
                        $registry   = weforms()->templates->get_templates();
                        $categories = weforms_get_form_template_categories();
                        $colors     = weforms_get_flat_ui_colors();

                        foreach ( $categories as $key => $category ) {
                            printf( '<option value="%s">%s</option>', $key, $category['name'] );
                        }
                     ?>
                </select>
            </span>
        </h2>

        <div slot="body">

                <?php

                // remove the blank form from index as it's handled separately
                if ( array_key_exists( 'blank', $registry ) ) {
                    unset( $registry['blank'] );
                }

                foreach ($categories as $category_id => $category) {

                    ?>
                    <div class="clearfix" v-if="category=='<?php echo $category_id; ?>' || category=='all'">

                    <?php

                    printf( '<h2><i class="%s" style="color: %s"></i> &nbsp;  %s</h2> <ul class="clearfix">',$category['icon'], $colors[array_rand($colors)], $category['name'] );

                    if ( $category_id == 'default' ) {

                        ?>

                            <li class="blank-form">
                                <h3><?php _e( 'Blank Form', 'weforms' ); ?></h3>

                                <div class="blank-form-text">
                                    <span class="dashicons dashicons-plus"></span>
                                    <div class="title"><?php _e( 'Blank Form', 'weforms' ); ?></div>
                                </div>

                                <div class="form-create-overlay">

                                    <div class="title"><?php _e( 'Blank Form', 'weforms' ); ?></div>
                                    <br>
                                    <button class="button button-primary" @click.prevent="blankForm($event.target)" title="<?php echo esc_attr('Blank Form'); ?>">
                                        <?php _e('Create Form', 'weforms' );  ?>
                                    </button>
                                </div>
                            </li>

                        <?php
                    }

                    foreach ( $registry as $key => $template ) {

                        if ( $category_id !== $template->category ) {
                            continue;
                        }

                        $is_available = true;
                        $class        = 'template-active';
                        $title        = $template->title;
                        $image        = $template->image ? $template->image : '';

                         if ( ! $template->is_enabled() ) {

                            $title        = __( 'This integration is not installed.', 'weforms' );
                            $class        = 'template-inactive';
                            $is_available = false;
                         }


                        ?>

                        <li class="<?php echo $class; ?>">
                            <h3><?php _e( $title, 'weforms' ); ?></h3>

                            <?php  if ( $image ) { printf( '<img src="%s" alt="%s">', $image, $title );   }  ?>

                            <div class="form-create-overlay">

                                <div class="title"><?php echo $template->get_title(); ?></div>
                                <div class="description"><?php echo $template->get_description(); ?></div>
                                <br>

                                <button class="button button-primary" @click.prevent="createForm('<?php echo $key; ?>', $event.target)" title="<?php echo esc_attr( $title ); ?>" <?php echo $is_available ? '' : 'disabled="disabled"'; ?>>
                                  <?php if ( $is_available ) : ?>
                                       <?php _e('Create Form', 'weforms' );  ?>
                                    <?php else : ?>
                                        <?php _e('Require Pro Upgrade', 'weforms' );  ?>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </li>
                        <?php
                    }


                    ?>

                    </ul></div>

                    <?php
                }
                ?>
        </div>
    </wpuf-modal>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-weforms-text-editor">
<div>
    <textarea :value="value" :id="'wefroms-tinymce-' + editorId" rows="5"></textarea>
</div>
</script>
