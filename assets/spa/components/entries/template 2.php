<div class="wpuf-contact-form-entries">
    <div>
        <h1 class="wp-heading-inline">
            <?php _e( 'Entries', 'weforms' ); ?>
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
