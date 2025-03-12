<?php

/**
 * Plugin Name: Anonymize Comment IPs
 * Plugin URI: https://pixovoid.net/
 * Description: Automatically anonymizes comment IP addresses to ensure GDPR compliance.
 * Version: 1.2.0
 * Author: PixoVoid.net
 * Author URI: https://pixovoid.net/
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: anonymize-comment-ip
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access.
}

/**
 * Anonymizes an IP address (IPv4 & IPv6) by removing identifiable parts.
 *
 * @param string $ip The original IP address.
 * @return string Anonymized IP address.
 */
function pixovoid_anonymize_ip($ip) {
    if (!is_string($ip) || empty($ip)) {
        return '0.0.0.0'; // Fallback.
    }

    // Prüfe auf gültige IPv4-Adresse
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return preg_replace('/\.\d+$/', '.0', $ip); // Letztes Oktett anonymisieren
    }

    // Prüfe auf gültige IPv6-Adresse
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return preg_replace('/:[0-9a-fA-F]{1,4}$/', ':0', $ip); // Letztes Segment anonymisieren
    }

    return '0.0.0.0'; // Falls keine gültige IP erkannt wurde
}

/**
 * Filters and anonymizes new comment IPs before they are stored.
 *
 * @param string $comment_ip The original comment IP.
 * @return string Anonymized IP.
 */
function pixovoid_filter_comment_ip($comment_ip)
{
    return pixovoid_anonymize_ip($comment_ip);
}
add_filter('pre_comment_user_ip', 'pixovoid_filter_comment_ip');

/**
 * Bulk anonymizes all stored comment IPs.
 *
 * @return int Number of updated comments.
 */
function pixovoid_anonymize_existing_ips()
{
    global $wpdb;

    // Fetch comments that still have IPs.
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
            $updated = $wpdb->update(
                $wpdb->comments,
                ['comment_author_IP' => $anonymized_ip],
                ['comment_ID' => (int) $comment->comment_ID],
                ['%s'],
                ['%d']
            );
            if ($updated !== false) {
                $updated_count++;
            }
        }
    }

    return $updated_count;
}

/**
 * Adds admin menu for IP anonymization.
 */
function pixovoid_add_admin_menu()
{
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
function pixovoid_admin_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.', 'anonymize-comment-ip'));
    }

    if (isset($_POST['pixovoid_anonymize_ips']) && check_admin_referer('pixovoid_anonymize_ips_action', 'pixovoid_anonymize_ips_nonce')) {
        $count = pixovoid_anonymize_existing_ips();
        printf(
            '<div class="updated"><p><strong>%s</strong></p></div>',
            esc_html(sprintf(__('Anonymized %d comment IPs successfully.', 'anonymize-comment-ip'), $count))
        );
    }

?>
    <div class="wrap">
        <h2><?php esc_html_e('Anonymize Comment IPs', 'anonymize-comment-ip'); ?></h2>
        <p><?php esc_html_e('Click the button below to anonymize all stored comment IPs immediately.', 'anonymize-comment-ip'); ?></p>
        <form method="post">
            <?php wp_nonce_field('pixovoid_anonymize_ips_action', 'pixovoid_anonymize_ips_nonce'); ?>
            <input type="submit" name="pixovoid_anonymize_ips" class="button button-primary" value="<?php esc_attr_e('Start Anonymization', 'anonymize-comment-ip'); ?>">
        </form>
    </div>
<?php
}

/**
 * Loads plugin text domain for translations.
 */
function pixovoid_load_textdomain()
{
    load_plugin_textdomain('anonymize-comment-ip', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'pixovoid_load_textdomain');
