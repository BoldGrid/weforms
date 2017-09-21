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
</div>