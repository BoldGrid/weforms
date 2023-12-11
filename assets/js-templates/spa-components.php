<script type="text/x-template" id="tmpl-wpuf-component-table">
<div>

    <div class="tablenav top">

        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php esc_html_e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="restore" v-if="status == 'trash' ">
                    <?php esc_html_e( 'Restore Entries', 'weforms' ); ?>
                </option>
                <option value="delete">
                    <template v-if="status == 'trash' "><?php esc_html_e( 'Delete Permanently', 'weforms' ); ?></template>
                    <template v-else><?php esc_html_e( 'Delete Entries', 'weforms' ); ?></template>
                </option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php esc_html_e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="alignleft actions" v-if="has_export !== 'no' &&  status != 'trash' ">
            <a class="button" :href="'admin-post.php?action=weforms_export_form_entries&selected_forms=' + id + '&_wpnonce=' + '<?php echo esc_attr( wp_create_nonce( 'weforms-export-entries' ) ); ?>'" style="margin-top: 0;"><span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( 'Export Entries', 'weforms' ); ?></a>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php esc_html_e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php esc_html_e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped wpuf-contact-form">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th class="col-entry-details"><?php esc_html_e( 'Actions', 'weforms' ); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <tr v-if="loading">
                <td v-bind:colspan="columnLength + 3"><?php esc_html_e( 'Loading...', 'weforms' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td v-bind:colspan="columnLength + 3"><?php esc_html_e( 'No entries found!', 'weforms' ); ?></td>
            </tr>
            <tr v-for="(entry, index) in items">
                <template v-if="currentPage == 1">
                    <template v-if="index < perPage">
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="post[]" v-model="checkedItems" :value="entry.id">
                        </th>
                        <td v-for="(header, index) in columns"><span v-html="entry.fields[index]"></span></td>
                        <th class="col-entry-details">
                            <template v-if="status == 'trash'">
                                <a href="#" @click.prevent="restore(entry.id)"><?php esc_html_e( 'Restore', 'weforms' ); ?></a>
                                <span style="color: #ddd">|</span>
                                <a href="#" @click.prevent="deletePermanently(entry.id)"><?php esc_html_e( 'Delete Permanently', 'weforms' ); ?></a>
                            </template>
                            <template  v-else>
                                <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">
                                    <?php esc_html_e( 'Details', 'weforms' ); ?>
                                </router-link>
                            </template>
                        </th>
                    </template>
                </template>
                <template v-else>
                    <th scope="row" class="check-column">
                        <input type="checkbox" name="post[]" v-model="checkedItems" :value="entry.id">
                    </th>
                    <td v-for="(header, index) in columns"><span v-html="entry.fields[index]"></span></td>
                    <th class="col-entry-details">
                        <template v-if="status == 'trash'">
                            <a href="#" @click.prevent="restore(entry.id)"><?php esc_html_e( 'Restore', 'weforms' ); ?></a>
                            <span style="color: #ddd">|</span>
                            <a href="#" @click.prevent="deletePermanently(entry.id)"><?php esc_html_e( 'Delete Permanently', 'weforms' ); ?></a>
                        </template>
                        <template  v-else>
                            <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">
                                <?php esc_html_e( 'Details', 'weforms' ); ?>
                            </router-link>
                        </template>
                    </th>
                </template>
            </tr>
        </tbody>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php esc_html_e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="restore" v-if="status == 'trash' ">
                    <?php esc_html_e( 'Restore Entries', 'weforms' ); ?>
                </option>
                <option value="delete">
                    <template v-if="status == 'trash' "><?php esc_html_e( 'Delete Permanently', 'weforms' ); ?></template>
                    <template v-else><?php esc_html_e( 'Delete Entries', 'weforms' ); ?></template>
                </option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php esc_html_e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php esc_html_e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> of <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-entries">
<div class="wpuf-contact-form-entries">
    <div>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Entries', 'weforms' ); ?>
            <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>

            <span style="color: #999;" class="form-name">
                {{ form_title }}
            </span>

            <select v-if="Object.keys(forms).length" v-model="selected" @change="status='publish'">
                <option :value="form.id" v-for="form in forms">{{ form.name }}</option>
            </select>
        </h1>
    </div>

    <div>
        <ul class="subsubsub">
            <li class="all">
                <a href="#" :class="{ current: status =='publish' }" @click.prevent="status='publish'">
                    All
                    <span class="count">
                        ({{total}})
                    </span>
                </a> |
            </li>
            <li class="trash">
                <a href="#" :class="{ current: status =='trash' }" @click.prevent="status='trash'">
                    Trash
                    <span class="count">
                        ({{totalTrash}})
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <div>
        <template v-if="selected">

            <wpuf-table
                action="weforms_form_entries"
                :status="status"
                :id="selected"
                v-on:ajaxsuccess="
                form_title       = $event.form_title;
                $route.params.id = selected;
                total            = $event.meta.total;
                totalTrash       = $event.meta.totalTrash
                "
            >
            </wpuf-table>
        </template>
    </div>

</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-builder">
<form id="wpuf-form-builder" class="wpuf-form-builder-contact_form" method="post" action="" @submit.prevent="save_form_builder" v-cloak>
    <fieldset :class="[is_form_saving ? 'disabled' : '']" :disabled="is_form_saving">

        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-form-builder-container" :class="['nav-tab', isActiveTab( 'editor' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActive('editor')">
                <?php esc_html_e( 'Form Editor', 'weforms' ); ?>
            </a>

            <a href="#wpuf-form-builder-settings" :class="['nav-tab', isActiveTab( 'settings' ) ? 'nav-tab-active' : '']" v-on:click.prevent="makeActive('settings')">
                <?php esc_html_e( 'Settings', 'weforms' ); ?>
            </a>

            <?php do_action( 'wpuf-form-builder-tabs-contact_form' ); ?>

            <span class="pull-right">
                <a :href="'<?php echo home_url( '/' ); ?>?weforms_preview=1&form_id=' + post.ID" target="_blank" class="button"><span class="dashicons dashicons-visibility" style="padding-top: 3px;"></span> <?php esc_html_e( 'Preview', 'weforms' ); ?></a>

                <button v-if="!is_form_saving" type="button" class="button button-primary weforms-save-form-builder" @click="save_form_builder">
                    <?php esc_html_e( 'Save Form', 'weforms' ); ?>
                </button>

                <button v-else type="button" class="button button-primary button-ajax-working" disabled>
                    <span class="loader"></span> <?php esc_html_e( 'Saving Form Data', 'weforms' ); ?>
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
                            <?php esc_html_e( 'Share', 'Share' ); ?>
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
                                    <?php esc_html_e( 'Add Fields', 'weforms' ); ?>
                                </a>
                            </li>

                            <li :class="['field-options' === current_panel ? 'active' : '', !form_fields_count ? 'disabled' : '']">
                                <a href="#field-options" @click.prevent="set_current_panel('field-options')">
                                    <?php esc_html_e( 'Field Options', 'weforms' ); ?>
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
                        <?php do_action( 'wpuf-form-builder-settings-tabs-contact_form' ); ?>
                    </h2><!-- #wpuf-form-builder-settings-tabs -->

                    <div id="wpuf-form-builder-settings-contents" class="tab-contents">
                        <?php do_action( 'wpuf-form-builder-settings-tab-contents-contact_form' ); ?>
                    </div><!-- #wpuf-form-builder-settings-contents -->
                </fieldset>
            </div><!-- #wpuf-form-builder-settings -->

            <?php do_action( 'wpuf-form-builder-tab-contents-contact_form' ); ?>
        </div>
        <div v-else>

            <div class="updating-message">
                <p><?php esc_html_e( 'Loading the editor', 'weforms' ); ?></p>
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
    <div>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Entries', 'weforms' ); ?>
            <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>

            <span style="color: #999;" class="form-name">
                {{ form_title }}
            </span>

            <router-link class="page-title-action" to="/"><?php esc_html_e( 'Back to forms', 'weforms' ); ?></router-link>
        </h1>
    </div>

    <div>
        <ul class="subsubsub">
            <li class="all">
                <a href="#" :class="{ current: status =='publish' }" @click.prevent="status='publish'">
                    All
                    <span class="count">
                        ({{total}})
                    </span>
                </a> |
            </li>
            <li class="trash">
                <a href="#" :class="{ current: status =='trash' }" @click.prevent="status='trash'">
                    Trash
                    <span class="count">
                        ({{totalTrash}})
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <div>
        <template>

            <wpuf-table
                action="weforms_form_entries"
                :status="status"
                :id="id"
                v-on:ajaxsuccess="
                form_title       = $event.form_title;
                total            = $event.meta.total;
                totalTrash       = $event.meta.totalTrash
                "
            >
            </wpuf-table>
        </template>
    </div>

</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-entry-single">
<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Entry Details', 'weforms' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php esc_html_e( 'Back to Entries', 'weforms' ); ?></router-link>

    <div v-if="loading"><?php esc_html_e( 'Loading...', 'weforms' ); ?></div>
    <div v-else class="wpuf-contact-form-entry-wrap">

        <div v-bind:class="['wpuf-contact-form-entry-left', form_settings.quiz_form === 'yes' ? 'weforms-quiz-entry' : '']">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle">
                    <span>{{ entry.meta_data.form_title }} : Entry # {{ $route.params.entryid }}</span>
                    <span class="pull-right" v-if="hasEmpty">
                        <label style="font-weight: normal; font-size: 12px">
                            <input type="checkbox" v-model="hideEmpty" style="margin-right: 1px"> <?php esc_html_e( 'Hide Empty', 'weforms' ); ?>
                        </label>
                    </span>
                </h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(field, index) in entry.form_fields">
                                <template v-if="field.value || ! hideEmpty ">
                                    <tr v-bind:class="['field-label', answers[field.name] ? 'right-answer' : 'wrong-answer']">
                                        <th>
                                            <strong>{{ field.label }}</strong>
                                            <strong v-if="form_settings.quiz_form === 'yes'" class="field-points">
                                                <template v-if="answers[field.name] === true">{{ field.points}}/{{field.points}}</template>
                                                <template v-else>0/{{field.points}}</template>
                                            </strong>
                                        </th>
                                    </tr>
                                    <tr v-bind:class="['field-value', answers[field.name] ? 'right-answer' : 'wrong-answer']">
                                        <td>
                                            <weforms-entry-gmap :lat="field.value.lat" :long="field.value.long" :zoom="field.zoom" v-if="field.type == 'google_map'"></weforms-entry-gmap>
                                            <div v-else-if="field.type === 'checkbox_field' || field.type === 'multiple_select'">
                                                <ul style="margin: 0;">
                                                    <li v-for="item in field.value">- {{ item }}</li>
                                                </ul>
                                            </div>
                                            <div v-else-if="field.type === 'country_list_field'">{{ getCountryName( field.value ) }}</div>
                                            <div v-else-if="field.type === 'address_field'" v-html="getAddressFieldValue( field.value)"></div>
                                            <div v-else v-html="field.value"></div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside"><?php esc_html_e( 'Loading...', 'weforms' ); ?></div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Submission Info', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php esc_html_e( 'Entry ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ $route.params.entryid }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'User IP', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.ip_address }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Device', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.device }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Page', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.meta_data.referer">{{ entry.meta_data.referer }}</a></span>
                            </li>
                            <li v-if="entry.meta_data.user">
                                <span class="label"><?php esc_html_e( 'From', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Submitted On', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.created }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <button class="button button-large button-secondary" v-on:click.prevent="trashEntry"><span class="dashicons dashicons-trash"></span><?php esc_html_e( ' Delete', 'weforms' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <div v-if="form_settings.quiz_form === 'yes'" class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Points', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">
                        <p><?php esc_html_e( 'Total Points:', 'weforms' ); ?> {{ form_settings.total_points }}</p>
                        <p><?php esc_html_e( 'Respondent Points:', 'weforms' ); ?> {{ respondent_points }}</p>
                    </div>
                </div>
            </div>

            <?php do_action( 'weforms_entry_single_right_metabox' ); ?>
        </div>

        <div class="wpuf-contact-form-entry-right" v-if="entry.payment_data" style=" clear: right;">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Payment Info', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php esc_html_e( 'Payment ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ entry.payment_data.id }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Gateway', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.gateway }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Status', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.status }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Total', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.total }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Transaction ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.transaction_id ? entry.payment_data.transaction_id : 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="label"><?php esc_html_e( 'Created at', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.created_at }}</span>
                            </li>

                            <li v-if="entry.payment_data.payment_data">

                                <template v-if="show_payment_data" class="value" v-for="(val,key) in entry.payment_data.payment_data">
                                    <template v-if="key && (val === false || val)">
                                        <li>
                                            <span class="label">{{ key }}</span>
                                            <span class="sep"> : </span>
                                            <span class="value"> {{ val }}</span>
                                        </li>
                                    </template>
                                </template>

                                <span class="value"> <a href="#" @click.prevent="show_payment_data = !show_payment_data"> {{ show_payment_data ? 'Hide' : 'Show More' }} </a> </span>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-list-table">
<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php esc_html_e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php esc_html_e( 'Delete Forms', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php esc_html_e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php esc_html_e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php esc_html_e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage || totalPage == 0" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage() || totalPage == 0" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All', 'weforms' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php esc_html_e( 'Name', 'weforms' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php esc_html_e( 'Shortcode', 'weforms' ); ?></th>
                <th scope="col" class="col-form-entries"><?php esc_html_e( 'Entries', 'weforms' ); ?></th>
                <th scope="col" class="col-form-status weforms-form-status-col-title"><?php esc_html_e( 'Status', 'weforms' ); ?></th>
                <th scope="col" class="col-form-views"><?php esc_html_e( 'Views', 'weforms' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php esc_html_e( 'Conversion', 'weforms' ); ?></th>
                <th scope="col" class="col-form-created-by"><?php esc_html_e( 'Created by', 'weforms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6"><?php esc_html_e( 'Loading...', 'weforms' ); ?></td>
            </tr>
            <tr v-if="!items.length && !loading">
                <td colspan="6"><?php esc_html_e( 'No form found!', 'weforms' ); ?></td>
            </tr>
            <tr v-for="(form, index) in items">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" v-model="checkedItems" :value="form.id">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><router-link :to="{ name: 'edit', params: { id: form.id }}">{{ form.name }}</router-link> <span v-if="form.data.post_status != 'publish'">({{ form.data.post_status }})</span></strong>

                    <div class="row-actions">
                        <span class="edit"><router-link :to="{ name: 'edit', params: { id: form.id }}"><?php esc_html_e( 'Edit', 'weforms' ); ?></router-link> | </span>

                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete"><?php esc_html_e( 'Delete', 'weforms' ); ?></a> | </span>

                        <span class="duplicate"><a href="#" v-on:click.prevent="duplicate(form.id, index)"><?php esc_html_e( 'Duplicate', 'weforms' ); ?></a> <template v-if="form.entries">|</template> </span>

                        <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.id }}"><?php esc_html_e( 'View Entries', 'weforms' ); ?></router-link>

                        <template v-if="is_pro">
                            <span>
                                <template>|</template>
                                <router-link :to="{ name: 'formReports', params: { id: form.id }}"><?php esc_html_e( 'Reports', 'weforms' ); ?></router-link>
                            </span>
                        </template>

                        <template v-if="is_pro && has_payment && form.payments">
                            <span>
                                <template>|</template>
                                <router-link :to="{ name: 'formPayments', params: { id: form.id }}"><?php esc_html_e( 'Transactions', 'weforms' ); ?></router-link>
                            </span>
                        </template>
                    </div>
                </td>
                <td><code>[weforms id="{{ form.id }}"]</code></td>
                <td>
                    <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.id }}">{{ form.entries }}</router-link>
                    <span v-else>&mdash;</span>
                </td>
                <td class="weforms-form-status" >
                    <p v-if="isFormStatusClosed(form.settings, form.entries)">
                        <?php esc_html_e( 'Closed', 'weforms' ); ?>
                    </p>
                    <p v-else class="open"><?php esc_html_e( 'Open', 'weforms' ); ?></p>

                    <template v-if="form.settings.limit_entries === 'true'">
                        <span v-if="form.settings.schedule_form === 'true' && isExpiredForm(form.settings.schedule_end)">(Expired at {{formatTime(form.settings.schedule_end)}})</span>
                        <span v-else-if="form.settings.schedule_form === 'true' && isPendingForm(form.settings.schedule_start)">(Starts at {{formatTime(form.settings.schedule_start)}})</span>
                        <span v-else-if="form.entries >= form.settings.limit_number">(Reached maximum entry limit)</span>
                        <span v-else>({{form.settings.limit_number - form.entries}} entries remaining)</span>
                    </template>

                    <template v-else-if="form.settings.schedule_form === 'true'">
                        <span v-if="isPendingForm(form.settings.schedule_start)">(Starts at {{formatTime(form.settings.schedule_start)}})</span>
                        <span v-if="isExpiredForm(form.settings.schedule_end)">(Expired at {{formatTime(form.settings.schedule_end)}})</span>
                        <span v-if="isOpenForm(form.settings.schedule_start, form.settings.schedule_end)">(Expires at {{formatTime(form.settings.schedule_end)}})</span>
                    </template>

                    <template v-else-if="form.settings.require_login  === 'true'">
                        <span><?php esc_html_e( '(Requires login)', 'weforms' ); ?></span>
                    </template>
                </td>
                <td>{{ form.views }}</td>
                <td>
                    <span v-if="form.views">{{ ((form.entries/form.views) * 100).toFixed(2) }}%</span>
                    <span v-else>0%</span>
                </td>
                <td v-if="form.author" class="weforms-form-creator">
                    <img v-bind:src="form.author.avatar">
                    <span>{{ form.author.username }}</span>
                    <span class="date">{{formatTime(form.data.post_date)}}</span>
                </td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All', 'weforms' ); ?></label>
                    <input type="checkbox" v-model="selectAll">
                </td>
                <th scope="col" class="col-form-name"><?php esc_html_e( 'Name', 'weforms' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php esc_html_e( 'Shortcode', 'weforms' ); ?></th>
                <th scope="col" class="col-form-entries"><?php esc_html_e( 'Entries', 'weforms' ); ?></th>
                <th scope="col" class="col-form-status weforms-form-status-col-title"><?php esc_html_e( 'Status', 'weforms' ); ?></th>
                <th scope="col" class="col-form-views"><?php esc_html_e( 'Views', 'weforms' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php esc_html_e( 'Conversion', 'weforms' ); ?></th>
                <th scope="col" class="col-form-created-by"><?php esc_html_e( 'Created by', 'weforms' ); ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'weforms' ); ?></label>
            <select name="action" v-model="bulkAction">
                <option value="-1"><?php esc_html_e( 'Bulk Actions', 'weforms' ); ?></option>
                <option value="delete"><?php esc_html_e( 'Delete Forms', 'weforms' ); ?></option>
            </select>

            <button class="button action" v-on:click.prevent="handleBulkAction"><?php esc_html_e( 'Apply', 'weforms' ); ?></button>
        </div>

        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} <?php esc_html_e( 'items', 'weforms' ); ?></span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'weforms' ); ?></span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'weforms' ); ?></span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'weforms' ); ?></span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> <?php esc_html_e( 'of', 'weforms' ); ?> <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage || totalPage == 0" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'weforms' ); ?></span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage() || totalPage == 0" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'weforms' ); ?></span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-form-payments">
<div class="wpuf-contact-form-payments">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Transactions', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
        <span style="color: #999;" class="form-name">{{ form_title }}</span>
    </h1>

    <router-link class="page-title-action" to="/"><?php esc_html_e( 'Back to forms', 'weforms' ); ?></router-link>

    <wpuf-table
        has_export="no"
    	action="weforms_form_payments"
    	delete="weforms_form_payments_trash_bulk"
    	:id="id"
    	v-on:ajaxsuccess="form_title = $event.form_title"
    >
    </wpuf-table>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-home-page">
<div class="contact-form-list">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'All Forms', 'weforms' ); ?></h1>
    <a class="page-title-action add-form" herf="#" v-on:click.prevent="displayModal()"><?php esc_html_e( 'Add Form', 'weforms' ); ?></a>

    <wpuf-template-modal :show.sync="showTemplateModal" :onClose="closeModal"></wpuf-template-modal>

    <form-list-table></form-list-table>
</div></script>

<script type="text/x-template" id="tmpl-wpuf-tools">
<div class="export-import-wrap">

    <h2 class="nav-tab-wrapper">
        <a :class="['nav-tab', isActiveTab( 'export' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('export')"><?php esc_html_e( 'Export', 'wpuf' ); ?></a>
        <a :class="['nav-tab', isActiveTab( 'import' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('import')"><?php esc_html_e( 'Import', 'wpuf' ); ?></a>
        <a :class="['nav-tab', isActiveTab( 'logs' ) ? 'nav-tab-active' : '']" href="#" v-on:click.prevent="makeActive('logs')"><?php esc_html_e( 'Logs', 'wpuf' ); ?></a>
    </h2>

    <div class="nav-tab-content">
        <div class="nav-tab-inside" v-show="isActiveTab('export')">
            <h3><?php esc_html_e( 'Export Utility', 'weforms' ); ?></h3>

            <p><?php esc_html_e( 'You can export your form, as well as exporting the submitted entries by the users', 'weforms' ); ?></p>

            <div class="postboxes metabox-holder two-col">
                <div class="postbox">
                    <h3 class="hndle"><?php esc_html_e( 'Export Forms', 'weforms' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php esc_html_e( 'You can export your existing contact forms and import the same forms into a different site.', 'weforms' ); ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=weforms_export_forms" method="post">
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="all" checked> <?php esc_html_e( 'All Forms', 'weforms' ); ?></label></p>
                                <p><label><input v-model="exportType" type="radio" name="export_type" value="selected"> <?php esc_html_e( 'Selected Forms', 'weforms' ); ?></label></p>
                                <p v-show="exportType == 'selected'">
                                    <select name="selected_forms[]" class="forms-list" multiple="multiple">
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'weforms-export-forms' ); ?>
                                <input type="submit" class="button button-primary" name="weforms_export_forms" value="<?php esc_html_e( 'Export Forms', 'weforms' ); ?>">
                            </form>
                        </template>
                        <template v-else>
                            <div class="spinner loading-spinner is-active"></div>
                        </template>
                    </div>
                </div><!-- .postbox -->

                <div class="postbox">
                    <h3 class="hndle"><?php esc_html_e( 'Export Form Entries', 'weforms' ); ?></h3>

                    <div class="inside">
                        <p class="help">
                            <?php
                                // translators: 1: Opening strong tag. 2: closing strong tag
                                printf(
                                    esc_html__( 'Export your form entries/submissions as a %1$sCSV%2$s file.', 'weforms' ),
                                    '<strong>',
                                    '</strong>'
                                );
                            ?>
                        </p>

                        <template v-if="!loading">
                            <form action="admin-post.php?action=weforms_export_form_entries" method="post">
                                <p>
                                    <select name="selected_forms" class="forms-list">
                                        <option value=""><?php esc_html_e( '&mdash; Select Form &mdash;', 'weforms' ); ?></option>
                                        <option v-for="entry in forms" :value="entry.id">{{ entry.title }}</option>
                                    </select>
                                </p>

                                <?php wp_nonce_field( 'weforms-export-entries' ); ?>
                                <input type="submit" class="button button-primary" name="weforms_export_entries" value="<?php esc_html_e( 'Export Entries', 'weforms' ); ?>">
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
            <h3><?php esc_html_e( 'Import Contact Form', 'weforms' ); ?></h3>

            <p><?php esc_html_e( 'Browse and locate a json file you backed up before.', 'weforms' ); ?></p>
            <p><?php
                    // translators: 1: Opening strong tag. 2: closing strong tag.
                    printf(
                        esc_html__( 'Press %1$sImport%2$s button, we will do the rest for you.', 'weforms' ),
                        '<strong>',
                        '</strong>'
                    );
                ?>
            </p>

            <div class="updated-message notice notice-success is-dismissible" v-if="isSuccess">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'weforms' ); ?></span>
                </button>
            </div>

            <div class="update-message notice notice-error is-dismissible" v-if="isFailed">
                <p>{{ responseMessage }}</p>

                <button type="button" class="notice-dismiss" v-on:click="currentStatus = 0">
                    <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'weforms' ); ?></span>
                </button>
            </div>

            <form action="" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="file" name="importFile" v-on:change="importForm( $event.target.name, $event.target.files, $event )" accept="application/json" />
                <button type="submit" :class="['button', isSaving ? 'updating-message' : '']" disabled="disabled">{{ importButton }}</button>
            </form>

            <hr>
            <h3><?php esc_html_e( 'Import Other Forms', 'weforms' ); ?></h3>
            <p><?php esc_html_e( 'You can import other WordPress form plugins into weForms.', 'weforms' ); ?></p>

            <div class="updated" v-if="ximport.title">
                <p><strong>{{ ximport.title }}</strong></p>

                <p>{{ ximport.message }}</p>
                <p>{{ ximport.action }}</p>

                <ul v-if="hasRefs">
                    <li v-for="ref in ximport.refs">
                        <a target="_blank" :href="'admin.php?page=weforms#/form/' + ref.weforms_id + '/edit'">{{ ref.title }}</a> - <a :href="'admin.php?page=weforms#/form/' + ref.weforms_id + '/edit'" target="_blank" class="button button-small"><span class="dashicons dashicons-external"></span> <?php esc_html_e( 'Edit', 'weforms' ); ?></a>
                    </li>
                </ul>

                <p>
                    <a href="#" class="button button-primary" @click.prevent="replaceX($event.target, 'replace')"><?php esc_html_e( 'Replace Shortcodes', 'weforms' ); ?></a>&nbsp;
                    <a href="#" class="button" @click.prevent="replaceX($event.target, 'skip')"><?php esc_html_e( 'No Thanks', 'weforms' ); ?></a>
                </p>
            </div>


            <table style="min-width: 500px;">
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'Contact Form 7', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'cf7')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php esc_html_e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Ninja Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'nf')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php esc_html_e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Caldera Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'caldera-forms')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php esc_html_e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Gravity Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'gf')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php esc_html_e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Forms', 'weforms' ); ?></td>
                        <th><button class="button" @click.prevent="importx($event.target, 'wpforms')" data-importing="<?php esc_attr_e( 'Importing...', 'weforms' ); ?>" data-original="<?php esc_attr_e( 'Import', 'weforms' ); ?>"><?php esc_html_e( 'Import', 'weforms' ); ?></button></th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="nav-tab-inside" v-show="isActiveTab('logs')">
            <h3>
                <?php esc_html_e( 'Logs', 'weforms' ); ?>

                <span class="pull-right">

                    <button class="button button-large button-secondary" @click.prevent="fetchLogs($event.target)">
                        <span style="margin-top: 4px" class="dashicons dashicons-image-rotate"></span> Reload
                    </button>

                    <button class="button button-large button-secondary" @click.prevent="deleteLogs($event.target)" v-if="hasLogs">
                        <span style="margin-top: 4px" class="dashicons dashicons-trash"></span> Delete
                    </button>
                </span>
            </h3>

            <table class="wp-list-table widefat fixed striped wpuf-contact-form" v-if="hasLogs">
                <thead>
                    <tr>
                        <th style="width: 10%"> Type </th>
                        <th style="width: 15%"> Time </th>
                        <th style="width: 75%"> Details </th> </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs">
                        <td> <span> {{ log.type }} </span> </td>
                        <td> <span> {{ log.time }} </span> </td>
                        <td> {{ log.message }} </td>
                    </tr>
                </tbody>
            </table>
            <div v-else>
                <p><?php esc_html_e( 'No logs found. If any error occurs during an action. Those will be displayed here.', 'weforms' ); ?></p>
            </div>
        </div>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-transactions">
<div class="wpuf-contact-form-transactions">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Transactions', 'weforms' ); ?>
        <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>
           <span style="color: #999;" class="form-name">
            {{ form_title }}
        </span>

        <select v-if="Object.keys(forms).length " v-model="selected">
            <option :value="form.id" v-for="form in forms">{{ form.name }}</option>
        </select>
    </h1>

    <p v-if="no_transactions">
       <?php printf(
            __( 'You don\'t have any transactions yet. Learn how to %sset up payment integration%s and take payments with weFroms.' ),
            '<a target="_blank" href="https://weformspro.com/docs/modules/payment/">',
            '</a>'
              );
        ?>
    </p>

    <wpuf-table v-if="selected"
        has_export="no"
        action="weforms_form_payments"
        delete="weforms_form_payments_trash_bulk"
        :id="selected"
        v-on:ajaxsuccess="form_title = $event.form_title; $route.params.id = selected"
    >
    </wpuf-table>

</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-weforms-page-help">
<div class="weforms-help-page">

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/docs.svg" alt="<?php esc_attr_e( 'Looking for Something?', 'weforms' ); ?>">

        <h3><?php esc_html_e( 'Looking for Something?', 'weforms' ); ?></h3>

        <p><?php esc_html_e( 'We have detailed documentation on every aspects of weForms.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://weformspro.com/docs/?utm_source=weforms-help-page&utm_medium=help-block&utm_campaign=plugin-docs-link"><?php esc_html_e( 'Visit the Plugin Documentation', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/support.svg" alt="<?php esc_attr_e( 'Need Any Assistance?', 'weforms' ); ?>">

        <h3><?php esc_html_e( 'Need Any Assistance?', 'weforms' ); ?></h3>

        <p><?php esc_html_e( 'Our EXPERT Support Team is always ready to Help you out.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="<?php echo class_exists( 'WeForms_Pro' ) ? esc_url( 'https://weformspro.com/account/premium-support/' ) : esc_url( 'https://wordpress.org/support/plugin/weforms/' );?>"><?php esc_html_e( 'Contact Support', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/bugs.svg" alt="<?php esc_attr_e( 'Found Any Bugs?', 'weforms' ); ?>">

        <h3><?php esc_html_e( 'Found Any Bugs?', 'weforms' ); ?></h3>

        <p><?php esc_html_e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://github.com/BoldGrid/weforms/issues/new"><?php esc_html_e( 'Report to GitHub', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/customization.svg" alt="<?php esc_attr_e( 'Require Customization?', 'weforms' ); ?>">

        <h3><?php esc_html_e( 'Require Customization?', 'weforms' ); ?></h3>

        <p><?php esc_html_e( 'We would Love to hear your Integration and Customization Ideas.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://weformspro.com/support/?utm_source=weforms-help-page&utm_medium=help-block&utm_campaign=requires-customization"><?php esc_html_e( 'Contact Our Services', 'weforms' ); ?></a>
    </div>

    <div class="help-block">
        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/help/like.svg" alt="<?php esc_attr_e( 'Like The Plugin?', 'weforms' ); ?>">

        <h3><?php esc_html_e( 'Like The Plugin?', 'weforms' ); ?></h3>

        <p><?php esc_html_e( 'Your Review is very important to us as it helps us to grow more.', 'weforms' ); ?></p>

        <a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/weforms/reviews/?rate=5#new-post"><?php esc_html_e( 'Review Us on WP.org', 'weforms' ); ?></a>
    </div>
</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-weforms-page-privacy">
<div class="wpuf-privacy-page">

<h1><?php esc_html_e('Privacy' , 'weforms'); ?></h1>

<h2><?php esc_html_e( 'Your website may need a Privacy Policy by law' , 'weforms' ); ?></h2>

<p><?php esc_html_e( 'We at weForms take privacy law compliance seriously, and we want our customers to know just how important it is for them as well.', 'weforms' ); ?></p>

<p><?php esc_html_e( 'Because you are implementing a contact form, that means you may be collecting the Personally Identifiable Information (PII) of individuals. This means that there may be privacy laws that apply to your website that require you to have a Privacy Policy. It is important to understand that if your website collects PII from users outside of your jurisdiction, you still may need to comply with the privacy laws of other states and countries. Please note that non-compliance may put you in danger of significant privacy related fines and lawsuits.' , 'weforms' ); ?></p>

<p><?php esc_html_e( 'If you do not have a Privacy Policy or are unsure if yours is up to date and compliant, we encourage you to generate one with our partner, Termageddon. Termageddon is a generator of Privacy Policies, Terms &amp; Conditions and more. They monitor privacy laws for you and update your Privacy Policy when new disclosures are required.' , 'weforms' ); ?></p>

<p><?php esc_html_e( 'If you decide Termageddon is a good solution for your website, use the promo code <strong>WEFORMS</strong> for 10&#37; off your first purchase at checkout. More information on Termageddon can be found <a href="https://app.termageddon.com/?fp_ref=weforms" target="_blank">here</a>.' , 'weforms' ); ?></p>

<p><?php esc_html_e( 'If you own a web design, web development or digital marketing company, check out <a href="https://termageddon.com/agency-partners/" target="_blank">Termageddon&#39;s agency partner program</a>, where you can offer Termageddon licenses to your clients, helping them stay compliant with privacy laws.' , 'weforms' ); ?></p>

<h2><?php esc_html_e( 'How to add Privacy Policy consent to your forms' , 'weforms' ); ?></h2>

<p><?php esc_html_e( 'Adding a Privacy Policy consent checkbox is your forms is simple. All you will need is the weForms plugin and an existing /privacy-policy page.' , 'weforms' ); ?></p>

<ol>
    <li><?php esc_html_e( '"Edit" the respective form that you&#39;d like to add a consent checkbox to.' , 'weforms' ); ?></li>
    <li><?php esc_html_e( 'Click &#39;checkbox&#39; so that it is added to the bottom of your form.' , 'weforms' ); ?></li>
    <li><?php esc_html_e( '"Edit" the consent checkbox, remove the &#39;Field Label&#39;, and replace &#39;Option&#39; with &#39;INSERT YOUR "I AGREE" LANGUAGE HERE AND PROVIDE A LINK TO YOUR PRIVACY POLICY&#39;.' , 'weforms' ); ?></li>
</ol>

</div></script>

<script type="text/x-template" id="tmpl-wpuf-weforms-premium">
<div class="weforms-premium">
    <?php
        // esc_html_e( 'weForms Pro', 'weforms' );
        // echo WeForms_Form_Builder_Assets::get_pro_url();
        //  echo WEFORMS_ASSET_URI; /images/integrations/mailchimp.svg"
    ?>
    <!-- start banner section -->
    <div id="banner" class="wf-banner-section wf-section-wrapper">
        <!-- banner left column -->
        <div class="banner-left-column">
            <div class="banner-icon">
                <svg width="25px" height="28px" viewBox="0 0 25 28" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Premium-Page-Design-for-weForms" transform="translate(-298.000000, -174.000000)" fill="#4CDAA0">
                            <g id="Group-3" transform="translate(285.000000, 163.000000)">
                                <g id="Page-1-Copy-6" transform="translate(13.076923, 11.538462)">
                                    <path d="M22.4734843,6.65402042 L4.95080724,13.9647562 C3.1534996,14.7235956 1.06664956,13.9130012 0.290961897,12.1540325 C-0.484725767,10.3957631 0.343865941,8.35424032 2.14260342,7.59540091 L19.6645656,0.284665163 C21.4625881,-0.474174241 23.5487233,0.336420108 24.3244109,2.09468947 C25.1000986,3.85435762 24.2715069,5.89518101 22.4734843,6.65402042 M22.4734843,19.3276876 L4.95080724,26.6384233 C3.1534996,27.3972627 1.06664956,26.5866684 0.290961897,24.8276996 C-0.484725767,23.0687308 0.343865941,21.0279075 2.14260342,20.269068 L19.6645656,12.9583323 C21.4625881,12.1994929 23.5487233,13.0100872 24.3244109,14.769056 C25.1000986,16.5280248 24.2715069,18.5688481 22.4734843,19.3276876" id="Fill-1"></path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="banner-content">
                <h1><?php esc_html_e( 'weForms Pro', 'weforms' ); ?></h1>
                <p><?php esc_html_e( 'Upgrade to the premium versions of weForms and <br>unlock even more useful features.' ); ?></p>
            </div>
            <div class="banner-buttons">
                <a href="https://weformspro.com/pricing" class="wf-btn wf-btn-primary" target="_blank"><?php esc_html_e( 'Buy Now', 'weforms' ); ?></a>
                <a href="https://weformspro.com/docs/" class="wf-btn wf-btn-default" target="_blank"><?php esc_html_e( 'Read Full Guide', 'weforms' ); ?></a>
            </div>
        </div><!-- end banner left column -->


        <!-- video modal -->

        <div id="wf-video-modal" :class="['wf-modal', showModal ? 'wf-modal-open': '']" role="dialog" @click="showModal = false">
            <div class="wf-modal-dialog">
                <div class="wf-modal-content">
                    <span class="modal-close" @click="showModal = false">x</span>
                    <div class="wf-modal-body">
                        <iframe width="600px" height="400px" src="https://www.youtube.com/embed/668nUCeBHyY?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>


        <!-- banner right column -->
        <div class="banner-right-column">
            <div class="banner-thumb">
                <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/banner-thumb.svg" alt="Banner">
                <!-- <a class="video-play-icon" href="#" @click.prevent="showModal = true">
                    <svg width="15px" height="17px" viewBox="0 0 15 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <linearGradient x1="50%" y1="100%" x2="12.980572%" y2="0%" id="linearGradient-1">
                                <stop stop-color="#12CE66" offset="0%"></stop>
                                <stop stop-color="#7EE6D1" offset="100%"></stop>
                            </linearGradient>
                        </defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Premium-Page-Design-for-weForms" transform="translate(-1106.000000, -335.000000)" fill="url(#linearGradient-1)">
                                <g id="Group-9" transform="translate(1087.000000, 318.000000)">
                                    <path d="M32.552792,24.6413241 L21.1089656,18.2019425 C20.0661123,17.6145297 19.2249533,18.0841645 19.2307995,19.2479777 L19.2894009,32.0295103 C19.2946903,33.1930549 20.1461497,33.6667186 21.1920654,33.0838719 L32.5483378,26.7581051 C33.5931399,26.1763328 33.595367,25.2287369 32.552792,24.6413241 Z" id="Path"></path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a> -->
            </div>
        </div><!-- end banner right column -->

    </div><!-- end banner section -->

    <!-- start features section -->
    <div id="features" class="wf-features-wrapper wf-section-wrapper">
        <div class="section-header">
            <h2><?php esc_html_e( 'More Features', 'weforms' ); ?></h2>
        </div>
        <div class="section-content">
            <div class="feature-row">
                <div class="feature-column feature-advance-fields">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/advance-fields.svg" alt="Advanced Fields">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'Advance Fields', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Build any kind of form flexibly with the advanced field option. Its user friendly interface makes sure you do not have to scratch your head over building forms.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-conditional-logic">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/conditional-logic.svg" alt="Conditional Logic">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'Conditional Logic', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Configure your form’s settings and user flow based on conditional selection. Your forms should appear just the way you want it.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-multi-step">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/multistep-form.svg" alt="Multi Step">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'Multi-step Form', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Break down the long forms into small and attractive multi step forms. Long and lengthy forms are uninviting, why build one?', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-file-uploaders">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/file-uploader.svg" alt="File uploaders">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'File Uploaders', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Let the user upload any kind of file by filling up your contact form. The process is unbelievably smooth and supports a wide range of file formats.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-notification">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/notification.svg" alt="Form Submit Notification">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'Form Submission Notication', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Receive email notification every time your form is submitted. You can now configure the notification settings just as you like it.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-submission">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/submission.svg" alt="Manage Submission">
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e( 'Manage Submission', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'View, edit and manage all the submission data stored through your form. We believe that you should own it all- like literally!', 'weforms' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end features section -->

    <!-- start integration section -->
    <div id="integration" class="wf-integration-wrapper wf-section-wrapper">
        <div class="section-header">
            <h2><?php esc_html_e( 'More Integrations', 'weforms' ); ?></h2>
        </div>
        <div class="section-content">
            <div class="integration-row">
                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/mailchimp.svg" alt="Mailchimp integration">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'Mailchimp', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Integrate your desired form to your MailChimp email newsletter using latest API.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/campaign-monitor.svg" alt="Campaign Monitor">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'Campaign Monitor', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Lets you add submission form in your Campaign Monitor email campaigns too.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/constant-contact.svg" alt="Constant Contact">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'Constant Contact', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Integrate your contact forms seamlessly with your Constant Contact account.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/mailpoet.svg" alt="MailPoet">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'MailPoet', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Why only MailChimp? Do the same for MailPoet email campaigns as well!', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/aweber.svg" alt="AWeber">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'AWeber', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Use highly customizable forms and create subscriber’s list for AWber email solution.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/get-response.svg" alt="Get Response">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'Get Response', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Enjoy seamless integration of weForms with your Get Response account.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/convert-kit.svg" alt="ConvertKit">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'ConvertKit', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'Subscribe a contact to ConvertKit when a form is submited.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/more-integration.svg" alt="More.Integration">
                    </div>
                    <div class="integration-content">
                        <h3><?php esc_html_e( 'More...', 'weforms' ); ?></h3>
                        <p><?php esc_html_e( 'A bunch of more integrations are coming soon.', 'weforms' ); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- end integration section -->

    <!-- start footer section -->
    <section id="import" class="wf-import-wrapper">
        <div class="section-content">
            <div class="import-left">
                <div class="import-icon">

                    <svg width="32px" height="35px" viewBox="0 0 32 35" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Premium-Page-Design-for-weForms" transform="translate(-278.000000, -2204.000000)" fill="#FFFFFF">
                                <g id="Group-3" transform="translate(261.000000, 2189.000000)">
                                    <g id="Page-1-Copy-6" transform="translate(17.000000, 15.000000)">
                                        <path d="M29.2155296,8.65022654 L6.43604941,18.154183 C4.09954948,19.1406742 1.38664443,18.0869016 0.378250466,15.8002422 C-0.630143496,13.514492 0.447025723,10.8605124 2.78538444,9.87402119 L25.5639353,0.370064711 C27.9013646,-0.616426514 30.6133402,0.43734614 31.6217342,2.72309632 C32.6301282,5.01066491 31.5529589,7.66373532 29.2155296,8.65022654 M29.2155296,25.1259938 L6.43604941,34.6299503 C4.09954948,35.6164415 1.38664443,34.5626689 0.378250466,32.2760095 C-0.630143496,29.9893501 0.447025723,27.3362797 2.78538444,26.3497885 L25.5639353,16.845832 C27.9013646,15.8593408 30.6133402,16.9131134 31.6217342,19.1997728 C32.6301282,21.4864322 31.5529589,24.1395026 29.2155296,25.1259938" id="Fill-1"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="import-text">
                    <p><?php esc_html_e( 'Extend the functionalities while', 'weforms' ); ?></p>
                    <h2><?php esc_html_e( 'Building WordPress Forms', 'wefoms' ); ?></h2>
                </div>
            </div>
            <div class="import-right">
                <a href="https://weformspro.com/pricing" target="_blank" class="wf-btn wf-btn-primary wf-btn-lg"><?php esc_html_e( 'Upgrade Now', 'weforms' ); ?></a>
            </div>
        </div>
    </section><!-- end footer section -->

</div>
</script>

<script type="text/x-template" id="tmpl-wpuf-weforms-settings">
<div class="weforms-settings clearfix" id="weforms-settings">

    <h1><?php esc_html_e( 'Settings', 'weforms' ); ?></h1>
    <div id="weforms-settings-tabs-warp" class="<?php echo !function_exists( 'weforms_pro' ) ? 'weforms-pro-deactivate' : ''; ?>">
        <div id="weforms-settings-tabs">
            <ul>
                <?php
                $tabs = apply_filters( 'weforms_settings_tabs', [] );

                foreach ( $tabs as $key => $tab ) {
                    ?>
                    <li>
                        <a
                            href="#"
                            :class="['we-settings-nav-tab', isActiveTab( '<?php echo $key; ?>' ) ? 'we-settings-nav-tab-active' : '']"
                            v-on:click.prevent="makeActive( '<?php echo $key; ?>' )"
                        >
                            <?php

                            if ( !empty( $tab['icon'] ) ) {
                                printf( '<img src="%s">', $tab['icon'] );
                            } ?>
                            <?php esc_html_e( $tab['label'], 'weforms' ); ?>
                        </a>
                    </li>

                    <?php
                }

                do_action( 'weforms_settings_tabs_area' );
                ?>
            </ul>
        </div>

        <div id="weforms-settings-tabs-contents">

            <?php
                foreach ( $tabs as $key => $tab ) {
                    ?>
                    <div id="weforms-settings-<?php echo $key; ?>" class="tab-content" v-show="isActiveTab('<?php echo $key; ?>')">
                        <?php do_action( 'weforms_settings_tab_content_' . $key, $tab ); ?>
                    </div>
                    <?php
                }

                do_action( 'weforms_settings_tabs_contents' );
            ?>

        </div>
    </div>

    <?php if ( !function_exists( 'weforms_pro' ) ) { ?>

        <div id="weforms-settings-page-sidebar" class="weforms-settings-page-sidebar">
            <div class="weforms-settings-page-sidebar-content">
                <h2>Upgrade to <br><strong style="color:#57AB64">weForms Pro</strong></h2>

                <ul class="weforms-pro-features">
                    <li><span class="dashicons dashicons-yes"></span> Integration with email marketing solutions such as Aweber, GetResponse, ConvertKit etc.</li>
                    <li><span class="dashicons dashicons-yes"></span> Connect with productivity tools such as Google Analytics, Zapier, Trello, Google Sheets.</li>
                    <li><span class="dashicons dashicons-yes"></span> Manage payments directly from your forms with PayPal & Stripe.</li>
                    <li><span class="dashicons dashicons-yes"></span> Integrate with popular CRM tools such as Zoho, Salesforce, HubSpot and better manage your relationship with your customers.</li>
                    <li><span class="dashicons dashicons-yes"></span> Create quiz forms, calculate numbers directly in your form, set geolocation and more in weForms Pro.</li>
                </ul>

                <a href="https://weformspro.com/pricing" target="_blank" class="button button-primary">Get weForms Pro</a>
            </div>
        </div>

    <?php } ?>
</div>
</script>
