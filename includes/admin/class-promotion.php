<?php

/**
 * Promotional offer class
 */

class WeForms_Admin_Promotion {

    public function __construct() {
        add_action( 'admin_notices', array( $this, 'promotional_offer' ) );
        add_action( 'wp_ajax_weforms-dismiss-promotional-offer-notice', array( $this, 'dismiss_promotional_offer' ) );
    }

    /**
     * Promotional offer notice
     *
     * @since 1.2.6
     *
     * @return void
     */
    public function promotional_offer() {
        // Show only to Admins
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 2018-03-26 23:59:00
        if ( time() > 1543276740 ) {
            return;
        }

        // check if wpuf is showing banner
        if ( class_exists( 'WPUF_Admin_Promotion' ) ) {
            return;
        }

        // check if it has already been dismissed
        $hide_notice = get_option( 'weforms_promotional_offer_notice', 'no' );

        if ( 'hide' == $hide_notice ) {
            return;
        }

        // $product_text = (  weforms()->is_pro() ) ? __( 'Pro upgrade and all extensions, ', 'weforms' ) : __( 'all extensions, ', 'weforms' );

        // $offer_msg  = __( '<h2><span class="dashicons dashicons-awards"></span> weDevs 5th Birthday Offer</h2>', 'weforms' );
        $offer_msg  = __( '<p>
                                        <strong class="highlight-text" style="font-size: 18px">33&#37; flat discount on all our products</strong><br>
                                        Save money this holiday season while supercharging your WordPress site with plugins that were made to empower you.
                                        <br>
                                        Offer ending soon!
                                    </p>', 'weforms' );

        ?>
            <div class="notice is-dismissible" id="weforms-promotional-offer-notice">
                <table>
                    <tbody>
                        <tr>
                            <td class="image-container">
                                <img src="https://ps.w.org/weforms/assets/icon-256x256.png" alt="">
                            </td>
                            <td class="message-container">
                                <?php echo $offer_msg; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <span class="dashicons dashicons-megaphone"></span>
                <a href="https://wedevs.com/coupons/?utm_campaign=black_friday_cyber_monday&utm_medium=banner&utm_source=inside_plugin" class="button button-primary promo-btn" target="_blank"><?php _e( 'Get the Offer', 'weforms' ); ?></a>
            </div><!-- #weforms-promotional-offer-notice -->

            <style>
                #weforms-promotional-offer-notice {
                    background-image: linear-gradient(35deg,#00c9ff 0%, #92fe9d 100%) !important;
                    border: 0px;
                    padding: 0;
                    opacity: 0;
                }

                .wrap > #weforms-promotional-offer-notice {
                    opacity: 1;
                }

                #weforms-promotional-offer-notice table {
                    border-collapse: collapse;
                    width: 100%;
                }

                #weforms-promotional-offer-notice table td {
                    padding: 0;
                }

                #weforms-promotional-offer-notice table td.image-container {
                    background-color: #ebf0f4;
                    vertical-align: middle;
                    width: 95px;
                }

                #weforms-promotional-offer-notice img {
                    max-width: 100%;
                    max-height: 100px;
                    vertical-align: middle;
                }

                #weforms-promotional-offer-notice table td.message-container {
                    padding: 0 10px;
                }

                #weforms-promotional-offer-notice h2{
                    color: rgba(250, 250, 250, 0.77);
                    margin-bottom: 10px;
                    font-weight: normal;
                    margin: 16px 0 14px;
                    -webkit-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    -moz-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    -o-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                }

                #weforms-promotional-offer-notice h2 span {
                    position: relative;
                    top: 0;
                }

                #weforms-promotional-offer-notice p{
                    color: rgba(250, 250, 250, 0.77);
                    font-size: 14px;
                    margin-bottom: 10px;
                    -webkit-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    -moz-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    -o-text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                    text-shadow: 0.1px 0.1px 0px rgba(250, 250, 250, 0.24);
                }

                #weforms-promotional-offer-notice p strong.highlight-text{
                    color: #fff;
                }

                #weforms-promotional-offer-notice p a {
                    color: #fafafa;
                }

                #weforms-promotional-offer-notice .notice-dismiss:before {
                    color: #fff;
                }

                #weforms-promotional-offer-notice span.dashicons-megaphone {
                    position: absolute;
                    bottom: 46px;
                    right: 248px;
                    color: rgba(253, 253, 253, 0.29);
                    font-size: 96px;
                    transform: rotate(-21deg);
                }

                #weforms-promotional-offer-notice a.promo-btn{
                    background: #149269;
                    border-color: #149269 #149269 #149269;
                    box-shadow: 0 1px 0 #149269;
                    /*color: #45E2D0;*/
                    text-decoration: none;
                    text-shadow: none;
                    position: absolute;
                    top: 30px;
                    right: 26px;
                    height: 40px;
                    line-height: 40px;
                    width: 130px;
                    text-align: center;
                    font-weight: 600;
                }

            </style>

            <script type='text/javascript'>
                jQuery('body').on('click', '#weforms-promotional-offer-notice .notice-dismiss', function(e) {
                    e.preventDefault();

                    wp.ajax.post('weforms-dismiss-promotional-offer-notice', {
                        dismissed: true
                    });
                });
            </script>
        <?php
    }


   /**
    * Dismiss promotion notice
    *
    * @since  1.2.6
    *
    * @return void
    */
   public function dismiss_promotional_offer() {
        if ( ! empty( $_POST['dismissed'] ) ) {
            $offer_key = 'weforms_promotional_offer_notice';
            update_option( $offer_key, 'hide' );
        }
    }
}