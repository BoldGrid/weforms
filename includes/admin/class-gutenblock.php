<?php

if ( ! defined( 'ABSPATH' ) )
    exit;
/**
 * Adds weForms Forms widget.
 */
class weForms_FormBlock {

    public function __construct() {
        // load the preview information and form
        add_action( 'wp_head', array( $this, 'load_preview_data' ) );
        add_action( 'enqueue_block_editor_assets', array ( $this, 'weform_block' ) );
    }

    function weform_block() {
        $js_dir  = WEFORMS_ASSET_URI . '/js/';
        $css_dir = WEFORMS_ASSET_URI . '/css/';

        $block_logo = $thumbnail_logo = WEFORMS_ASSET_URI . '/images/icon-weforms.png';
        // $thumbnail_logo = WEFORMS_ASSET_URI . 'images/icon-weforms.png';

        // Once we have Gutenberg block javascript, we can enqueue our assets
        wp_register_script(
            'weforms-block',
            $js_dir . 'gutenblock.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'underscore' )
        );

         wp_register_style(
             'weforms-block-editor',
             $css_dir . 'gutenblock.css'
         );

        /**
         * we need to get our forms so that the block can build a dropdown
         * with the forms
         * */
        wp_enqueue_script( 'weforms-block' );
        wp_enqueue_style( 'weforms-block-editor' );

        $forms = array();
        $forms[] = array (
            'value' => '',
            'label' => '-- Select a Form --',
        );

        $all_forms = weforms()->form->all();

        foreach ( $all_forms['forms'] as $form ) {
            $forms[] = array (
                'value' => $form->id,
                'label' => $form->name,
            );
        }

        wp_localize_script( 'weforms-block', 'weformsblock', array(
            'forms'          => $forms,
            'siteUrl'        => get_site_url(),
            'block_logo'     => $block_logo,
            'thumbnail_logo' => $thumbnail_logo
        ) );
    }

    public function load_preview_data() {
        // check for preview and iframe get parameters
        if ( isset( $_GET[ 'weforms_preview' ] ) && isset( $_GET[ 'weforms_iframe' ] ) ) {
            ?>
            <style media="screen">
                #wpadminbar {
                    display: none;
                }
                header{
                    display: none;
                }
                .wpuf-form-add {
                    z-index: 9001;
                    position: fixed;
                    top: 0; left: 0;
                    width: 100vw;
                    height: 100vh;
                    background-color: white;
                    /* overflow-x: hidden; */
                }
            </style>
            <script type="text/javascript">
                jQuery( document ).ready( function() {
                    var frameEl = window.frameElement;
                    // get the form element
                    var $form = jQuery('.wpuf-form-add');
                    // get the height of the form
                    var height = $form.find( '.wpuf-form' ).outerHeight(true);

                    if ( frameEl ) {
                        frameEl.height = height + 50;
                    }
                });
            </script>
            <?php
        }
    }
}
