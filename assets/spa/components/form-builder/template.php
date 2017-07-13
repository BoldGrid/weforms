<form id="wpuf-form-builder" class="wpuf-form-builder-contact_form" method="post" action="" @submit.prevent="save_form_builder" v-cloak>
    <fieldset :class="[is_form_saving ? 'disabled' : '']" :disabled="is_form_saving">
        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-form-builder-container" class="nav-tab nav-tab-active">
                <?php _e( 'Form Editor', 'wpuf' ); ?>
            </a>

            <a href="#wpuf-form-builder-settings" class="nav-tab">
                <?php _e( 'Settings', 'wpuf' ); ?>
            </a>

            <?php do_action( "wpuf-form-builder-tabs-contact_form" ); ?>

            <span class="pull-right">
                <button v-if="!is_form_saving" type="button" class="button button-primary" @click="save_form_builder">
                    <?php _e( 'Save Form', 'wpuf' ); ?>
                </button>

                <button v-else type="button" class="button button-primary button-ajax-working" disabled>
                    <span class="loader"></span> <?php _e( 'Saving Form Data', 'wpuf' ); ?>
                </button>
            </span>
        </h2>

        <div class="tab-contents">
            <div id="wpuf-form-builder-container" class="group active">
                <div id="builder-stage">
                    <header class="clearfix">
                        <span v-if="!post_title_editing" class="form-title" @click.prevent="post_title_editing = true">{{ post.post_title }}</span>

                        <span v-show="post_title_editing">
                            <input type="text" v-model="post.post_title" name="post_title" />
                            <button type="button" class="button button-small" style="margin-top: 13px;" @click.prevent="post_title_editing = false"><i class="fa fa-check"></i></button>
                        </span>

                        <i :class="(is_form_switcher ? 'fa fa-angle-up' : 'fa fa-angle-down') + ' form-switcher-arrow'" @click.prevent="switch_form"></i>
                        <?php
                            $form_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

                            printf( "<span class=\"form-id\" title=\"%s\" data-clipboard-text='%s'><i class=\"fa fa-clipboard\" aria-hidden=\"true\"></i> #{{ post.ID }}</span>", __( 'Click to copy shortcode', 'wpuf' ), '[wpuf_contact_form id="' . $form_id . '"]' );

                        ?>
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
                                    <?php _e( 'Add Fields', 'wpuf' ); ?>
                                </a>
                            </li>

                            <li :class="['field-options' === current_panel ? 'active' : '', !form_fields_count ? 'disabled' : '']">
                                <a href="#field-options" @click.prevent="set_current_panel('field-options')">
                                    <?php _e( 'Field Options', 'wpuf' ); ?>
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

            <div id="wpuf-form-builder-settings" class="group clearfix">
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

            <input type="hidden" name="form_settings_key" value="wpuf_form_settings">

        <?php wp_nonce_field( 'wpuf_form_builder_save_form', 'wpuf_form_builder_nonce' ); ?>

        <input type="hidden" name="wpuf_form_id" value="<?php echo $form_id; ?>">
    </fieldset>
</form><!-- #wpuf-form-builder -->
