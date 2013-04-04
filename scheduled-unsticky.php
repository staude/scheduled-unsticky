<?php
/*
Plugin Name: Scheduled Unsticky
Plugin URI: http://www.staude.net/wordpress/plugins/ScheduledUnsticky
Description: Removes sticky flag after a adjustable period from posts
Author: Frank Staude
Version: 0.1
Author URI: http://www.staude.net/
Compatibility: WordPress 3.5.1
*/

/*  Copyright 2012  Frank Staude  (email : frank@staude.net)

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

if (!class_exists( 'scheduled_unsticky' ) ) {

    include_once dirname( __FILE__ ) .'/class-scheduled-unsticky.php';

    function scheduled_unsticky_uninstall() {
        delete_option( 'scheduled_unsticky_days' );
        delete_option( 'scheduled_unsticky_cron' );
    }

    function scheduled_unsticky_install() {
        add_option( 'scheduled_unsticky_days', '7' );
        add_option( 'scheduled_unsticky_cron', '0' );
    }

    register_uninstall_hook( __FILE__,  'scheduled_unsticky_uninstall' );
    register_activation_hook( __FILE__,  'scheduled_unsticky_install');

    $scheduled_unsticky = new scheduled_unsticky();

}
