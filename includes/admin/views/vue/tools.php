<div class="export-import-wrap">

    <h2 class="nav-tab-wrapper">
        <a v-bind:class="['nav-tab', isActiveTab( 'export' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('export')"><?php _e( 'Export', 'wpuf' ); ?></a>
        <a v-bind:class="['nav-tab', isActiveTab( 'import' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('import')"><?php _e( 'Import', 'wpuf' ); ?></a>
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
        </div>
    </div>
</div>