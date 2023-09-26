<?php 
/*
Plugin Name: UrlUpdater
Description: Update outgoing links on fly
Version: 1.0
Author: Shahid Siddique
*/

function update_amazon_links($content) {
    // Regular expression pattern to match Amazon links
    $pattern = '/<a(.*?)href=["\'](http[s]?:\/\/(www\.)?amazon\.(com|co\.in|co\.uk|de|fr|es|it|ca|com\.au|com\.br|com\.mx|nl|se|ae|sg|jp|cn|in)([^\s"\']*)?)["\'](.*?)>/i';

    // Replace outgoing Amazon links with the modified version
    $content = preg_replace_callback($pattern, function ($match) {
        // Extract the parts of the link
        $url = $match[2];
        $query = parse_url($url, PHP_URL_QUERY);

        // Check if the "tag" parameter exists in the query
        if (preg_match('/\btag=[A-Za-z0-9-]+\b/', $query)) {
            // If the "tag" parameter exists, replace it with "tag=ssiddique"
            $new_query = preg_replace('/\btag=[A-Za-z0-9-]+\b/', 'tag=ssiddique', $query);
        } else {
            // If the "tag" parameter doesn't exist, add it
            if ($query) {
                // If there are existing parameters, append "&tag=ssiddique"
                $new_query = $query . '&tag=ssiddique';
            } else {
                // If there are no existing parameters, add "?tag=ssiddique"
                $new_query = 'tag=ssiddique';
            }
        }

        // Reconstruct the URL with the modified query
        $new_url = $url;

        if ($query !== null) {
            // Remove the existing query parameters
            $new_url = str_replace('?' . $query, '', $new_url);
            $new_url = str_replace('&' . $query, '', $new_url);
        }

        // Add the new query
        $new_url .= (strpos($new_url, '?') !== false ? '&' : '?') . $new_query;

        return '<a' . $match[1] . 'href="' . $new_url . '"' . $match[5] . '>';
    }, $content);

    return $content;
}

// Hook into the content filter to modify links
add_filter('the_content', 'update_amazon_links');