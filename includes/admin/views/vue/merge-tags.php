<div class="merge-tag-wrap">
    <a href="#" v-on:click.prevent="toggleFields($event)" class="merge-tag-link" title="<?php echo esc_attr( 'Click to toggle merge tags', 'best-contact-form' ); ?>"><span class="dashicons dashicons-editor-code"></span></a>

    <!-- <pre>{{ form_fields }}</pre> -->

    <div class="merge-tags">
        <div class="merge-tag-section">
            <div class="merge-tag-head">Form Fields</div>

            <ul>
                <li v-for="field in form_fields">
                    <a href="#" v-on:click.prevent="insertField('field', field.name);">{{ field.label }}</a>
                </li >
            </ul>
        </div><!-- .merge-tag-section -->

        <?php
        $merge_tags = wpuf_cf_get_merge_tags();

        foreach ($merge_tags as $section_key => $section) {
            ?>

            <div class="merge-tag-section">
                <div class="merge-tag-head"><?php echo $section['title'] ?></div>

                <ul>
                    <?php foreach ($section['tags'] as $key => $value) { ?>
                        <li>
                            <a href="#" v-on:click.prevent="insertField('<?php echo $key; ?>');"><?php echo $value; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div><!-- .merge-tag-section -->

            <?php
        }
        ?>
    </div><!-- .merge-tags -->
</div>