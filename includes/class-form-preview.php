<?php

/**
 * The preview class
 *
 * This is a clever technique to preview a form without having to set any placeholder page by
 * setting the page templates to singular pages (page.php, single.php and index.php). Setting
 * the posts per page to 1 and changing the page title and contents dynamiclly allows us to
 * preview the form without any placeholder page.
 *
 * This technique requires the theme to have at least the above mentioned templates in the theme
 * and requires to have the WordPress Loop, otherwise we wouldn't be able to set the title and
 * the page content dynamically.
 *
 * The technique is borrowed from Ninja Forms (thanks guys!)
 */
class WeForms_Form_Preview {

    /**
     * Form id
     *
     * @var integer
     */
    private $form_id;

    function __construct() {

        if ( ! isset( $_GET['weforms_preview'] ) ) {
            return;
        }

        $this->form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;

        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
        add_filter( 'template_include', array( $this, 'template_include' ) );

        add_filter( 'the_title', array( $this, 'the_title' ) );
        add_filter( 'the_content', array( $this, 'the_content' ) );
        add_filter( 'get_the_excerpt', array( $this, 'the_content' ) );
        add_filter( 'post_thumbnail_html', '__return_empty_string' );
    }

    /**
     * Set the page title
     *
     * @param  string $title
     *
     * @return string
     */
    public function the_title( $title ) {
        if ( ! in_the_loop() ) {
            return $title;
        }

        $form = weform_get_form( $this->form_id );

        if ( ! $form ) {
            return $title;
        }

        return $form->post_title . ' ' . __( 'Preview', 'weforms' );
    }

    /**
     * Set the content of the page
     *
     * @param  string $content
     *
     * @return string
     */
    public function the_content( $content ) {

        if ( ! is_user_logged_in() ) {
            return __( 'You must be logged in to preview this form.', 'weforms' );
        }

        $viewing_capability = apply_filters( 'weforms_preview_form_cap', 'edit_posts' ); // at least has to be contributor

        if ( ! current_user_can( $viewing_capability ) ) {
            return __( 'Sorry, you are not eligible to preview this form.', 'weforms' );
        }

        return do_shortcode( sprintf( '[weforms id="%d"]', $this->form_id ) );
    }

    /**
     * Set the posts to one
     *
     * @param  WP_Query $query
     *
     * @return void
     */
    public function pre_get_posts( $query ) {
        if ( $query->is_main_query() ) {
            $query->set( 'posts_per_page', 1 );
        }
    }

    /**
     * Limit the page templates to singular pages only
     *
     * @return string
     */
    function template_include() {
        return locate_template( array( 'page.php', 'single.php', 'index.php' ) );
    }
}
