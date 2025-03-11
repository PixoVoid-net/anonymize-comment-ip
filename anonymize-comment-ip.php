<?php
/**
 * Plugin Name: Anonymize Comment IPs
 * Plugin URI: https://pixovoid.net/
 * Description: Efficiently anonymizes comment IP addresses to comply with GDPR.
 * Version: 1.1.0
 * Author: PixoVoid.net
 * Author URI: https://pixovoid.net/
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: anonymize-comment-ip
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

/**
 * Anonymizes an IP address (IPv4 & IPv6).
 *
 * @param string $ip The original IP address.
 * @return string Anonymized IP address or default if invalid.
 */
function pixovoid_anonymize_ip($ip) {
    // Ensure $ip is a string and not empty
    if (!is_string($ip) || empty($ip)) {
        return '127.0.0.1';
    }

    // Validate and anonymize IPv4
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return preg_replace('/\.\d+$/', '.0', $ip); // Anonymize last octet
    }

    // Validate and anonymize IPv6
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return preg_replace('/:[0-9a-fA-F]{1,4}$/', ':0', $ip); // Anonymize last segment
    }

    // Return default for invalid IPs
    return '127.0.0.1';
}

/**
 * Filters new comment IPs before saving.
 *
 * @param string $comment_ip The original comment IP.
 * @return string Anonymized IP.
 */
function pixovoid_filter_comment_ip($comment_ip) {
    return pixovoid_anonymize_ip($comment_ip);
}
add_filter('pre_comment_user_ip', 'pixovoid_filter_comment_ip', 10, 1);

/**
 * Anonymizes all existing comment IPs in the database.
 *
 * @return int Number of updated comments.
 */
function pixovoid_anonymize_existing_ips() {
    global $wpdb;

    // Fetch comments with non-anonymized IPs
    $comments = $wpdb->get_results("
        SELECT comment_ID, comment_author_IP
        FROM {$wpdb->comments}
        WHERE comment_author_IP != ''
    ");

    if (!$comments) {
        return 0;
    }

    $updated_count = 0;
    foreach ($comments as $comment) {
        $anonymized_ip = pixovoid_anonymize_ip($comment->comment_author_IP);
        if ($anonymized_ip !== $comment->comment_author_IP) {
            $result = $wpdb->update(
                $wpdb->comments,
                ['comment_author_IP' => $anonymized_ip],
                ['comment_ID' => $comment->comment_ID],
                ['%s'],
                ['%d']
            );
            if ($result !== false) {
                $updated_count++;
            }
        }
    }

    return $updated_count;
}

/**
 * Adds admin menu for manual anonymization.
 */
function pixovoid_add_admin_menu() {
    add_menu_page(
        __('Anonymize Comment IPs', 'anonymize-comment-ip'),
        __('Anonymize IPs', 'anonymize-comment-ip'),
        'manage_options',
        'anonymize-comment-ip',
        'pixovoid_admin_page',
        'dashicons-shield',
        80
    );
}
add_action('admin_menu', 'pixovoid_add_admin_menu');

/**
 * Renders the admin settings page.
 */
function pixovoid_admin_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'anonymize-comment-ip'));
    }

    // Handle form submission
    if (isset($_POST['pixovoid_anonymize_ips']) && check_admin_referer('pixovoid_anonymize_ips_action', 'pixovoid_anonymize_ips_nonce')) {
        $count = pixovoid_anonymize_existing_ips();
        $message = sprintf(
            /* translators: %d: number of IPs anonymized */
            esc_html__('%d IPs anonymized successfully.', 'anonymize-comment-ip'),
            $count
        );
        echo '<div class="updated"><p><strong>' . esc_html($message) . '</strong></p></div>';
    }

    ?>
    <div class="wrap">
        <h2><?php esc_html_e('Anonymize Comment IPs', 'anonymize-comment-ip'); ?></h2>
        <p><?php esc_html_e('Click the button below to anonymize all stored comment IPs immediately.', 'anonymize-comment-ip'); ?></p>
        <form method="post">
            <?php wp_nonce_field('pixovoid_anonymize_ips_action', 'pixovoid_anonymize_ips_nonce'); ?>
            <input type="submit" name="pixovoid_anonymize_ips" class="button button-primary" value="<?php esc_attr_e('Start IP Anonymization', 'anonymize-comment-ip'); ?>">
        </form>
    </div>
    <?php
}

/**
 * Load plugin text domain for translations.
 */
function pixovoid_load_textdomain() {
    load_plugin_textdomain('anonymize-comment-ip', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'pixovoid_load_textdomain');
