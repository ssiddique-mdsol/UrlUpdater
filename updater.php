<?php
// Retrieve settings from the database
global $wpdb;
$table_name = $wpdb->prefix . 'plugin_settings';

$settings = $wpdb->get_row("SELECT * FROM $table_name");

if ($settings) {
    $domain_name = $settings->domain_name;
    $tag_to_add = 'tag=' . $settings->tag_value;

    // Regular expression pattern to match links to the specified domain (including www)
    $pattern = '/<a(.*?)href=["\'](http[s]?:\/\/(?:www\.)?' . preg_quote($domain_name) . '[^\s"\']+)["\'](.*?)>/i';

    // Add a filter to modify the content when it's displayed
    add_filter('the_content', function ($content) use ($pattern, $tag_to_add) {
        // Replace outgoing links with the modified version
        $content = preg_replace_callback($pattern, function ($match) use ($tag_to_add) {
            $url = $match[2];

            // Ensure that $url is not null before using parse_url()
            if ($url !== null) {
                $query = parse_url($url, PHP_URL_QUERY);

                // Check if the "tag" parameter exists in the query
                if (preg_match('/\btag=[A-Za-z0-9-]+\b/', $query)) {
                    // If the "tag" parameter exists, replace it with the user-entered tag
                    $new_query = preg_replace('/\btag=[A-Za-z0-9-]+\b/', $tag_to_add, $query);
                } else {
                    // If the "tag" parameter doesn't exist, add it
                    if ($query) {
                        // If there are existing parameters, append the user-entered tag
                        $new_query = $query . '&' . $tag_to_add;
                    } else {
                        // If there are no existing parameters, add the user-entered tag
                        $new_query = $tag_to_add;
                    }
                }

                // Reconstruct the URL with the modified query
                $new_url = $url;

                // Ensure that $query is not null before using it
                if ($query !== null) {
                    // Remove the existing query parameters
                    $new_url = str_replace('?' . $query, '', $new_url);
                    $new_url = str_replace('&' . $query, '', $new_url);
                }

                // Add the new query
                $new_url .= (strpos($new_url, '?') !== false ? '&' : '?') . $new_query;

                return '<a' . $match[1] . 'href="' . $new_url . '"' . $match[3] . '>';
            } else {
                // If $url is null, return the original link
                return $match[0];
            }
        }, $content);

        return $content;
    });
}