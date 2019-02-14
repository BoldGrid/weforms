<div class="weforms-settings clearfix" id="weforms-settings">

    <h1><?php _e( 'Settings', 'weforms' ); ?></h1>
    <div id="weforms-settings-tabs-warp" class="<?php echo !function_exists( 'weforms_pro' ) ? 'weforms-pro-deactivate' : ''; ?>">
        <div id="weforms-settings-tabs">
            <ul>
                <?php

                $tabs = apply_filters( 'weforms_settings_tabs', array() );

                foreach ( $tabs as $key => $tab ) :
                    ?>
                    <li>
                        <a
                            href="#"
                            :class="['we-settings-nav-tab', isActiveTab( '<?php echo $key; ?>' ) ? 'we-settings-nav-tab-active' : '']"
                            v-on:click.prevent="makeActive( '<?php echo $key; ?>' )"
                        >
                            <?php

                            if ( ! empty($tab['icon'] ) ) {
                                printf('<img src="%s">', $tab['icon']);
                            }
                            ?>
                            <?php _e( $tab['label'], 'weforms' ); ?>
                        </a>
                    </li>

                    <?php

                endforeach;

                do_action( 'weforms_settings_tabs_area' );
                ?>
            </ul>
        </div>

        <div id="weforms-settings-tabs-contents">

            <?php

                foreach ( $tabs as $key => $tab ) :
                    ?>
                    <div id="weforms-settings-<?php echo $key; ?>" class="tab-content" v-show="isActiveTab('<?php echo $key; ?>')">
                        <?php do_action( 'weforms_settings_tab_content_' . $key, $tab ); ?>
                    </div>
                    <?php

                endforeach;

                do_action( 'weforms_settings_tabs_contents' );
            ?>

        </div>
    </div>

    <?php if ( !function_exists( 'weforms_pro' ) ) : ?>

        <div id="weforms-settings-page-sidebar" class="weforms-settings-page-sidebar">
            <div class="weforms-settings-page-sidebar-content">
                <h2>Upgrade to <br><strong style="color:#57AB64">weForms Pro</strong></h2>

                <ul class="weforms-pro-features">
                    <li><span class="dashicons dashicons-yes"></span> Integration with email marketing solutions such as Aweber, GetResponse, ConvertKit etc.</li>
                    <li><span class="dashicons dashicons-yes"></span> Connect with productivity tools such as Google Analytics, Zapier, Trello, Google Sheets.</li>
                    <li><span class="dashicons dashicons-yes"></span> Manage payments directly from your forms with PayPal & Stripe.</li>
                    <li><span class="dashicons dashicons-yes"></span> Integrate with popular CRM tools such as Zoho, Salesforce, HubSpot and better manage your relationship with your customers.</li>
                    <li><span class="dashicons dashicons-yes"></span> Create quiz forms, calculate numbers directly in your form, set geolocation and more in weForms Pro.</li>
                </ul>

                <a href="https://wedevs.com/weforms-upgrade/" target="_blank" class="button button-primary">Get weForms Pro</a>
            </div>
        </div>

    <?php endif; ?>
</div>
