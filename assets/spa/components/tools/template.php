<div class="export-import-wrap">

    <h2 class="nav-tab-wrapper">
        <a :class="['nav-tab', isActiveTab( 'export' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('export')"><?php _e( 'Export', 'wpuf' ); ?></a>
        <a :class="['nav-tab', isActiveTab( 'import' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('import')"><?php _e( 'Import', 'wpuf' ); ?></a>
    </h2>

    <div class="nav-tab-content">
        <div class="nav-tab-inside" v-show="isActiveTab('export')">
            <h3><?php _e( 'Export Utility', 'best-contact-form' ); ?></h3>

            <p><?php _e( 'You can export your form, as well as exporting the submitted entries by the users', 'best-contact-form' ); ?></p>

            <div class="postboxes metabox-holder two-col">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( 'Export Forms', 'best-contact-form' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php _e( 'You can export your existing contact forms and import the same forms into a different site.', 'best-contact-form' ); ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=bcf_export_forms" method="post">
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="all" checked> <?php _e( 'All Forms', 'best-contact-form' ); ?></label></p>
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="selected"> <?php _e( 'Selected Forms', 'best-contact-form' ); ?></label></p>
                                <p v-show="exportType == 'selected'">
                                    <select name="selected_forms[]" class="forms-list" multiple="multiple">
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'bcf-export-forms' ); ?>
                                <input type="submit" class="button button-primary" name="bcf_export_forms" value="<?php _e( 'Export Forms', 'best-contact-form' ) ?>">
                            </form>
                        </template>
                        <template v-else>
                            <div class="spinner loading-spinner is-active"></div>
                        </template>
                    </div>
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3 class="hndle"><?php _e( 'Export Form Entries', 'best-contact-form' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php _e( 'Export your form entries/submissions as a <strong>CSV</strong> file.', 'best-contact-form' ); ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=bcf_export_form_entries" method="post">
                                <p>
                                    <select name="selected_forms" class="forms-list">
                                        <option value=""><?php _e( '&mdash; Select Form &mdash;', 'best-contact-form' ); ?></option>
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'bcf-export-entries' ); ?>
                                <input type="submit" class="button button-primary" name="bcf_export_entries" value="<?php _e( 'Export Entries', 'best-contact-form' ) ?>">
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
            <h3><?php _e( 'Import Contact Form', 'best-contact-form' ); ?></h3>

            <p><?php _e( 'Browse and locate a json file you backed up before.', 'best-contact-form' ); ?></p>
            <p><?php _e( 'Press <strong>Import</strong> button, we will do the rest for you.', 'best-contact-form' ); ?></p>

            <div class="updated-message notice notice-success is-dismissible" v-if="isSuccess">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'best-contact-form' ); ?></span>
                </button>
            </div>

            <div class="update-message notice notice-error is-dismissible" v-if="isFailed">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'best-contact-form' ); ?></span>
                </button>
            </div>

            <form action="" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="file" name="importFile" v-on:change="importForm( $event.target.name, $event.target.files, $event )" accept="application/json" />
                <button type="submit" :class="['button', isSaving ? 'updating-message' : '']" disabled="disabled">{{ importButton }}</button>
            </form>
        </div>
    </div>
</div>