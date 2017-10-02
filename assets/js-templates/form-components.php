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
            <p class="help"><?php _e( 'Slack webhook URL to send our JSON payloads', 'weforms' ); ?></p>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-merge-tags">
<div class="wpuf-merge-tag-wrap">
    <a href="#" v-on:click.prevent="toggleFields($event)" class="merge-tag-link" title="<?php echo esc_attr( 'Click to toggle merge tags', 'wpuf' ); ?>"><span class="dashicons dashicons-editor-code"></span></a>

    <!-- <pre>{{ form_fields.length }}</pre> -->

    <div class="wpuf-merge-tags">
        <div class="merge-tag-section">
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
                <li v-else><?php _e( 'No fields available', 'wpuf' ); ?></li>
            </ul>
        </div><!-- .merge-tag-section -->

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
    </div><!-- .merge-tags -->
</div></script>

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

                        $class = 'template-active';
                        $title = $template->title;
                        $image = $template->image ? $template->image : '';

                        if ( ! $template->is_enabled() ) {
                            $class = 'template-inactive';
                            $title = __( 'This integration is not installed.', 'weforms' );
                        }

                        ?>

                        <li>
                            <h3><?php _e( $title, 'weforms' ); ?></h3>

                            <?php  if ( $image ) { printf( '<img src="%s" alt="%s">', $image, $title );   }  ?>

                            <div class="form-create-overlay">

                                <div class="title"><?php echo $template->get_title(); ?></div>
                                <div class="description"><?php echo $template->get_description(); ?></div>
                                <br>
                                <button class="button button-primary" @click.prevent="createForm('<?php echo $key; ?>', $event.target)" title="<?php echo esc_attr( $title ); ?>">
                                    <?php _e('Create Form', 'weforms' );  ?>
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
