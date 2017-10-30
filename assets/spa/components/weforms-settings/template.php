<div class="weforms-settings clearfix" id="weforms-settings">

    <h1><?php _e( 'Settings', 'weforms' ); ?></h1>
    <div id="weforms-settings-tabs-warp">
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
</div>