# Anonymize Comment IPs

Anonymize Comment IPs is a WordPress plugin that efficiently anonymizes comment IP addresses to comply with GDPR.

## Description

This plugin anonymizes both IPv4 and IPv6 addresses in comments to ensure compliance with GDPR regulations. It provides an admin interface to manually anonymize all existing comment IPs in the database.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/anonymize-comment-ip` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the 'Anonymize IPs' menu item in the WordPress admin to manually anonymize existing comment IPs.

## Usage

Once activated, the plugin will automatically anonymize IP addresses for new comments. To anonymize existing comment IPs, navigate to the 'Anonymize IPs' menu item in the WordPress admin and click the 'Start IP Anonymization' button.

## Functions

### `pixovoid_anonymize_ip($ip)`

Anonymizes an IP address (IPv4 & IPv6).

- **Parameters:**
  - `$ip` (string): The original IP address.
- **Returns:**
  - (string): Anonymized IP address or default if invalid.

### `pixovoid_filter_comment_ip($comment_ip)`

Filters new comment IPs before saving.

- **Parameters:**
  - `$comment_ip` (string): The original comment IP.
- **Returns:**
  - (string): Anonymized IP.

### `pixovoid_anonymize_existing_ips()`

Anonymizes all existing comment IPs in the database.

- **Returns:**
  - (int): Number of updated comments.

## License

This plugin is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Author

[PixoVoid.net](https://pixovoid.net/)

## Changelog

### 1.1.0

- Initial release.