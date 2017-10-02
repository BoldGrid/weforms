<form id="wpuf-form-builder" class="wpuf-form-builder-contact_form" method="post" action="" @submit.prevent="save_form_builder" v-cloak>
    <fieldset :class="[is_form_saving ? 'disabled' : '']" :disabled="is_form_saving">

        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-form-builder-container" :class="['nav-tab', isActiveTab( 'editor' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActive('editor')">
                <?php _e( 'Form Editor', 'weforms' ); ?>
            </a>

            <a href="#wpuf-form-builder-settings" :class="['nav-tab', isActiveTab( 'settings' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActive('settings')">
                <?php _e( 'Settings', 'weforms' ); ?>
            </a>

            <?php do_action( "wpuf-form-builder-tabs-contact_form" ); ?>

            <span class="pull-right">
                <a :href="'<?php echo site_url( '/' ); ?>?weforms_preview=1&form_id=' + post.ID" target="_blank" class="button"><span class="dashicons dashicons-visibility" style="padding-top: 3px;"></span> <?php _e( 'Preview', 'weforms' ); ?></a>

                <button v-if="!is_form_saving" type="button" class="button button-primary" @click="save_form_builder">
                    <?php _e( 'Save Form', 'weforms' ); ?>
                </button>

                <button v-else type="button" class="button button-primary button-ajax-working" disabled>
                    <span class="loader"></span> <?php _e( 'Saving Form Data', 'weforms' ); ?>
                </button>
            </span>
        </h2>

        <div class="tab-contents" v-if="!loading">
            <div id="wpuf-form-builder-container" v-show="isActiveTab('editor')">
                <div id="builder-stage">
                    <header class="clearfix">
                        <span v-if="!post_title_editing" title="<?php esc_attr_e( 'Click to edit the form name', 'weforms' ); ?>" class="form-title" @click.prevent="post_title_editing = true"><span class="dashicons dashicons-edit"></span> {{ post.post_title }}</span>

                        <span v-show="post_title_editing">
                            <input type="text" v-model="post.post_title" name="post_title" />
                            <button type="button" class="button button-small" style="margin-top: 8px;" @click.prevent="post_title_editing = false"><i class="fa fa-check"></i></button>
                        </span>

                        <span class="form-id" title="<?php echo esc_attr_e( 'Click to copy shortcode', 'weforms' ); ?>" :data-clipboard-text='"[weforms id=\"" + post.ID + "\"]"'><i class="fa fa-clipboard" aria-hidden="true"></i> #{{ post.ID }}</span>

                        <span :class="{ sharing_on : settings.sharing_on }" class="ann-form-btn form-id" @click="shareForm( '<?php echo site_url( '/' ); ?>',post)" title="<?php echo esc_attr_e( 'Share Your Form', 'weforms' ); ?>"> 
                            <i class="fa fa-share-alt" aria-hidden="true"></i> 
                            <?php _e('Share', 'Share' ); ?>
                        </span>

                    </header>

                    <ul v-if="is_form_switcher" class="form-switcher-content">

                    </ul>

                    <section>
                        <div id="form-preview">
                            <builder-stage></builder-stage>
                        </div>
                    </section>
                </div><!-- #builder-stage -->

                <div id="builder-form-fields">
                    <header>
                        <ul class="clearfix">
                            <li :class="['form-fields' === current_panel ? 'active' : '']">
                                <a href="#add-fields" @click.prevent="set_current_panel('form-fields')">
                                    <?php _e( 'Add Fields', 'weforms' ); ?>
                                </a>
                            </li>

                            <li :class="['field-options' === current_panel ? 'active' : '', !form_fields_count ? 'disabled' : '']">
                                <a href="#field-options" @click.prevent="set_current_panel('field-options')">
                                    <?php _e( 'Field Options', 'weforms' ); ?>
                                </a>
                            </li>
                        </ul>
                    </header>

                    <section>
                        <div class="wpuf-form-builder-panel">
                            <component :is="current_panel"></component>
                        </div>
                    </section>
                </div><!-- #builder-form-fields -->
            </div><!-- #wpuf-form-builder-container -->

            <div id="wpuf-form-builder-settings" class="clearfix" v-show="isActiveTab('settings')">
                <fieldset>
                    <h2 id="wpuf-form-builder-settings-tabs" class="nav-tab-wrapper">
                        <?php do_action( "wpuf-form-builder-settings-tabs-contact_form" ); ?>
                    </h2><!-- #wpuf-form-builder-settings-tabs -->

                    <div id="wpuf-form-builder-settings-contents" class="tab-contents">
                        <?php do_action( "wpuf-form-builder-settings-tab-contents-contact_form" ); ?>
                    </div><!-- #wpuf-form-builder-settings-contents -->
                </fieldset>
            </div><!-- #wpuf-form-builder-settings -->

            <?php do_action( "wpuf-form-builder-tab-contents-contact_form" ); ?>
        </div>
        <div v-else>
            <div class="updating-message">
                <p><?php _e( 'Loading the editor', 'weforms' ); ?></p>
            </div>
        </div>

        <input type="hidden" name="form_settings_key" value="wpuf_form_settings">

        <?php wp_nonce_field( 'wpuf_form_builder_save_form', 'wpuf_form_builder_nonce' ); ?>

        <input type="hidden" name="wpuf_form_id" :value="post.ID">
    </fieldset>
</form><!-- #wpuf-form-builder -->
