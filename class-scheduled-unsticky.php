<?php

/*  Copyright 2013  Frank Staude  (email : frank@staude.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class scheduled_unsticky {
    
    /**
     * Constructor
     * 
     * Register all actions and filters
     */
    function __construct() {
        add_action( 'admin_init',               array( 'scheduled_unsticky', 'settings_init' ) );
        add_action( 'plugins_loaded',           array( 'scheduled_unsticky', 'load_translations' ) );
        add_action( 'scheduled_unsticky_posts', array( 'scheduled_unsticky', 'unsticky_posts' ) );
        
        if ( defined ( 'WP_CLI' ) && WP_CLI ) {
            require_once( __DIR__ . '/class-wp-cli-commands.php' );
        }
    }
    
    /**
     * load the plugin textdomain
     * 
     * load the plugin textdomain with translations
     */
    function load_translations() {
        load_plugin_textdomain( 'scheduled_unsticky', false, dirname( plugin_basename( __FILE__ )) . '/languages/'  ); 
    }
    
    function settings_init() {
        add_settings_section('scheduled_unsticky_settings', __('Scheduled unsticky settings', 'scheduled_unsticky'), array( 'scheduled_unsticky', 'section_info' ), 'reading');
        add_settings_field('scheduled_unsticky_days', __('Days after publish', 'scheduled_unsticky'), array( 'scheduled_unsticky', 'field_days' ), 'reading', 'scheduled_unsticky_settings');
        add_settings_field('scheduled_unsticky_cron', __('Schedule interval','scheduled_unsticky'), array( 'scheduled_unsticky', 'field_cron' ), 'reading', 'scheduled_unsticky_settings');
        register_setting('reading', 'scheduled_unsticky_days');
        register_setting('reading', 'scheduled_unsticky_cron');
    }
    
    function section_info() {
        echo "<p>". __( 'Infotext', 'scheduled_unsticky' ) ."</p>";
    }

    function field_days() {
        echo '<input name="scheduled_unsticky_days" class="small-text" value="'. get_option('scheduled_unsticky_days') .'"> '.__('days','scheduled_unsticky');
    }

    function field_cron() {
        $cron =  get_option('scheduled_unsticky_cron');
        ?>
        <select name="scheduled_unsticky_cron">
            <option value="0" <?php if ( $cron == 0 ) { echo "selected"; } ?> ><?php _e('Never','scheduled_unsticky'); ?></option>
            <?php
            $schedules = wp_get_schedules();
            foreach ($schedules as $key => $schedule) {
                echo '<option value="'. $key . '"';
                if ( $cron == $key ) { echo " selected"; }
                echo '>';
                echo $schedule['display'];
                echo "</option>";
            }
            ?>
        </select>
        <?php
        // Einstellungen sind gespeichert, Cronjob ändern
        if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
            // alten Eintrag entfernen
            $timestamp = wp_next_scheduled( 'scheduled_unsticky_posts' );
            wp_unschedule_event( $timestamp, 'scheduled_unsticky_posts' );
            if ( $cron != "0") {
                wp_schedule_event( time(), $cron, 'scheduled_unsticky_posts' );
            }
        }

    }

    function unsticky_posts() {
        global $wpdb;
        $sticky = get_option( 'sticky_posts' );
        $days = get_option( 'scheduled_unsticky_days' );
        foreach ( $sticky as $key => $postID ) {
            $pubdate = $wpdb->get_var("SELECT post_date FROM {$wpdb->posts} WHERE ID = {$postID} ");
            $postTS =  strtotime($pubdate);
            if ( ( $postTS > 0 ) && ( $postTS + ( $days *  86400 ) < time() ) ) {
                unset ($sticky[ $key] );
            }
        }
        update_option( 'sticky_posts', $sticky );
    }
}
