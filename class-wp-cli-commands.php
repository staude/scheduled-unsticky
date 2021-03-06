<?php

/**
 * @description Manage the plugin settings
 */
class Scheduled_Unsticky_WP_CLI_Command extends WP_CLI_Command {

    /**
     * @subcommand info
     */    
    static public function info( $args, $assoc_args ) {
        $cron =  get_option('scheduled_unsticky_cron');
        $days = get_option( 'scheduled_unsticky_days' );
        if ($cron != 0) {
            $schedules = wp_get_schedules();
            $cron = $schedules[ $cron ];
        }
        WP_CLI::line('interval: '. $cron );
        WP_CLI::line('days    : '. $days );
    }

    /**
     * @subcommand days 
     * @synopsis <days>
     */
    static public function days( $args ) {
        $days = (int) $args[0];
        update_option( 'scheduled_unsticky_days', $days );
        WP_CLI::success('days: '. $days );
    }
    
    /**
     * @subcommand interval 
     * @synopsis <interval>
     */
    static public function interval( $args ) {
        $interval = $args[0];
        $schedules = wp_get_schedules();
        foreach ($schedules as $key => $value ) {
            if ( $key == $interval ) {
                update_option( 'scheduled_unsticky_cron', $interval );
                WP_CLI::success('interval: '. $interval );
                return;
            }
        }
        WP_CLI::error('interval ' . $interval . ' not available' );
        return;
        
    }
}


WP_CLI::add_command( 'scheduled-unsticky', 'Scheduled_Unsticky_WP_CLI_Command' );

