<script type="text/x-template" id="tmpl-wpuf-component-table">
<div>

    <div class="tablenav top">

        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php _e( 'Delete Entries', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="alignleft actions">
            <a class="button" :href="'admin-post.php?action=weforms_export_form_entries&selected_forms=' + id + '&_wpnonce=' + nonce" style="margin-top: 0;"><span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php _e( 'Export Entries', 'weforms' ); ?></a>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped wpuf-contact-form">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th class="col-entry-id"><?php _e( 'ID', 'weforms' ); ?></th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th class="col-entry-id"><?php _e( 'ID', 'weforms' ); ?></th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th class="col-entry-details"><?php _e( 'Actions', 'weforms' ); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <tr v-if="loading">
                <td v-bind:colspan="columnLength + 3"><?php _e( 'Loading...', 'weforms' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td v-bind:colspan="columnLength + 3"><?php _e( 'No entries found!', 'weforms' ); ?></td>
            </tr>
            <tr v-for="(entry, index) in items">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" v-model="checkedItems" :value="entry.id">
                </th>
                <th class="col-entry-id">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">#{{ entry.id }}</router-link>
                </th>
                <td v-for="(header, index) in columns"><span v-html="entry.fields[index]"></span></td>
                <th class="col-entry-details">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}"><?php _e( 'Details', 'weforms' ); ?></router-link>
                </th>
            </tr>
        </tbody>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php _e( 'Delete Entries', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> of <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-builder">
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
</script>

<script type="text/x-template" id="tmpl-wpuf-form-entries">
<div class="wpuf-contact-form-entries">
    <h1 class="wp-heading-inline">
        <?php _e( 'Entries', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
        <span style="color: #999;" class="form-name">{{ form_title }}</span>
    </h1>

    <router-link class="page-title-action" to="/"><?php _e( 'Back to forms', 'weforms' ); ?></router-link>

    <wpuf-table action="weforms_form_entries" :id="id" v-on:ajaxsuccess="form_title = $event.form_title"></wpuf-table>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-entry-single">
<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php _e( 'Entry Details', 'weforms' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php _e( 'Back to Entries', 'weforms' ); ?></router-link>

    <div v-if="loading"><?php _e( 'Loading...', 'weforms' ); ?></div>
    <div v-else class="wpuf-contact-form-entry-wrap">

        <div class="wpuf-contact-form-entry-left">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>{{ entry.meta_data.form_title }} : Entry # {{ $route.params.entryid }}</span></h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(field, index) in entry.form_fields">
                                <tr class="field-label">
                                    <th><strong>{{ field.label }}</strong></th>
                                </tr>
                                <tr class="field-value">
                                    <td>
                                        <weforms-entry-gmap :lat="entry.meta_data[index]['lat']" :long="entry.meta_data[index]['long']" v-if="field.type == 'map'"></weforms-entry-gmap>
                                        <div v-else-if="field.type === 'checkbox_field' || field.type === 'multiple_select'">
                                            <ul style="margin: 0;">
                                                <li v-for="item in field.value">- {{ item }}</li>
                                            </ul>
                                        </div>
                                        <div v-else v-html="field.value"></div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside"><?php _e( 'Loading...', 'weforms' ); ?></div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Submission Info', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php _e( 'Entry ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ $route.params.entryid }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'User IP', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.ip_address }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Page', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.meta_data.referer">{{ entry.meta_data.referer }}</a></span>
                            </li>
                            <li v-if="entry.meta_data.user">
                                <span class="label"><?php _e( 'From', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Submitted On', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.created }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <button class="button button-large button-secondary" v-on:click.prevent="trashEntry"><span class="dashicons dashicons-trash"></span><?php _e( ' Delete', 'weforms' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-form-list-table">
<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php _e( 'Delete Forms', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'weforms' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'weforms' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'weforms' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'weforms' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'weforms' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'weforms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6"><?php _e( 'Loading...', 'weforms' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td colspan="6"><?php _e( 'No form found!', 'weforms' ); ?></td>
            </tr>
            <tr v-for="(form, index) in items">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" v-model="checkedItems" :value="form.id">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><router-link :to="{ name: 'edit', params: { id: form.id }}">{{ form.name }}</router-link> <span v-if="form.data.post_status != 'publish'">({{ form.data.post_status }})</span></strong>

                    <div class="row-actions">
                        <span class="edit"><router-link :to="{ name: 'edit', params: { id: form.id }}"><?php _e( 'Edit', 'weforms' ); ?></router-link> | </span>
                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete"><?php _e( 'Delete', 'weforms' ); ?></a> | </span>
                        <span class="duplicate"><a href="#" v-on:click.prevent="duplicate(form.id, index)"><?php _e( 'Duplicate', 'weforms' ); ?></a> <template v-if="form.entries">|</template> </span>
                        <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.id }}"><?php _e( 'View Entries', 'weforms' ); ?></router-link>
                    </div>
                </td>
                <td><code>[weforms id="{{ form.id }}"]</code></td>
                <td>
                    <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.id }}">{{ form.entries }}</router-link>
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
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'weforms' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'weforms' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'weforms' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'weforms' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'weforms' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'weforms' ); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php _e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php _e( 'Delete Forms', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php _e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php _e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php _e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php _e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php _e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php _e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php _e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php _e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-home-page">
<div class="contact-form-list">
    <h1 class="wp-heading-inline"><?php _e( 'All Forms', 'weforms' ); ?></h1>
    <a class="page-title-action add-form" herf="#" v-on:click.prevent="displayModal()"><?php _e( 'Add Form', 'weforms' ); ?></a>

    <wpuf-template-modal :show.sync="showTemplateModal" :onClose="closeModal"></wpuf-template-modal>

    <form-list-table></form-list-table>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-tools">
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
</div></script>

<script type="text/x-template" id="tmpl-wpuf-weforms-page-help">
<div class="weforms-help-page">

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/docs.svg" alt="<?php esc_attr_e( 'Looking for Something?', 'weforms' ); ?>">

        <h3><?php _e( 'Looking for Something?', 'weforms' ); ?></h3>

        <p><?php _e( 'We have detailed documentation on every aspects of weForms.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://docs.wedevs.com/docs/weforms/"><?php _e( 'Visit the Plugin Documentation', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/support.svg" alt="<?php esc_attr_e( 'Need Any Assistance?', 'weforms' ); ?>">

        <h3><?php _e( 'Need Any Assistance?', 'weforms' ); ?></h3>

        <p><?php _e( 'Our EXPERT Support Team is always ready to Help you out.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://wedevs.com/account/tickets/"><?php _e( 'Contact Support', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/bugs.svg" alt="<?php esc_attr_e( 'Found Any Bugs?', 'weforms' ); ?>">

        <h3><?php _e( 'Found Any Bugs?', 'weforms' ); ?></h3>

        <p><?php _e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://github.com/weDevsOfficial/weforms"><?php _e( 'Report to GitHub', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/customization.svg" alt="<?php esc_attr_e( 'Require Customization?', 'weforms' ); ?>">

        <h3><?php _e( 'Require Customization?', 'weforms' ); ?></h3>

        <p><?php _e( 'We would Love to hear your Integration and Customization Ideas.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://wedevs.com/contact/"><?php _e( 'Contact Our Services', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/like.svg" alt="<?php esc_attr_e( 'Like The Plugin?', 'weforms' ); ?>">

        <h3><?php _e( 'Like The Plugin?', 'weforms' ); ?></h3>

        <p><?php _e( 'Your Review is very important to us as it helps us to grow more.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/weforms/reviews/?rate=5#new-post"><?php _e( 'Review Us on WP.org', 'weforms' ); ?></a>
    </div>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-weforms-premium">
<div class="wrap weforms-premium about-wrap">
    <h1><?php _e( 'weForms Pro', 'weforms' ); ?></h1>

    <p class="about-text"><?php _e( 'Upgrade to the premium versions of weForms and unlock even more useful features.', 'weforms' ); ?></p>
    <div class="wp-badge">weForms Pro</div>

    <hr>
    <div class="feature-section one-col">
        <div class="col">
            <h2><?php _e( 'More Features', 'weforms' ); ?></h2>
            <p style="text-align: center;">weForms Pro is designed just for you, specially to fulfil your business needs. We have designed and curated every package keeping your requirements in mind.</p>
        </div>
    </div>

    <div class="feature-section two-col">
        <div class="col">
            <h3>Advanced Fields</h3>
            <p>Build any kind of form flexibly with the advanced field option. Its user friendly interface makes sure you do not have to scratch your head over building forms.</p>
        </div>

        <div class="col">
            <h3>Conditional Logic</h3>
            <p>Configure your form’s settings and user flow based on conditional selection. Your forms should appear just the way you want it.</p>
        </div>

        <div class="col">
            <h3>Multi-step Form</h3>
            <p>Break down the long forms into small and attractive multi step forms. Long and lengthy forms are uninviting, why build one?</p>
        </div>

        <div class="col">
            <h3>File Uploaders</h3>
            <p>Let the user upload any kind of file by filling up your contact form. The process is unbelievably smooth and supports a wide range of file formats.</p>
        </div>

        <div class="col">
            <h3>Form Submission Notification</h3>
            <p>Receive email notification every time your form is submitted. You can now configure the notification settings just as you like it.</p>
        </div>

        <div class="col">
            <h3>Manage Submission</h3>
            <p>View, edit and manage all the submission data stored through your form. We believe that you should own it all- like literally!</p>
        </div>
    </div>

    <hr>

    <h2><?php _e( 'More Integrations', 'weforms' ); ?></h2>

    <div class="headline-feature four-col">
        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/mailchimp.svg" alt="MailChimp Integration">
            <h3>MailChimp</h3>
            <p>Integrate your desired form to your MailChimp email newsletter using latest API.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/campaign-monitor.svg" alt="">
            <h3>Campaign Monitor</h3>
            <p>Lets you add submission form in your Campaign Monitor email campaigns too.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/constant-contact.svg" alt="">
            <h3>Constant Contact</h3>
            <p>Integrate your contact forms seamlessly with your Constant Contact account.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/mailpoet.svg" alt="">
            <h3>MailPoet</h3>
            <p>Why only MailChimp? Do the same for MailPoet email campaigns as well!</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/aweber.svg" alt="">
            <h3>AWeber</h3>
            <p>Use highly customizable forms and create subscriber’s list for AWber email solution.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/getresponse.svg" alt="">
            <h3>Get Response</h3>
            <p>Enjoy seamless integration of weForms with your Get Response account.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/convertkit.svg" alt="">
            <h3>ConvertKit</h3>
            <p>Subscribe a contact to ConvertKit when a form is submited.</p>
        </div>

        <div class="col">
            <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/integrations/more.svg" alt="">
            <h3>More...</h3>
            <p>A bunch of more integrations are coming soon.</p>
        </div>
    </div>

    <div style="display: block; height: 100px; overflow: hidden;"></div>

    <div class="weforms-upgrade-sticky-footer">
        <p><a class="button button-primary" href="<?php echo WeForms_Form_Builder_Assets::get_pro_url(); ?>" target="_blank"><?php _e( 'Upgrade Now', 'weforms' ); ?></a></p>
    </div>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-weforms-settings">
<div class="weforms-settings">
    <h1><?php _e( 'Settings', 'weforms' ); ?></h1>

    <div class="postboxes metabox-holder two-col">
        <div class="postbox">
            <h3 class="hndle"><?php _e( 'General Settings', 'weforms' ); ?></h3>

            <div class="inside">
                <p class="help">
                    <?php _e( 'For better email deliverability choose a email provider that will ensure the email reaches your inbox, as well as reducing your server load.', 'weforms' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Send Email Via', 'weforms' ); ?></th>
                        <td>
                            <select v-model="settings.email_gateway">
                                <option value="wordpress"><?php _e( 'WordPress', 'weforms' ); ?></option>
                                <option value="sendgrid" :disabled="is_pro ? false : true"><?php _e( 'SendGrid', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                                <option value="mailgun" :disabled="is_pro ? false : true"><?php _e( 'Mailgun', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                                <option value="sparkpost" :disabled="is_pro ? false : true"><?php _e( 'SparkPost', 'weforms' ); ?> <span v-if="!is_pro">(<?php _e( 'Premium', 'weforms' ); ?>)</span></option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'sendgrid'">
                        <th><?php _e( 'SendGrid API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.sendgrid" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your SendGrid <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.sendgrid.com/settings/api_keys' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'mailgun'">
                        <th><?php _e( 'Domain Name', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.mailgun_domain" class="regular-text">

                            <p class="description"><?php _e( 'Your Mailgun domain name', 'weforms' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'mailgun'">
                        <th><?php _e( 'API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.mailgun" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your Mailgun <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.mailgun.com/app/account/security' ); ?></p>
                        </td>
                    </tr>
                    <tr v-if="settings.email_gateway == 'sparkpost'">
                        <th><?php _e( 'SparkPost API Key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.gateways.sparkpost" class="regular-text">

                            <p class="description"><?php printf( __( 'Fill your SparkPost <a href="%s" target="_blank">API Key</a>.', 'weforms' ), 'https://app.sparkpost.com/account/credentials' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Show Credit', 'weforms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" v-model="settings.credit">
                                <?php _e( 'Show <em>powered by weForms</em> credit in form footer.', 'weforms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Form Permission', 'weforms' ); ?></th>
                        <td>
                            <select :disabled="!is_pro" v-model="settings.permission">
                                <option value="manage_options"><?php _e( 'Admins Only', 'weforms' ); ?></option>
                                <option value="edit_others_posts"><?php _e( 'Admins, Editors', 'weforms' ); ?></option>
                                <option value="publish_posts"><?php _e( 'Admins, Editors, Authors', 'weforms' ); ?></option>
                                <option value="edit_posts"><?php _e( 'Admins, Editors, Authors, Contributors', 'weforms' ); ?></option>
                            </select>

                            <p v-if="!is_pro" class="description"><?php _e( 'Available in PRO version.', 'weforms' ); ?></p>
                            <p v-else class="description"><?php _e( 'Which user roles can access and create forms, manage form submissions.', 'weforms' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="submit-wrapper">
                <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php _e( 'Save Changes', 'weforms' ); ?></button>
            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><?php _e( 'reCaptcha', 'weforms' ); ?></h3>

            <div class="inside">
                <p class="help">
                    <?php printf( __( '<a href="%s" target="_blank">reCAPTCHA</a> is a free anti-spam service from Google which helps to protect your website from spam and abuse. Get <a href="%s" target="_blank">your API Keys</a>.', 'weforms' ), 'https://www.google.com/recaptcha/intro/', 'https://www.google.com/recaptcha/admin#list' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Site key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.recaptcha.key" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Secret key', 'weforms' ); ?></th>
                        <td>
                            <input type="text" v-model="settings.recaptcha.secret" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="submit-wrapper">
                <button v-on:click.prevent="saveSettings($event.target)" class="button button-primary"><?php _e( 'Save Changes', 'weforms' ); ?></button>
            </div>
        </div>

        <?php do_action( 'weforms_settings' ); ?>
    </div>
</div></script>
