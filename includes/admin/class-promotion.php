<?php

/**
 * Promotional offer class
 */

class WeForms_Admin_Promotion {

    public function __construct() {
        add_action( 'admin_notices', array( $this, 'promotional_offer' ) );
        add_action( 'admin_notices', array( $this, 'weforms_review_notice_message' ) );
        add_action( 'wp_ajax_weforms-dismiss-promotional-offer-notice', array( $this, 'dismiss_promotional_offer' ) );
        add_action( 'wp_ajax_weforms-dismiss-review-notice', array( $this, 'dismiss_review_notice' ) );
    }

    /**
     * Promotional offer notice
     *
     * @return void
     * @since 1.2.6
     *
     */
    public function promotional_offer() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! isset( $_GET['page'] ) ) {
            return;
        }

        if ( class_exists( 'WPUF_Admin_Promotion' ) ) {
            return;
        }

        // check if it has already been dismissed
        $offer_key        = 'weforms_promotional_offer_notice';
        $offer_start_date = strtotime( '2019-11-26 00:00:01' );
        $offer_end_date   = strtotime( '2019-12-04 23:59:00' );
        $hide_notice      = get_option( $offer_key, 'show' );

        if ( 'hide' == $hide_notice ) {
            return;
        }

        if ( $offer_start_date < current_time( 'timestamp' ) && current_time( 'timestamp' ) < $offer_end_date ) {
            ?>
            <div class="notice notice-success is-dismissible" id="weforms-bfcm-notice">
                <div class="logo">
                    <img src="<?php echo WEFORMS_ASSET_URI . '/images/promo-logo.png' ?>" alt="weForms">
                </div>
                <div class="content">
                    <h3><span class="highlight-red">Black Friday</span> &amp; <span
                            class="highlight-blue">Cyber Monday</span></h3>
                    <p>Don't miss out on the biggest sale of the year on <span
                            class="highlight-red">weForms</span></p>
                    <div class="coupon-box">
                        <div class="highlight-red">Use this coupon</div>
                        <div class="highlight-code">BFCM2019</div>
                    </div>
                </div>
                <div class="call-to-action">
                    <a href="https://wedevs.com/weforms/pricing?utm_campaign=black_friday_&_cyber_monday&utm_medium=banner&utm_source=plugin_dashboard">Save
                        33%</a>
                    <p>Valid till 4th December.</p>
                </div>
            </div>

            <style>
                #weforms-bfcm-notice {
                    font-size: 14px;
                    border-left: none;
                    background: #000;
                    color: #fff;
                    display: flex
                }

                #weforms-bfcm-notice .logo {
                    text-align: center;
                    text-align: center;
                    margin: 13px 30px 5px 15px;
                }

                #weforms-bfcm-notice .logo img {
                    width: 80%;
                }

                #weforms-bfcm-notice .highlight-red {
                    color: #FF0000;
                }

                #weforms-bfcm-notice .highlight-blue {
                    color: #48ABFF;
                }

                #weforms-bfcm-notice .content {
                    margin-top: 5px;
                }

                #weforms-bfcm-notice .content h3 {
                    color: #FFF;
                    margin: 12px 0px 5px;
                    font-weight: normal;
                    font-size: 20px;
                }

                #weforms-bfcm-notice .content p {
                    margin: 0px 0px;
                    padding: 0px;
                    letter-spacing: 0.4px;
                }

                #weforms-bfcm-notice .coupon-box {
                    margin-top: 10px;
                    display: flex;
                    align-items: center;
                    font-size: 17px;
                }

                #weforms-bfcm-notice .coupon-box .highlight-code {
                    margin-left: 15px;
                    border: 1px dashed;
                    padding: 4px 10px;
                    border-radius: 15px;
                    letter-spacing: 1px;
                    background: #1E1B1B;

                    -webkit-user-select: all;
                    -moz-user-select: all;
                    -ms-user-select: all;
                    user-select: all;
                }

                #weforms-bfcm-notice .call-to-action {
                    margin-left: 8%;
                    margin-top: 25px;
                }

                #weforms-bfcm-notice .call-to-action a {
                    border: none;
                    background: #FF0000;
                    padding: 8px 15px;
                    font-size: 15px;
                    color: #fff;
                    border-radius: 20px;
                    text-decoration: none;
                    display: block;
                    text-align: center;
                }

                #weforms-bfcm-notice .call-to-action p {
                    font-size: 12px;
                    margin-top: 1px;
                }
            </style>

            <script type='text/javascript'>
                jQuery('body').on('click', '#weforms-bfcm-notice .notice-dismiss', function (e) {
                    e.preventDefault();

                    wp.ajax.post('weforms-dismiss-promotional-offer-notice', {
                        dismissed: true
                    });
                });
            </script>
            <?php
        }
    }

    /**
     *
     * @return void
     **@since 1.3.5
     *
     */
    public function weforms_review_notice_message() {
        // Show only to Admins
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $dismiss_notice  = get_option( 'weforms_review_notice_dismiss', 'no' );
        $activation_time = get_option( 'weforms_installed' );
        $total_entries   = weforms_count_entries();

        $args = array(
            'order'   => 'DESC',
            'orderby' => 'post_date'
        );

        $contact_forms  = weforms()->form->get_forms( $args );
        $form_published = count( $contact_forms['forms'] );

        // check if it has already been dismissed
        // and don't show notice in 45 days of installation, 3888000 = 45 Days in seconds
        if ( 'yes' == $dismiss_notice ) {
            return;
        }

        if (
            ( time() - $activation_time < 3888000 )
            && $total_entries < 25
            && $form_published < 3
        ) {
            return;
        }

        ?>
        <div id="weforms-review-notice" class="weforms-review-notice">
            <div class="weforms-review-thumbnail">
                <img src="<?php echo WEFORMS_ASSET_URI . '/images/icon-weforms.png' ?>" alt="">
            </div>
            <div class="weforms-review-text">
                <?php if ( $total_entries >= 25 ) : ?>
                    <h3><?php _e( 'Enjoying <strong>weForms</strong>?', 'weforms' ) ?></h3>
                    <p><?php _e( 'Seems like you are getting a good response using <strong>weForms</strong>. Would you please show us a little love by rating us in the <a href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><strong>WordPress.org</strong></a>?', 'weforms' ) ?></p>
                <?php else: ?>
                    <h3><?php _e( 'Enjoying <strong>weForms</strong>?', 'weforms' ) ?></h3>
                    <p><?php _e( 'Hope that you had a neat and snappy experience with the tool. Would you please show us a little love by rating us in the <a href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><strong>WordPress.org</strong></a>?', 'weforms' ) ?></p>
                <?php endif; ?>

                <ul class="weforms-review-ul">
                    <li><a href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><span
                                class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'weforms' ) ?>
                        </a></li>
                    <li><a href="#" class="notice-dismiss"><span
                                class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'weforms' ) ?>
                        </a></li>
                    <li><a href="#" class="notice-dismiss"><span
                                class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'weforms' ) ?>
                        </a></li>
                </ul>
            </div>
        </div>
        <style type="text/css">
            #weforms-review-notice .notice-dismiss {
                padding: 0 0 0 26px;
            }

            #weforms-review-notice .notice-dismiss:before {
                display: none;
            }

            #weforms-review-notice.weforms-review-notice {
                padding: 15px 15px 15px 0;
                background-color: #fff;
                border-radius: 3px;
                margin: 20px 20px 0 0;
                border-left: 4px solid transparent;
            }

            #weforms-review-notice .weforms-review-thumbnail {
                width: 114px;
                float: left;
                line-height: 80px;
                text-align: center;
                border-right: 4px solid transparent;
            }

            #weforms-review-notice .weforms-review-thumbnail img {
                width: 60px;
                vertical-align: middle;
            }

            #weforms-review-notice .weforms-review-text {
                overflow: hidden;
            }

            #weforms-review-notice .weforms-review-text h3 {
                font-size: 24px;
                margin: 0 0 5px;
                font-weight: 400;
                line-height: 1.3;
            }

            #weforms-review-notice .weforms-review-text p {
                font-size: 13px;
                margin: 0 0 5px;
            }

            #weforms-review-notice .weforms-review-ul {
                margin: 0;
                padding: 0;
            }

            #weforms-review-notice .weforms-review-ul li {
                display: inline-block;
                margin-right: 15px;
            }

            #weforms-review-notice .weforms-review-ul li a {
                display: inline-block;
                color: #82C776;
                text-decoration: none;
                padding-left: 26px;
                position: relative;
            }

            #weforms-review-notice .weforms-review-ul li a span {
                position: absolute;
                left: 0;
                top: -2px;
            }
        </style>
        <script type='text/javascript'>
            jQuery('body').on('click', '#weforms-review-notice .notice-dismiss', function (e) {
                e.preventDefault();
                jQuery("#weforms-review-notice").hide();

                wp.ajax.post('weforms-dismiss-review-notice', {
                    dismissed: true
                });
            });
        </script>
        <?php

    }


    /**
     * Dismiss promotion notice
     *
     * @return void
     * @since  1.2.6
     *
     */
    public function dismiss_promotional_offer() {
        if ( ! empty( $_POST['dismissed'] ) ) {
            $offer_key = 'weforms_promotional_offer_notice';
            update_option( $offer_key, 'hide' );
        }
    }

    /**
     * Dismiss review notice
     *
     * @return void
     **@since  1.3.5
     *
     */
    public function dismiss_review_notice() {
        if ( ! empty( $_POST['dismissed'] ) ) {
            update_option( 'weforms_review_notice_dismiss', 'yes' );
        }
    }
}
