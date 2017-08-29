<div class="export-import-wrap">

    <h2 class="nav-tab-wrapper">
        <a :class="['nav-tab', isActiveTab( 'export' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('export')"><?php _e( 'Export', 'wpuf' ); ?></a>
        <a :class="['nav-tab', isActiveTab( 'import' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('import')"><?php _e( 'Import', 'wpuf' ); ?></a>
    </h2>

    <div class="nav-tab-content">
        <div class="nav-tab-inside" v-show="isActiveTab('export')">
            <h3><?php _e( 'Export Utility', 'weforms' ); ?></h3>

            <p><?php _e( 'You can export your form, as well as exporting the submitted entries by the users', 'weforms' ); ?></p>

            <div class="postboxes metabox-holder two-col">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( 'Export Forms', 'weforms' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php _e( 'You can export your existing contact forms and import the same forms into a different site.', 'weforms' ); ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=weforms_export_forms" method="post">
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="all" checked> <?php _e( 'All Forms', 'weforms' ); ?></label></p>
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="selected"> <?php _e( 'Selected Forms', 'weforms' ); ?></label></p>
                                <p v-show="exportType == 'selected'">
                                    <select name="selected_forms[]" class="forms-list" multiple="multiple">
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'weforms-export-forms' ); ?>
                                <input type="submit" class="button button-primary" name="weforms_export_forms" value="<?php _e( 'Export Forms', 'weforms' ) ?>">
                            </form>
                        </template>
                        <template v-else>
                            <div class="spinner loading-spinner is-active"></div>
                        </template>
                    </div>
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3 class="hndle"><?php _e( 'Export Form Entries', 'weforms' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php _e( 'Export your form entries/submissions as a <strong>CSV</strong> file.', 'weforms' ); ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=weforms_export_form_entries" method="post">
                                <p>
                                    <select name="selected_forms" class="forms-list">
                                        <option value=""><?php _e( '&mdash; Select Form &mdash;', 'weforms' ); ?></option>
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'weforms-export-entries' ); ?>
                                <input type="submit" class="button button-primary" name="weforms_export_entries" value="<?php _e( 'Export Entries', 'weforms' ) ?>">
                            </form>
                        </template>
                        <template v-else>
                            <div class="spinner loading-spinner is-active"></div>
                        </template>
                    </div>
                </div><!-- .postbox -->
            </div>
        </div>

        <div class="nav-tab-inside" v-show="isActiveTab('import')">
            <h3><?php _e( 'Import Contact Form', 'weforms' ); ?></h3>

            <p><?php _e( 'Browse and locate a json file you backed up before.', 'weforms' ); ?></p>
            <p><?php _e( 'Press <strong>Import</strong> button, we will do the rest for you.', 'weforms' ); ?></p>

            <div class="updated-message notice notice-success is-dismissible" v-if="isSuccess">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'weforms' ); ?></span>
                </button>
            </div>

            <div class="update-message notice notice-error is-dismissible" v-if="isFailed">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'weforms' ); ?></span>
                </button>
            </div>

            <form action="" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="file" name="importFile" v-on:change="importForm( $event.target.name, $event.target.files, $event )" accept="application/json" />
                <button type="submit" :class="['button', isSaving ? 'updating-message' : '']" disabled="disabled">{{ importButton }}</button>
            </form>

            <hr>
            <h3><?php _e( 'Import Other Forms', 'weforms' ); ?></h3>
            <p><?php _e( 'You can import other WordPress form plugins into weForms.', 'weforms' ); ?></p>

            <div class="updated" v-if="ximport.title">
                <p><strong>{{ ximport.title }}</strong></p>

                <p>{{ ximport.message }}</p>
                <p>{{ ximport.action }}</p>

                <ul v-if="hasRefs">
                    <li v-for="ref in ximport.refs">
                        <a target="_blank" :href="'admin.php?page=weforms#/form/' + ref.weforms_id + '/edit'">{{ ref.title }}</a> - <a :href="'admin.php?page=weforms#/form/' + ref.weforms_id + '/edit'" target="_blank" class="button button-small"><span class="dashicons dashicons-external"></span> <?php _e( 'Edit', 'weforms' ); ?></a>
                    </li>
                </ul>

                <p>
                    <a href="#" class="button button-primary" @click.prevent="replaceX($event.target, 'replace')"><?php _e( 'Replace Shortcodes', 'weforms' ); ?></a>&nbsp;
                    <a href="#" class="button" @click.prevent="replaceX($event.target, 'skip')"><?php _e( 'No Thanks', 'weforms' ); ?></a>
                </p>
            </div>


            <table style="min-width: 500px;">
                <tbody>
                    <tr>
                        <td><?php _e( 'Contact Form 7', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'cf7')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php _e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php _e( 'Ninja Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'nf')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php _e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php _e( 'Caldera Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'caldera-forms')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php _e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php _e( 'Gravity Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'gf')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php _e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php _e( 'WP Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'wpforms')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php _e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>