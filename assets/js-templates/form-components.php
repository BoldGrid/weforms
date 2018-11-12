<script type="text/x-template" id="tmpl-wpuf-dynamic-field">
<div>

    <div class="panel-field-opt panel-field-opt-text">
        <label>
            <?php _e( 'Dynamic value population', 'weforms' ); ?>
            <help-text text="<?php _e( "Field value or options can be populated dynamically through filter hook or query string", 'weforms' ) ?>"></help-text>
        </label>

        <ul>
            <li>
                <label><input type="checkbox" value="yes" v-model="dynamic.status"> <?php _e( 'Allow field to be populated dynamically', 'weforms' ); ?></label>
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
            <div class="wpuf-integration-header">
                <div class="wpuf-integration-header-toggle">
                    <span :class="['wpuf-toggle-switch', 'big', isActive(integration.id) ? 'checked' : '']" v-on:click="toggleState(integration.id, $event.target)"></span>
                </div>
                <div class="wpuf-integration-header-label">
                    <img class="icon" :src="integration.icon" :alt="integration.title">
                    {{ integration.title }} <span class="label-premium" v-if="!isAvailable(integration.id)"><?php _e( 'Premium Feature', 'best-contact-form' ); ?></span>
                </div>

                <div class="wpuf-integration-header-actions">
                    <button type="button" class="toggle-area" v-on:click="showHide($event.target)">
                        <span class="screen-reader-text"><?php _e( 'Toggle panel', 'best-contact-form' ); ?></span>
                        <span class="toggle-indicator"></span>
                    </button>
                </div>
            </div>

            <div class="wpuf-integration-settings">

                <div v-if="isAvailable(integration.id)">
                    <component :is="'wpuf-integration-' + integration.id" :id="integration.id"></component>
                </div>
                <div v-else>
                    <?php _e( 'This feature is available on the premium version only.', 'best-contact-form' ); ?>
                    <a class="button" :href="pro_link" target="_blank"><?php _e( 'Upgrade to Pro', 'best-contact-form' ); ?></a>
                </div>

            </div>
        </div>
    </template>

    <div v-else>
        <?php _e( 'No integration found.', 'best-contact-form' ); ?>
    </div>
</div></script>

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
