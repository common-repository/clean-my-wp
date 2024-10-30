<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section id="clean-my-wordpress-app" :class="[{'full-screen': full_screen}, current_view]">
    <nav>
        <ul class="top-left-actions">
            <li class="full-screen-toggle-button" @click="full_screen = !full_screen">
                <i :class="!full_screen ? 'bi bi-arrows-angle-expand' : 'bi bi-arrows-angle-contract'"></i>
            </li>
        </ul>
        <ul class="menu">
            <li v-for="view in views" :class="current_view === view.id ? 'active' : ''" @click="current_view = view.id">
                <div class="title">
                    <i :class="view.icon"></i>
                    <span>{{ view.title }}</span>
                </div>
                <div class="data">
                    {{ view.data }}
                </div>
            </li>
            <li class="cta">
                <a href="https://bmc.link/ludwig" target="_blank">
                    <?php esc_html_e( "Contribute ☕️", 'clean-my-wordpress' ); ?>
                </a>
            </li>
        </ul>
    </nav>

    <main>
        <?php include( CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'admin/templates/page-tools-parts/home.php' ); ?>

        <?php include( CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'admin/templates/page-tools-parts/disk-explorer.php' ); ?>
    </main>
</section>