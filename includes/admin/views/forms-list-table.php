<div class="wrap">
    <h2>
        <?php
            _e( 'Contact Forms', 'wpuf-contact-form' );

            if ( current_user_can( wpuf_admin_role() ) ):
            ?>
                <a href="<?php echo $add_new_page_url; ?>" id="new-wpuf-post-form" class="page-title-action"><?php _e( 'Add Form', 'wpuf-contact-form' ); ?></a>
            <?php
            endif;
        ?>
    </h2>


    <div class="list-table-wrap wpuf-post-form-wrap">
        <div class="list-table-inner wpuf-post-form-wrap-inner">

            <form method="get">
                <input type="hidden" name="page" value="wpuf-post-forms">
                <?php
                    $list_table = new WPUF_Contact_Form_Admin_Forms_List_Table();
                    $list_table->prepare_items();
                    $list_table->search_box( __( 'Search Forms', 'wpuf-contact-form' ), 'wpuf-post-form-search' );

                    if ( current_user_can( wpuf_admin_role() ) ) {
                        $list_table->views();
                    }

                    $list_table->display();
                ?>
            </form>

        </div><!-- .list-table-inner -->
    </div><!-- .list-table-wrap -->

</div>
