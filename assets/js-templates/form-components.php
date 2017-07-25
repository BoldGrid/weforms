<script type="text/x-template" id="tmpl-wpuf-field-name">
<div>
    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'First Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.first_name.placeholder">
            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.first_name.default">
            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix" v-if="editing_form_field.format !== 'first-last'">

        <label>
            <?php _e( 'Middle Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.middle_name.placeholder">
            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.middle_name.default">
            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>

    <div class="panel-field-opt panel-field-name clearfix">

        <label>
            <?php _e( 'Last Name', 'weforms' ); ?>
        </label>

        <div class="name-field-placeholder">
            <input type="text" v-model="editing_form_field.last_name.placeholder">
            <label><?php _e( 'Placeholder', 'weforms' ); ?></label>
        </div>

        <div class="name-field-value">
            <input type="text" v-model="editing_form_field.last_name.default">
            <label><?php _e( 'Default Value', 'weforms' ); ?></label>
        </div>
    </div>
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

<script type="text/x-template" id="tmpl-wpuf-integration-slack">
<div>
    <div class="wpuf-int-form-row">
        <div class="wpuf-int-field-label">
            <label for="mailchimp-list-id"><?php _e( 'Slack Webhook URL', 'weforms' ); ?></label>
        </div>
        <div class="wpuf-int-field">
            <input type="url" class="regular-text" v-model="settings.url" placeholder="https://hooks.slack.com/services/...">
            <p class="help"><?php _e( 'Slack webhook URL to send our JSON payloads', 'weforms' ); ?></p>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-template-modal">
<div>
    <wpuf-modal :show.sync="show" :onClose="onClose">
        <h2 slot="header">
            <?php _e( 'Select a Template', 'weforms' ); ?>
            <small><?php printf( __( 'Select from a pre-defined template or from a <a href="#" %s>blank form</a>', 'weforms' ), '@click.prevent="blankForm()"' ); ?></small>
        </h2>

        <div slot="body">
            <ul>
                <li class="blank-form">
                    <a href="#" @click.prevent="blankForm($event.target)">
                        <span class="dashicons dashicons-plus"></span>
                        <div class="title"><?php _e( 'Blank Form', 'weforms' ); ?></div>
                    </a>
                </li>

                <?php
                $registry = weforms_get_form_templates();

                foreach ($registry as $key => $template ) {
                    $class = 'template-active';
                    $title = '';

                    if ( ! $template->is_enabled() ) {
                        $class = 'template-inactive';
                        $title = __( 'This integration is not installed.', 'weforms' );
                    }
                    ?>

                    <li>
                        <a href="#" @click.prevent="createForm('<?php echo $key; ?>', $event.target)" title="<?php echo esc_attr( $title ); ?>">
                            <div class="title"><?php echo $template->get_title(); ?></div>
                            <div class="description"><?php echo $template->get_description(); ?></div>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </wpuf-modal>
</div></script>
