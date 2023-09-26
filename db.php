<?php
// Create the database table on plugin activation
function create_plugin_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'plugin_settings'; // Use the correct table name with prefix
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        domain_name varchar(255) NOT NULL,
        tag_value varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    // add_option( 'test_db_version', $test_db_version );
}


// Handle database interactions (insert, retrieve settings, etc.)
global $wpdb;
$table_name = $wpdb->prefix . 'plugin_settings';

if (isset($_POST['save_settings'])) {
    $domain_name = sanitize_text_field($_POST['domain_name']);
    $tag_value = sanitize_text_field($_POST['tag_value']);

    $wpdb->replace(
        $table_name,
        array(
            'domain_name' => $domain_name,
            'tag_value' => $tag_value,
        )
    );
}

// Retrieve settings from the database
$settings = $wpdb->get_row("SELECT * FROM $table_name");

if ($settings) {
    $domain_name = $settings->domain_name;
    $tag_to_add = 'tag=' . $settings->tag_value;
}
