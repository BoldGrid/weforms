<div class="weforms-premium">
    <?php
        // _e( 'weForms Pro', 'weforms' );
        // echo WeForms_Form_Builder_Assets::get_pro_url();
        //  echo WEFORMS_ASSET_URI; /images/integrations/mailchimp.svg"
    ?>
    <!-- start banner section -->
    <div id="banner" class="wf-banner-section wf-section-wrapper">
        <!-- banner left column -->
        <div class="banner-left-column">
            <div class="banner-icon">
                <svg width="25px" height="28px" viewBox="0 0 25 28" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Premium-Page-Design-for-weForms" transform="translate(-298.000000, -174.000000)" fill="#4CDAA0">
                            <g id="Group-3" transform="translate(285.000000, 163.000000)">
                                <g id="Page-1-Copy-6" transform="translate(13.076923, 11.538462)">
                                    <path d="M22.4734843,6.65402042 L4.95080724,13.9647562 C3.1534996,14.7235956 1.06664956,13.9130012 0.290961897,12.1540325 C-0.484725767,10.3957631 0.343865941,8.35424032 2.14260342,7.59540091 L19.6645656,0.284665163 C21.4625881,-0.474174241 23.5487233,0.336420108 24.3244109,2.09468947 C25.1000986,3.85435762 24.2715069,5.89518101 22.4734843,6.65402042 M22.4734843,19.3276876 L4.95080724,26.6384233 C3.1534996,27.3972627 1.06664956,26.5866684 0.290961897,24.8276996 C-0.484725767,23.0687308 0.343865941,21.0279075 2.14260342,20.269068 L19.6645656,12.9583323 C21.4625881,12.1994929 23.5487233,13.0100872 24.3244109,14.769056 C25.1000986,16.5280248 24.2715069,18.5688481 22.4734843,19.3276876" id="Fill-1"></path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="banner-content">
                <h1><?php _e( 'weForms Pro', 'weforms' ); ?></h1>
                <p><?php _e( 'Upgrade to the premium versions of weForms and <br>unlock even more useful features.' );?></p>
            </div>
            <div class="banner-buttons">
                <a href="https://wedevs.com/weforms-upgrade/" class="wf-btn wf-btn-primary" target="_blank"><?php _e( 'Buy Now', 'weforms' ); ?></a>
                <a href="https://wedevs.com/weforms/" class="wf-btn wf-btn-default" target="_blank"><?php _e( 'Read Full Guide', 'weforms' ); ?></a>
            </div>
        </div><!-- end banner left column -->


        <!-- video modal -->

        <div id="wf-video-modal" :class="['wf-modal', showModal ? 'wf-modal-open': '']" role="dialog" @click="showModal = false">
            <div class="wf-modal-dialog">
                <div class="wf-modal-content">
                    <span class="modal-close" @click="showModal = false">x</span>
                    <div class="wf-modal-body">
                        <iframe width="600px" height="400px" src="https://www.youtube.com/embed/668nUCeBHyY?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>


        <!-- banner right column -->
        <div class="banner-right-column">
            <div class="banner-thumb">
                <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/banner-thumb.svg" alt="Banner">
                <!-- <a class="video-play-icon" href="#" @click.prevent="showModal = true">
                    <svg width="15px" height="17px" viewBox="0 0 15 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <linearGradient x1="50%" y1="100%" x2="12.980572%" y2="0%" id="linearGradient-1">
                                <stop stop-color="#12CE66" offset="0%"></stop>
                                <stop stop-color="#7EE6D1" offset="100%"></stop>
                            </linearGradient>
                        </defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Premium-Page-Design-for-weForms" transform="translate(-1106.000000, -335.000000)" fill="url(#linearGradient-1)">
                                <g id="Group-9" transform="translate(1087.000000, 318.000000)">
                                    <path d="M32.552792,24.6413241 L21.1089656,18.2019425 C20.0661123,17.6145297 19.2249533,18.0841645 19.2307995,19.2479777 L19.2894009,32.0295103 C19.2946903,33.1930549 20.1461497,33.6667186 21.1920654,33.0838719 L32.5483378,26.7581051 C33.5931399,26.1763328 33.595367,25.2287369 32.552792,24.6413241 Z" id="Path"></path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a> -->
            </div>
        </div><!-- end banner right column -->

    </div><!-- end banner section -->

    <!-- start features section -->
    <div id="features" class="wf-features-wrapper wf-section-wrapper">
        <div class="section-header">
            <h2><?php _e( 'More Features', 'weforms' ); ?></h2>
        </div>
        <div class="section-content">
            <div class="feature-row">
                <div class="feature-column feature-advance-fields">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/advance-fields.svg" alt="Advanced Fields">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'Advance Fields', 'weforms' ); ?></h3>
                        <p><?php _e( 'Build any kind of form flexibly with the advanced field option. Its user friendly interface makes sure you do not have to scratch your head over building forms.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-conditional-logic">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/conditional-logic.svg" alt="Conditional Logic">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'Conditional Logic', 'weforms' ); ?></h3>
                        <p><?php _e( 'Configure your form’s settings and user flow based on conditional selection. Your forms should appear just the way you want it.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-multi-step">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/multistep-form.svg" alt="Multi Step">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'Multi-step Form', 'weforms' ); ?></h3>
                        <p><?php _e( 'Break down the long forms into small and attractive multi step forms. Long and lengthy forms are uninviting, why build one?', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-file-uploaders">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/file-uploader.svg" alt="File uploaders">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'File Uploaders', 'weforms' ); ?></h3>
                        <p><?php _e( 'Let the user upload any kind of file by filling up your contact form. The process is unbelievably smooth and supports a wide range of file formats.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-notification">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/notification.svg" alt="Form Submit Notification">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'Form Submission Notication', 'weforms' ); ?></h3>
                        <p><?php _e( 'Receive email notification every time your form is submitted. You can now configure the notification settings just as you like it.', 'weforms' ); ?></p>
                    </div>
                </div>
                <div class="feature-column feature-submission">
                    <div class="feature-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/features/submission.svg" alt="Manage Submission">
                    </div>
                    <div class="feature-content">
                        <h3><?php _e( 'Manage Submission', 'weforms' ); ?></h3>
                        <p><?php _e( 'View, edit and manage all the submission data stored through your form. We believe that you should own it all- like literally!', 'weforms' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end features section -->

    <!-- start integration section -->
    <div id="integration" class="wf-integration-wrapper wf-section-wrapper">
        <div class="section-header">
            <h2><?php _e( 'More Integrations', 'weforms' ); ?></h2>
        </div>
        <div class="section-content">
            <div class="integration-row">
                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/mailchimp.svg" alt="Mailchimp integration">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'Mailchimp', 'weforms' ); ?></h3>
                        <p><?php _e( 'Integrate your desired form to your MailChimp email newsletter using latest API.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/campaign-monitor.svg" alt="Campaign Monitor">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'Campaign Monitor', 'weforms' ); ?></h3>
                        <p><?php _e( 'Lets you add submission form in your Campaign Monitor email campaigns too.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/constant-contact.svg" alt="Constant Contact">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'Constant Contact', 'weforms' ); ?></h3>
                        <p><?php _e( 'Integrate your contact forms seamlessly with your Constant Contact account.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/mailpoet.svg" alt="MailPoet">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'MailPoet', 'weforms' ); ?></h3>
                        <p><?php _e( 'Why only MailChimp? Do the same for MailPoet email campaigns as well!', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/aweber.svg" alt="AWeber">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'AWeber', 'weforms' ); ?></h3>
                        <p><?php _e( 'Use highly customizable forms and create subscriber’s list for AWber email solution.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/get-response.svg" alt="Get Response">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'Get Response', 'weforms' ); ?></h3>
                        <p><?php _e( 'Enjoy seamless integration of weForms with your Get Response account.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/convert-kit.svg" alt="ConvertKit">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'ConvertKit', 'weforms' ); ?></h3>
                        <p><?php _e( 'Subscribe a contact to ConvertKit when a form is submited.', 'weforms' ); ?></p>
                    </div>
                </div>

                <div class="integration-column">
                    <div class="integration-thumb">
                        <img src="<?php echo WEFORMS_ASSET_URI; ?>/images/premium/integrations/more-integration.svg" alt="More.Integration">
                    </div>
                    <div class="integration-content">
                        <h3><?php _e( 'More...', 'weforms' ); ?></h3>
                        <p><?php _e( 'A bunch of more integrations are coming soon.', 'weforms' ); ?></p>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- end integration section -->

    <!-- start footer section -->
    <section id="import" class="wf-import-wrapper">
        <div class="section-content">
            <div class="import-left">
                <div class="import-icon">

                    <svg width="32px" height="35px" viewBox="0 0 32 35" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Premium-Page-Design-for-weForms" transform="translate(-278.000000, -2204.000000)" fill="#FFFFFF">
                                <g id="Group-3" transform="translate(261.000000, 2189.000000)">
                                    <g id="Page-1-Copy-6" transform="translate(17.000000, 15.000000)">
                                        <path d="M29.2155296,8.65022654 L6.43604941,18.154183 C4.09954948,19.1406742 1.38664443,18.0869016 0.378250466,15.8002422 C-0.630143496,13.514492 0.447025723,10.8605124 2.78538444,9.87402119 L25.5639353,0.370064711 C27.9013646,-0.616426514 30.6133402,0.43734614 31.6217342,2.72309632 C32.6301282,5.01066491 31.5529589,7.66373532 29.2155296,8.65022654 M29.2155296,25.1259938 L6.43604941,34.6299503 C4.09954948,35.6164415 1.38664443,34.5626689 0.378250466,32.2760095 C-0.630143496,29.9893501 0.447025723,27.3362797 2.78538444,26.3497885 L25.5639353,16.845832 C27.9013646,15.8593408 30.6133402,16.9131134 31.6217342,19.1997728 C32.6301282,21.4864322 31.5529589,24.1395026 29.2155296,25.1259938" id="Fill-1"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="import-text">
                    <p><?php _e( 'Extend the functionalities while', 'weforms' );?></p>
                    <h2><?php _e( 'Building WordPress Forms', 'wefoms' ); ?></h2>
                </div>
            </div>
            <div class="import-right">
                <a href="https://wedevs.com/weforms/pricing/" target="_blank" class="wf-btn wf-btn-primary wf-btn-lg"><?php _e( 'Upgrade Now', 'weforms' ); ?></a>
            </div>
        </div>
    </section><!-- end footer section -->

</div>