<?php

/**
 * Promotional offer class
 */
class WeForms_Admin_Promotion {

    public function __construct() {
        add_action( 'admin_notices', [ $this, 'promotional_offer' ] );
        add_action( 'admin_notices', [ $this, 'weforms_review_notice_message' ] );
        add_action( 'wp_ajax_weforms-dismiss-promotional-offer-notice', [ $this, 'dismiss_promotional_offer' ] );
        add_action( 'wp_ajax_weforms-dismiss-review-notice', [ $this, 'dismiss_review_notice' ] );
    }

    /**
     * Promotional offer notice
     *
     * @return void
     *
     * @since 1.2.6
     */
    public function promotional_offer() {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( class_exists( 'WeForms_Pro' ) ) {
            return;
        }

        // check if it has already been dismissed
        $offer_key        = 'weforms_promotional_offer_notice';
        $offer_start_date = strtotime( '2019-11-20 00:00:01' );
        $offer_end_date   = strtotime( '2019-12-04 23:59:00' );
        $hide_notice      = get_option( $offer_key, 'show' );

        if ( 'hide' == $hide_notice ) {
            return;
        }

        if ( $offer_start_date < current_time( 'timestamp' ) && current_time( 'timestamp' ) < $offer_end_date ) {
            ?>
            <div class="notice notice-success is-dismissible" id="weforms-bfcm-notice">
                <div class="logo">
                    <img src="<?php echo  esc_attr( WEFORMS_ASSET_URI ) . '/images/promo-logo.png' ?>" alt="weForms">
                </div>
                <div class="content">
                    <p>Biggest Sale of the year on this</p>

                    <h3><span class="highlight-green"> Black Friday &amp; </span>Cyber Monday</h3>
                    <p>Claim your discount on <span class="highlight-olive">weForms</span> till 4th December</p>
                </div>
                <div class="call-to-action">
                    <a target="_blank" href="https://wedevs.com/weforms/pricing?utm_campaign=black_friday_&_cyber_monday&utm_medium=banner&utm_source=plugin_dashboard">
                        <img src="<?php echo esc_attr( WEFORMS_ASSET_URI ) . '/images/promo-btn.png' ?>" alt="Btn">
                    </a>
                    <p>
                        <span class="highlight-green">Coupon: </span>
                        <span class="coupon-code">BFCM2019</span>
                    </p>
                </div>
            </div>

            <style>
                #weforms-bfcm-notice {
                    font-size: 14px;
                    border-left: none;
                    background: #398085;
                    color: #fff;
                    display: flex
                }

                #weforms-bfcm-notice .notice-dismiss:before {
                    color: #76E5FF;
                }

                #weforms-bfcm-notice .notice-dismiss:hover:before {
                    color: #b71c1c;
                }

                #weforms-bfcm-notice .logo {
                    text-align: center;
                    text-align: center;
                    margin: auto 50px;
                }

                #weforms-bfcm-notice .logo img {
                    width: 80%;
                }

                #weforms-bfcm-notice .highlight-green {
                    color: #72F3FB;
                }

                #weforms-bfcm-notice .highlight-olive {
                    color: #7CF5AA;
                }

                #weforms-bfcm-notice .content {
                    margin-top: 5px;
                }

                #weforms-bfcm-notice .content h3 {
                    color: #FFF;
                    margin: 12px 0 5px;
                    font-weight: normal;
                    font-size: 30px;
                }

                #weforms-bfcm-notice .content p {
                    margin-top: 12px;
                    padding: 0;
                    letter-spacing: .4px;
                    color: #ffffff;
                    font-size: 15px;
                }

                #weforms-bfcm-notice .call-to-action {
                    margin-left: 10%;
                    margin-top: 20px;
                }

                #weforms-bfcm-notice .call-to-action a:focus {
                    box-shadow: none;
                }

                #weforms-bfcm-notice .call-to-action p {
                    font-size: 16px;
                    color: #fff;
                    margin-top: 1px;
                    text-align: center;
                }

                #weforms-bfcm-notice .coupon-code {
                    -moz-user-select: all;
                    -webkit-user-select: all;
                    user-select: all;
                }
            </style>

            <script type='text/javascript'>
                jQuery('body').on('click', '#weforms-bfcm-notice .notice-dismiss', function (e) {
                    e.preventDefault();

                    wp.ajax.post('weforms-dismiss-promotional-offer-notice', {
                        dismissed: true,
                        _wpnonce: '<?php echo esc_attr ( wp_create_nonce( 'weforms' ) ); ?>'
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * @return void
     *
     **@since 1.3.5
     */
    public function weforms_review_notice_message() {
        // Show only to Admins
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }

		$screen = get_current_screen();
		if ( $screen && $screen->base && 'toplevel_page_weforms' !== $screen->base ) {
			return;
		}

        $dismiss_notice  = get_option( 'weforms_review_notice_dismiss', 'no' );
        $activation_time = get_option( 'weforms_installed' );
        $total_entries   = weforms_count_entries();

        $args = [
            'order'   => 'DESC',
            'orderby' => 'post_date',
        ];

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
        } ?>
        <div id="weforms-review-notice" class="weforms-review-notice updated notice">
			<div class="weforms-review-top">
				<p><img src="<?php echo esc_attr( WEFORMS_ASSET_URI ) . '/images/weforms-logo.png'; ?>" alt=""></p>
				<div class="weforms-review-text">
					<?php if ( $total_entries >= 25 ) : ?>
						<h3><?php printf( __( 'Enjoying %sweForms%s?', 'weforms' ), '<strong>', '</strong>' ); ?></h3>
						<p><?php
						printf(
							// translators: opening <a>tag, closing </a> tag.
							__(
								'Seems like you are getting a good response using weForms. Would you please show us a little love by rating us in the %sWordPress.org%s?',
								'weforms'
							),
							'<a href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><strong>',
							'</strong></a>'
						); ?></p>
					<?php else: ?>
						<h3><?php printf(
							// translators: opening <strong> tag, closing </strong> tag.
							__( 'Enjoying %sweForms%s?', 'weforms' ),
							'<strong>',
							'</strong>' ); ?></h3>
						<p><?php
						printf(
							// translators: opening <a> tag, closing </a> tag
							__(
								'Hope that you had a neat and snappy experience with the tool. Would you please show us a little love by rating us in the %sWordPress.org%s?',
								'weforms'
							),
							'<a href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><strong>',
							'</strong></a>'
						); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<div class="weforms-review-links">
				<ul class="weforms-review-ul">
					<li><a class="button-primary button" href="https://wordpress.org/support/plugin/weforms/reviews/#postform" target="_blank"><span
								class="dashicons dashicons-external"></span><?php esc_html_e( 'Sure! I\'d love to!', 'weforms' ) ?>
						</a></li>
					<li><a href="#" class="button notice-dismiss"><span
								class="dashicons dashicons-smiley"></span><?php esc_html_e( 'I\'ve already left a review', 'weforms' ) ?>
						</a></li>
					<li><a href="#" class="button notice-dismiss"><span
								class="dashicons dashicons-dismiss"></span><?php esc_html_e( 'Never show again', 'weforms' ) ?>
						</a>
					</li>
				</ul>
			</div>
        </div>
        <style type="text/css">

            #weforms-review-notice .notice-dismiss:before {
                display: none;
            }

            #weforms-review-notice.weforms-review-notice {
                background-color: #fff;
                border-radius: 3px;
                margin: 5px 0 15px;
            }

			#weforms-review-notice .weforms-review-top {
				display: flex;
				margin: 0.5em 0;
			}

			#weforms-review-notice .weforms-review-top > p {
				padding-right: 15px;
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

			#weforms-review-notice .weforms-review-links {
				margin: 0.5em 0;
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
                display: flex;
				align-items: center;
				justify-content: space-between;
                text-decoration: none;
                position: relative;
            }

            #weforms-review-notice .weforms-review-ul li a span {
				margin-right: 10px;
            }
        </style>
        <script type='text/javascript'>
            jQuery('body').on('click', '#weforms-review-notice .notice-dismiss', function (e) {
                e.preventDefault();
                jQuery("#weforms-review-notice").hide();

                wp.ajax.post('weforms-dismiss-review-notice', {
                    dismissed: true,
                    _wpnonce: '<?php echo esc_attr ( wp_create_nonce( 'weforms' ) ); ?>'
                });
            });
        </script>
        <?php
    }

    /**
     * Dismiss promotion notice
     *
     * @return void
     *
     * @since  1.2.6
     */
    public function dismiss_promotional_offer() {
        if( empty( $_POST['_wpnonce'] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'weforms' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! isset( $_POST['reason_id'] ) ) {
            wp_send_json_error();
        }

        if ( ! empty( $_POST['dismissed'] ) ) {
            $offer_key = 'weforms_promotional_offer_notice';
            update_option( $offer_key, 'hide' );
        }
    }

    /**
     * Dismiss review notice
     *
     * @return void
     *
     **@since  1.3.5
     */
    public function dismiss_review_notice() {
        if( empty( $_POST['_wpnonce'] ) ) {
             wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'weforms' ) ) {
            wp_send_json_error( __( 'Unauthorized operation', 'weforms' ) );
        }

        if ( ! empty( $_POST['dismissed'] ) ) {
            update_option( 'weforms_review_notice_dismiss', 'yes' );
        }
    }
}
