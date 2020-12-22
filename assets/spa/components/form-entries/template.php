<div class="wpuf-contact-form-entries">
    <div>
        <h1 class="wp-heading-inline">
            <?php _e( 'Entries', 'weforms' ); ?>
            <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 5px;"></span>

            <span style="color: #999;" class="form-name">
                {{ form_title }}
            </span>

            <router-link class="page-title-action" to="/"><?php _e( 'Back to forms', 'weforms' ); ?></router-link>
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
