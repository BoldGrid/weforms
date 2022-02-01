<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Adds weForms block
 */
class weForms_FormBlock {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        //add_action( 'weforms_loaded', array($this, 'weforms_block_load' ) );
        // wait for Gutenberg to enqueue it's block assets
        add_action( 'enqueue_block_editor_assets', [ $this, 'weforms_form_block' ] );
        // load the preview information and form
        add_action( 'wp_head', [ $this, 'load_preview_data' ] );
    }

    public function weforms_form_block() {
        $js_dir  = WEFORMS_ASSET_URI . '/js/';
        $css_dir = WEFORMS_ASSET_URI . '/css/';

        // Once we have Gutenberg block javascript, we can enqueue our assets
        wp_register_script(
            'weforms-forms-block',
            $js_dir . 'gutenblock.js',
            [ 'wp-blocks', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-element', 'underscore' ]
        );

        wp_register_style(
            'weforms-forms-block-style',
            $css_dir . 'gutenblock.css',
            [ 'wp-edit-blocks' ]
        );
        wp_register_style(
            'weforms-forms-block-editor',
            $css_dir . 'gutenblock-editor.css',
            [ 'wp-edit-blocks', 'form-blocks-style' ]
        );

        /*
         * we need to get our forms so that the block can build a dropdown
         * with the forms
         * */
        wp_enqueue_script( 'weforms-forms-block' );

        $forms      = [];
        $all_forms  = weforms()->form->all();

        foreach ( $all_forms['forms'] as $form ) {
            $forms[] =  [
                'value' => $form->id,
                'label' => $form->name,
            ];
        }

        $block_logo      = WEFORMS_ASSET_URI . '/images/icon-weforms.png';
        $thumbnail_logo  = WEFORMS_ASSET_URI . '/images/icon-weforms.png';

        wp_localize_script( 'weforms-forms-block', 'weFormsBlock', array(
            'forms'           => $forms,
            'siteUrl'         => get_home_url(),
            'block_logo'      => $block_logo,
            'thumbnail_logo'  => $thumbnail_logo,
        ) );
        wp_enqueue_style( 'weforms-forms-block-style' );
        wp_enqueue_style( 'weforms-forms-block-editor' );
    }

    public function load_preview_data() {
        $js_dir  = WEFORMS_ASSET_URI . '/js/';

        // check for preview and iframe get parameters
        if ( isset( $_GET[ 'weforms_preview' ] ) && isset( $_GET[ 'weforms_iframe' ] ) ) {
            $form_id = intval( $_GET[ 'weforms_preview' ] );
            // Style below: update width and height for particular form?>
            <style media="screen">
                #wpadminbar {
                    display: none;
                }
                header,
                footer{
                    display: none;
                }

                .wpuf-form-add {
                    z-index: 9001;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background-color: white;
                    display: block !important;
                }

            </style>
            <?php

            // register our script to target the form iFrame in page builder
            wp_register_script(
                'weforms-block-setup',
                $js_dir . 'blockFrameSetup.js',
                [ 'underscore', 'jquery' ]
              );

            wp_localize_script( 'weforms-block-setup', 'weFormsBlockSetup', [
                'form_id' => $form_id,
            ] );

            wp_enqueue_script( 'weforms-block-setup' );
        }
    }
}
