<?php
/**
 * Plugin Name: CodeHive Cleanup old orders
 * Plugin URI: https://github.com/gabrielfilippi/cdh-cleanup-orders
 * Description: Remove old orders after a certain date
 * Author: CodeHive
 * Author URI: https://codehive.com.br
 * Version: 1.0.0
 *
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

// Add cron job when activating the plugin
function cdh_cleanup_register_cron() {
    if (!wp_next_scheduled('cdh_cleanup_delete_orders_cron')) {
        wp_schedule_event(time(), 'daily', 'cdh_cleanup_delete_orders_cron');
    }
}
register_activation_hook(__FILE__, 'cdh_cleanup_register_cron');

// Remove cron job on plugin deactivation
function cdh_cleanup_remove_cron() {
    $timestamp = wp_next_scheduled('cdh_cleanup_delete_orders_cron');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'cdh_cleanup_delete_orders_cron');
    }
}
register_deactivation_hook(__FILE__, 'cdh_cleanup_remove_cron');

// Set cron job action
add_action('cdh_cleanup_delete_orders_cron', 'cdh_cleanup_delete_old_orders');
function cdh_cleanup_delete_old_orders() {
    cdh_cleanup_delete_orders('pending', '-1 month');
    cdh_cleanup_delete_orders('failed', '-2 months');
    cdh_cleanup_delete_orders('cancelled', '-2 months');
    cdh_cleanup_delete_orders('completed', '-1 year');
}

/**
 * Function to remove orders by status and time since creation date
 */
function cdh_cleanup_delete_orders($status, $time) {
    // Processes up to 200 orders at a time
    $limit = 200;
    if(defined("CDH_CLEANUP_ORDERS_BATCH_LIMIT")){
        $limit = intval(CDH_CLEANUP_ORDERS_BATCH_LIMIT);
    }

    $date_created = date("Y-m-d", strtotime($time));
    $args = array(
        'limit'         => $limit,
        'status'        => $status,
        'date_created'  => '<' . $date_created,
    );

    // Check if HPOS is enabled
    $is_hpos_enabled = class_exists('Automattic\WooCommerce\Utilities\OrderUtil') && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();

    if ($is_hpos_enabled) {
        $args['return'] = 'objects'; // Returns order objects
    } else {
        $args['return'] = 'ids'; // Returns order IDs
    }

    $orders = wc_get_orders($args);

    if (!empty($orders)) {
        foreach ($orders as $order) {
            if ($is_hpos_enabled) {
                // HPOS: use delete() method to delete orders
                $order->delete(true);
            } else {
                // Default method: uses wp_trash_post and wp_delete_post
                wp_trash_post($order); // Move to Trash
                wp_delete_post($order, true); // Permanently delete
            }
        }
    }
}