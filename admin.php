<?php
// Create the admin settings page
function plugin_settings_page() {
    if (isset($_POST['save_settings'])) {
        $domain_name = sanitize_text_field($_POST['domain_name']);
        $tag_value = sanitize_text_field($_POST['tag_value']);
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'plugin_settings';
    
        $wpdb->insert(
            $table_name,
            array(
                'domain_name' => $domain_name,
                'tag_value' => $tag_value,
            )
        );
    }

    // Display the form
    ?>
    <div class="wrap">
        <h2>Plugin Settings</h2>
        <form method="post" action="">
            <label for="domain_name">Domain Name:</label>
            <input type="text" name="domain_name" id="domain_name" value=""><br>

            <label for="tag_value">Tag Value:</label>
            <input type="text" name="tag_value" id="tag_value" value=""><br>

            <input type="submit" name="save_settings" value="Save Settings">
        </form>
    </div>
    <?php
}

// Add the menu item in the admin dashboard
function add_plugin_menu() {
    add_menu_page(
        'Plugin Settings',
        'Plugin Settings',
        'manage_options',
        'plugin-settings',
        'plugin_settings_page'
    );
}

add_action('admin_menu', 'add_plugin_menu');