<?php

/**
 * Contact form class
 */
class WPUF_Contact_Form_Builder {

    private $form_type = 'contact_form';

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );

        add_action( 'wpuf-form-builder-tabs-' . $this->form_type, array( $this, 'add_primary_tabs' ) );
        add_action( 'wpuf-form-builder-settings-tabs-' . $this->form_type, array( $this, 'add_settings_tabs' ) );
        add_action( 'wpuf-form-builder-settings-tab-contents-' . $this->form_type, array( $this, 'add_settings_tab_contents' ) );
    }

    /**
     * Register form post types
     *
     * @return void
     */
    public function register_post_type() {
        $capability = wpuf_admin_role();

        register_post_type( 'wpuf_contact_form', array(
            'label'           => __( 'Contact Forms', 'wpuf-contact-form' ),
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => false,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'query_var'       => false,
            'supports'        => array('title'),
            'capabilities' => array(
                'publish_posts'       => $capability,
                'edit_posts'          => $capability,
                'edit_others_posts'   => $capability,
                'delete_posts'        => $capability,
                'delete_others_posts' => $capability,
                'read_private_posts'  => $capability,
                'edit_post'           => $capability,
                'delete_post'         => $capability,
                'read_post'           => $capability,
            ),
            'labels' => array(
                'name'               => __( 'Forms', 'wpuf-contact-form' ),
                'singular_name'      => __( 'Form', 'wpuf-contact-form' ),
                'menu_name'          => __( 'Contact Forms', 'wpuf-contact-form' ),
                'add_new'            => __( 'Add Form', 'wpuf-contact-form' ),
                'add_new_item'       => __( 'Add New Form', 'wpuf-contact-form' ),
                'edit'               => __( 'Edit', 'wpuf-contact-form' ),
                'edit_item'          => __( 'Edit Form', 'wpuf-contact-form' ),
                'new_item'           => __( 'New Form', 'wpuf-contact-form' ),
                'view'               => __( 'View Form', 'wpuf-contact-form' ),
                'view_item'          => __( 'View Form', 'wpuf-contact-form' ),
                'search_items'       => __( 'Search Form', 'wpuf-contact-form' ),
                'not_found'          => __( 'No Form Found', 'wpuf-contact-form' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'wpuf-contact-form' ),
                'parent'             => __( 'Parent Form', 'wpuf-contact-form' ),
            ),
        ) );
    }

    /**
     * Additional primary tabs
     *
     * @return void
     */
    public function add_primary_tabs() {
        $tabs = apply_filters( 'wpuf_contact_form_editor_tabs', array(
            'notification' => __( 'Notifications', 'wpuf-contact-form' ),
            'integration'  => __( 'Integrations', 'wpuf-contact-form' )
        ) );

        foreach ($tabs as $key => $label) {
            ?>
            <a href="#wpuf-form-builder-<?php echo $key; ?>" class="nav-tab"><?php echo $label; ?></a>
            <?php
        }
    }

    /**
     * Add settings tabs
     *
     * @return void
     */
    public function add_settings_tabs() {
        ?>

            <a href="#wpuf-metabox-settings" class="nav-tab"><?php _e( 'Form Settings', 'wpuf-contact-form' ); ?></a>

        <?php
    }

    /**
     * Add settings tabs
     *
     * @return void
     */
    public function add_settings_tab_contents() {
        ?>

            <div id="wpuf-metabox-settings" class="group">
                <?php $this->form_settings(); ?>
            </div>

            <?php do_action( 'wpuf_contact_form_settings_tab_content' ); ?>

        <?php
    }

    public function form_settings() {
        global $post;

        $form_settings = wpuf_get_form_settings( $post->ID );

        $redirect_to           = isset( $form_settings['redirect_to'] ) ? $form_settings['redirect_to'] : 'same';
        $message               = isset( $form_settings['message'] ) ? $form_settings['message'] : __( 'Post saved', 'wpuf-contact-form' );
        $update_message        = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'wpuf-contact-form' );
        $page_id               = isset( $form_settings['page_id'] ) ? $form_settings['page_id'] : 0;
        $url                   = isset( $form_settings['url'] ) ? $form_settings['url'] : '';

        $submit_text           = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( 'Submit', 'wpuf-contact-form' );

        ?>
            <table class="form-table">
                <tr class="wpuf-redirect-to">
                    <th><?php _e( 'Redirect To', 'wpuf-contact-form' ); ?></th>
                    <td>
                        <select name="wpuf_settings[redirect_to]">
                            <?php
                            $redirect_options = array(
                                'same' => __( 'Same Page', 'wpuf-contact-form' ),
                                'page' => __( 'To a page', 'wpuf-contact-form' ),
                                'url'  => __( 'To a custom URL', 'wpuf-contact-form' )
                            );

                            foreach ($redirect_options as $to => $label) {
                                printf('<option value="%s"%s>%s</option>', $to, selected( $redirect_to, $to, false ), $label );
                            }
                            ?>
                        </select>
                        <p class="description">
                            <?php _e( 'After successfull submit, where the page will redirect to', $domain = 'default' ) ?>
                        </p>
                    </td>
                </tr>

                <tr class="wpuf-same-page">
                    <th><?php _e( 'Message to show', 'wpuf-contact-form' ); ?></th>
                    <td>
                        <textarea rows="3" cols="40" name="wpuf_settings[message]"><?php echo esc_textarea( $message ); ?></textarea>
                    </td>
                </tr>

                <tr class="wpuf-page-id">
                    <th><?php _e( 'Page', 'wpuf-contact-form' ); ?></th>
                    <td>
                        <select name="wpuf_settings[page_id]">
                            <?php
                            $pages = get_posts(  array( 'numberposts' => -1, 'post_type' => 'page') );

                            foreach ($pages as $page) {
                                printf('<option value="%s"%s>%s</option>', $page->ID, selected( $page_id, $page->ID, false ), esc_attr( $page->post_title ) );
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr class="wpuf-url">
                    <th><?php _e( 'Custom URL', 'wpuf-contact-form' ); ?></th>
                    <td>
                        <input type="url" name="wpuf_settings[url]" value="<?php echo esc_attr( $url ); ?>">
                    </td>
                </tr>

                <tr class="wpuf-submit-text">
                    <th><?php _e( 'Submit Button text', 'wpuf-contact-form' ); ?></th>
                    <td>
                        <input type="text" name="wpuf_settings[submit_text]" value="<?php echo esc_attr( $submit_text ); ?>">
                    </td>
                </tr>
            </table>
        <?php
    }
}
