<div class="weforms-settings clearfix" id="weforms-settings">

    <h1><?php _e( 'Settings', 'weforms' ); ?></h1>

    <div>
        <fieldset class="clearfix">
            <h2 id="weforms-settings-tabs" class="nav-tab-wrapper">

                <?php

                $tabs = apply_filters( 'weforms_settings_tabs', array() );

                foreach ( $tabs as $key => $tab ) :
                    ?>
                    <a
                        href="#"
                        :class="['nav-tab', isActiveTab( '<?php echo $key; ?>' ) ? 'nav-tab-active' : '']"
                        v-on:click.prevent="makeActive( '<?php echo $key; ?>' )"
                        class="nav-tab"
                    >
                        <?php

                        if ( ! empty($tab['icon'] ) ) {
                            printf('<img src="%s">', $tab['icon']);
                        }
                        ?>
                        <?php _e( $tab['label'], 'weforms' ); ?>
                    </a>

                    <?php

                endforeach;

                do_action( 'weforms_settings_tabs_area' );
                ?>
            </h2>

            <div id="weforms-settings-tabs-contents" class="tab-contents">

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
        </fieldset>
    </div>
</div>