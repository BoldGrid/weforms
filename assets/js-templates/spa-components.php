<script type="text/x-template" id="tmpl-wpuf-addons">
<div class="wrap bcf-extension-list">
    <h2><?php _e( 'Best Contact Form - Add-Ons', 'wpuf' ); ?></h2>

    <?php
    $add_ons = get_transient( 'wpuf_addons' );

    if ( false === $add_ons ) {
        $response = wp_remote_get( 'https://wedevs.com/api/wpuf/addons.php', array('timeout' => 15) );
        $update   = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            return false;
        }

        set_transient( 'wpuf_addons', $update, 12 * HOUR_IN_SECONDS );
        $add_ons = $update;
    }

    $add_ons = json_decode( $add_ons );

    if ( count( $add_ons ) ) {
        ?>
        <div class="wp-list-table widefat plugin-install">

        <?php foreach ($add_ons as $addon) { ?>

            <div class="plugin-card">
                <div class="plugin-card-top">

                    <div class="name column-name">
                        <h3>
                            <a href="<?php echo $addon->url; ?>" target="_blank">
                                <?php echo $addon->title; ?>
                                <img class="plugin-icon" src="<?php echo $addon->thumbnail; ?>" alt="<?php echo esc_attr( $addon->title ); ?>" />
                            </a>
                        </h3>
                    </div>

                    <div class="action-links">
                        <ul class="plugin-action-buttons">
                            <li>
                                <?php if ( class_exists( $addon->class ) ) { ?>
                                    <a class="button button-disabled" href="<?php echo $addon->url; ?>" target="_blank">Installed</a>
                                <?php } else { ?>
                                    <a class="button" href="<?php echo $addon->url; ?>" target="_blank">View Details</a>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>

                    <div class="desc column-description">
                        <p>
                            <?php echo $addon->desc; ?>
                        </p>

                        <p class="authors">
                            <cite>By <a href="https://wedevs.com" target="_blank">weDevs</a></cite>
                        </p>
                    </div>
                </div>

                <div class="plugin-card-bottom">
                    <div class="column-updated">
                        <strong>Last Updated:</strong> 2 months ago
                    </div>

                    <div class="column-compatibility">
                        <span class="compatibility-compatible">
                            <strong>Compatible</strong> with your version of WordPress
                        </span>
                    </div>
                </div>
            </div>

        <?php } ?>

        </div>

        <?php
    } else {
        echo '<div class="error"><p>Error fetching add-ons. Please refresh the page again.</p></div>';
    }
    ?>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-component-table">
<div>

    <div class="tablenav top">

        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'best-contact-form' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'best-contact-form' ); ?></option>
                <option value="delete"><?php _e( 'Delete Entries', 'best-contact-form' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'best-contact-form' ); ?></button>
        </div>

        <div class="alignleft actions">
            <a class="button" :href="'admin-post.php?action=bcf_export_form_entries&selected_forms=' + id + '&_wpnonce=' + nonce" style="margin-top: 0;"><span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( 'Export Entries', 'best-contact-form' ); ?></a>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'best-contact-form' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'best-contact-form' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'best-contact-form' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'best-contact-form' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'best-contact-form' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'best-contact-form' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'best-contact-form' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped wpuf-contact-form">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th class="col-entry-id"><?php _e( 'ID', 'best-contact-form' ); ?></th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th class="col-entry-id"><?php _e( 'ID', 'best-contact-form' ); ?></th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th class="col-entry-details"><?php _e( 'Actions', 'best-contact-form' ); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <tr v-if="loading">
                <td v-bind:colspan="columnLength + 3"><?php _e( 'Loading...', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td v-bind:colspan="columnLength + 3"><?php _e( 'No entries found!', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-for="(entry, index) in items">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" v-model="checkedItems" :value="entry.id">
                </th>
                <th class="col-entry-id">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">#{{ entry.id }}</router-link>
                </th>
                <td v-for="(header, index) in columns">{{ entry.fields[index] }}</td>
                <th class="col-entry-details">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}"><?php _e( 'Details', 'best-contact-form' ); ?></router-link>
                </th>
            </tr>
        </tbody>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'best-contact-form' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'best-contact-form' ); ?></option>
                <option value="delete"><?php _e( 'Delete Entries', 'best-contact-form' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'best-contact-form' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'best-contact-form' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'best-contact-form' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'best-contact-form' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'best-contact-form' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> of <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'best-contact-form' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'best-contact-form' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-builder">
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
</script>

<script type="text/x-template" id="tmpl-wpuf-form-entries">
<div class="wpuf-contact-form-entries">
    <h1 class="wp-heading-inline">
        <?php _e( 'Entries', 'best-contact-form' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
        <span style="color: #999;" class="form-name">{{ form_title }}</span>
    </h1>

    <router-link class="page-title-action" to="/"><?php _e( 'Back to forms', 'best-contact-form' ); ?></router-link>

    <wpuf-table action="bcf_contact_form_entries" :id="id" v-on:ajaxsuccess="form_title = $event.form_title"></wpuf-table>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-entry-single">
<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php _e( 'Entry Details', 'best-contact-form' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php _e( 'Back to Entries', 'best-contact-form' ); ?></router-link>

    <div v-if="loading"><?php _e( 'Loading...', 'best-contact-form' ); ?></div>
    <div v-else class="wpuf-contact-form-entry-wrap">

        <div class="wpuf-contact-form-entry-left">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>{{ entry.info.form_title }} : Entry # {{ $route.params.entryid }}</span></h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(field, index) in entry.form_fields">
                                <tr class="field-label">
                                    <th><strong>{{ field.label }}</strong></th>
                                </tr>
                                <tr class="field-value">
                                    <td>
                                        <div v-html="entry.meta_data[index]"></div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside"><?php _e( 'Loading...', 'best-contact-form' ); ?></div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Submission Info', 'best-contact-form' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php _e( 'Entry ID', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ $route.params.entryid }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'User IP', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.ip }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Page', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.info.referer">{{ entry.info.referer }}</a></span>
                            </li>
                            <li v-if="entry.info.user">
                                <span class="label"><?php _e( 'From', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Submitted On', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.created }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <button class="button button-large button-secondary" v-on:click.prevent="trashEntry"><span class="dashicons dashicons-trash"></span><?php _e( ' Trash', 'best-contact-form' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-list-table">
<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <!-- <pre>{{ $data }}</pre> -->

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'best-contact-form' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'best-contact-form' ); ?></option>
                <option value="delete"><?php _e( 'Delete Forms', 'best-contact-form' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'best-contact-form' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'best-contact-form' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'best-contact-form' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'best-contact-form' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'best-contact-form' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'best-contact-form' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'best-contact-form' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'best-contact-form' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'best-contact-form' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'best-contact-form' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6"><?php _e( 'Loading...', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td colspan="6"><?php _e( 'No form found!', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-for="(form, index) in items">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" v-model="checkedItems" :value="form.ID">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><a :href="'<?php echo admin_url( 'admin.php?page=best-contact-forms&action=edit&id=') ?>' + form.ID">{{ form.post_title }}</a> <span v-if="form.post_status != 'publish'">({{ form.post_status }})</span></strong>

                    <div class="row-actions">
                        <span class="edit"><a :href="'<?php echo admin_url( 'admin.php?page=best-contact-forms&action=edit&id=') ?>' + form.ID"><?php _e( 'Edit', 'best-contact-form' ); ?></a> | </span>
                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete"><?php _e( 'Delete', 'best-contact-form' ); ?></a> | </span>
                        <span class="duplicate"><a href="#" v-on:click.prevent="duplicate(form.ID, index)"><?php _e( 'Duplicate', 'best-contact-form' ); ?></a> <template v-if="form.entries">|</template> </span>
                        <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}"><?php _e( 'View Entries', 'best-contact-form' ); ?></router-link> |
                        <router-link :to="{ name: 'edit', params: { id: form.ID }}"><?php _e( 'EDIT', 'best-contact-form' ); ?></router-link>
                    </div>
                </td>
                <td><code>[wpuf_contact_form id="{{ form.ID }}"]</code></td>
                <td>
                    <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}">{{ form.entries }}</router-link>
                    <span v-else>&mdash;</span>
                </td>
                <td>{{ form.views }}</td>
                <td>
                    <span v-if="form.views">{{ ((form.entries/form.views) * 100).toFixed(2) }}%</span>
                    <span v-else>0%</span>
                </td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'best-contact-form' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'best-contact-form' ); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'best-contact-form' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'best-contact-form' ); ?></option>
                <option value="delete"><?php _e( 'Delete Forms', 'best-contact-form' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'best-contact-form' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'best-contact-form' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'best-contact-form' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'best-contact-form' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'best-contact-form' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'best-contact-form' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'best-contact-form' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'best-contact-form' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-notification">
<div>
    <!-- <pre>{{ notifications.length }}</pre> -->
    <a href="#" class="button button-secondary add-notification" v-on:click.prevent="addNew"><span class="dashicons dashicons-plus-alt"></span> <?php _e( 'Add Notification', 'best-contact-form' ); ?></a>

    <div :class="[editing ? 'editing' : '', 'notification-wrap']">
    <!-- notification-wrap -->

        <div class="notification-table-wrap">
            <table class="wp-list-table widefat fixed striped posts wpuf-cf-notification-table">
                <thead>
                    <tr>
                        <th class="col-toggle">&nbsp;</th>
                        <th class="col-name"><?php _e( 'Name', 'best-contact-form' ); ?></th>
                        <th class="col-subject"><?php _e( 'Subject', 'best-contact-form' ); ?></th>
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
                            <a href="#" v-on:click.prevent="duplicate(index)" title="<?php esc_attr_e( 'Duplicate', 'best-contact-form' ); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                            <a href="#" v-on:click.prevent="editItem(index)" title="<?php esc_attr_e( 'Settings', 'best-contact-form' ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
                        </td>
                    </tr>
                    <tr v-if="!notifications.length">
                        <td colspan="4"><?php _e( 'No notifications found', 'best-contact-form' ); ?></td>
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
                        <label for="notification-title"><?php _e( 'To', 'best-contact-form' ); ?></label>
                        <input type="text" v-model="notifications[editingIndex].to">
                        <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="to"></wpuf-merge-tags>
                    </div>

                    <div class="row-one-half notification-field">
                        <label for="notification-title"><?php _e( 'Reply To', 'best-contact-form' ); ?></label>
                        <input type="email" v-model="notifications[editingIndex].replyTo">
                        <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="replyTo"></wpuf-merge-tags>
                    </div>
                </div>

                <div class="notification-row notification-field">
                    <label for="notification-title"><?php _e( 'Subject', 'best-contact-form' ); ?></label>
                    <input type="text" v-model="notifications[editingIndex].subject">
                    <wpuf-merge-tags v-on:insert="insertValue" field="subject"></wpuf-merge-tags>
                </div>

                <div class="notification-row notification-field">
                    <label for="notification-title"><?php _e( 'Email Message', 'best-contact-form' ); ?></label>
                    <textarea name="" rows="6" v-model="notifications[editingIndex].message"></textarea>
                    <wpuf-merge-tags v-on:insert="insertValue" field="message"></wpuf-merge-tags>
                </div>

                <section class="advanced-fields">
                    <a href="#" class="field-toggle" v-on:click.prevent="toggleAdvanced()"><span class="dashicons dashicons-arrow-right"></span><?php _e( ' Advanced', 'best-contact-form' ); ?></a>

                    <div class="advanced-field-wrap">
                        <div class="notification-row">
                            <div class="row-one-half notification-field first">
                                <label for="notification-title"><?php _e( 'From Name', 'best-contact-form' ); ?></label>
                                <input type="text" v-model="notifications[editingIndex].fromName">
                                <wpuf-merge-tags v-on:insert="insertValue" field="fromName"></wpuf-merge-tags>
                            </div>

                            <div class="row-one-half notification-field">
                                <label for="notification-title"><?php _e( 'From Address', 'best-contact-form' ); ?></label>
                                <input type="email" name="" v-model="notifications[editingIndex].fromAddress">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="fromAddress"></wpuf-merge-tags>
                            </div>
                        </div>

                        <div class="notification-row">
                            <div class="row-one-half notification-field first">
                                <label for="notification-title"><?php _e( 'CC', 'best-contact-form' ); ?></label>
                                <input type="email" name="" v-model="notifications[editingIndex].cc">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="cc"></wpuf-merge-tags>
                            </div>

                            <div class="row-one-half notification-field">
                                <label for="notification-title"><?php _e( 'BCC', 'best-contact-form' ); ?></label>
                                <input type="email" name="" v-model="notifications[editingIndex].bcc">
                                <wpuf-merge-tags filter="email_address" v-on:insert="insertValue" field="bcc"></wpuf-merge-tags>
                            </div>
                        </div>
                    </div>
                </section><!-- .advanced-fields -->
            </div>

            <div class="submit-area">
                <a href="#" v-on:click.prevent="deleteItem(editingIndex)" title="<?php esc_attr_e( 'Delete', 'best-contact-form' ); ?>"><span class="dashicons dashicons-trash"></span></a>
                <button class="button button-secondary" v-on:click.prevent="editDone()"><?php _e( 'Done', 'best-contact-form' ); ?></button>
            </div>
        </div><!-- .notification-edit-area -->

    </div><!-- .notification-wrap -->
</div></script>

<script type="text/x-template" id="tmpl-wpuf-home-page">
<div class="contact-form-list">
    <h1 class="wp-heading-inline"><?php _e( 'Contact Forms', 'best-contact-form' ); ?></h1>
    <a class="page-title-action add-form" herf="#"><?php _e( 'Add Form', 'best-contact-form' ); ?></a>

    <form-list-table></form-list-table>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-tools">
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
</div></script>
