<div>

    <div class="tablenav top">
        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} items</span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text">Current Page</span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> of <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox">
                </td>
                <th class="col-entry-id">ID</th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <input type="checkbox">
                </td>
                <th class="col-entry-id">ID</th>
                <th v-for="(header, index) in columns">{{ header }}</th>
                <th class="col-entry-details">Actions</th>
            </tr>
        </tfoot>
        <tbody>
            <tr v-if="loading">
                <td v-bind:colspan="columnLength + 3">Loading...</td>
            </tr>
            <tr v-if="!rows.length && !loading">
                <td v-bind:colspan="columnLength + 3">No entries found!</td>
            </tr>
            <tr v-for="(entry, index) in rows">
                <th scope="row" class="check-column">
                    <input type="checkbox" name="post[]" value="">
                </th>
                <th class="col-entry-id">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">#{{ entry.id }}</router-link>
                </th>
                <td v-for="(header, index) in columns">{{ entry.fields[index] }}</td>
                <th class="col-entry-details">
                    <router-link :to="{ name: 'formEntriesSingle', params: { entryid: entry.id }}">Details</router-link>
                </th>
            </tr>
        </tbody>
    </table>

    <div class="tablenav bottom">
        <div class="tablenav-pages">

            <span v-if="totalItems" class="displaying-num">{{ totalItems }} items</span>

            <span class="pagination-links">
                <span v-if="isFirstPage()" class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <a v-else class="first-page" href="#" @click.prevent="goFirstPage()"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>

                <span v-if="currentPage == 1" class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <a v-else class="prev-page" href="#" @click.prevent="goToPage('prev')"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>

                <span class="screen-reader-text">Current Page</span><input @keydown.enter.prevent="goToPage(pageNumberInput)" class="current-page" id="current-page-selector" v-model="pageNumberInput" type="text" value="1" size="1" aria-describedby="table-paging"> of <span class="total-pages">{{ totalPage }}</span>

                <span v-if="currentPage == totalPage" class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <a v-else class="next-page" href="#" @click.prevent="goToPage('next')"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>

                <span v-if="isLastPage()" class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <a v-else class="last-page" href="#" @click.prevent="goLastPage()"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
            </span>
        </div>
    </div>
</div>