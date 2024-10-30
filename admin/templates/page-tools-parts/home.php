<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div v-show="current_view === 'home'" class="home-container">
    <h1>
        <?php esc_html_e( "🛠️ Welcome to Clean My WP", 'clean-my-wordpress' ); ?>
    </h1>
    <p>
        <?php esc_html_e( "This plugin is a toolbox to help you clean your WordPress installation. Now you can explore your WordPress installation and find out what's taking up space. For this, click on the 'Disk Explorer' tab.", 'clean-my-wordpress' ); ?>
    </p>
    <p>
        <?php esc_html_e( "You can contribute to the development of this plugin by making a donation. For this, click on the 'Contribute' button in the menu.", 'clean-my-wordpress' ); ?>
    </p>
</div>