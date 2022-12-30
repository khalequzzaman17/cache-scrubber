<?php
/*
Plugin Name: Cache Scrubber
Plugin URI: https://github.com/khalequzzaman17/cache-scrubber
Description: A simple plugin to clear various types of cache on your site.
Version: 1.0
Author: Khalequzzaman
Author URI: https://github.com/khalequzzaman17
License: GPL2
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// register a custom menu page
function cache_clear_menu_page()
{
    add_menu_page('Cache Clear', // page title
        'Cache Clear', // menu title
        'manage_options', // capability
        'cache-clear', // menu slug
        'cache_clear_page_content', // function to display the page
        'dashicons-trash', // icon url
        20 // position
        );
}
add_action('admin_menu', 'cache_clear_menu_page');

// display the page content
function cache_clear_page_content()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap">';
    echo '<h1>Cache Clear</h1>';
    echo '<p>Use the buttons below to clear different types of cache on your site:</p>';
    echo '<form method="post">';
    echo '<input type="submit" name="clear_page_cache" value="Clear Page Cache" class="button-secondary" />';
    echo '<input type="submit" name="clear_object_cache" value="Clear Object Cache" class="button-secondary" />';
    echo '</form>';
    echo '</div>';
}

// clear the page cache
function cache_clear_page_cache()
{
    if (isset($_POST['clear_page_cache'])) {
        // clear the cache for all published pages
        $pages = get_pages();
        foreach ($pages as $page) {
            wp_cache_delete('page_' . $page->ID, 'pages');
        }
        // clear the cache for the home page
        wp_cache_delete('page_on_front', 'option');
        // clear the cache for the posts page
        wp_cache_delete('page_for_posts', 'option');
        // clear the cache for all terms
        $taxonomies = get_taxonomies();
        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy
            ));
            foreach ($terms as $term) {
                wp_cache_delete('term_' . $term->term_id, $taxonomy);
            }
        }
        // show a success message
        echo '<div class="notice notice-success is-dismissible">
            <p>The page cache has been cleared.</p>
        </div>';
    }
}

// clear the object cache
function cache_clear_object_cache()
{
    if (isset($_POST['clear_object_cache'])) {
        wp_cache_flush();
        // show a success message
        echo '<div class="notice notice-success is-dismissible"><p>The object cache has been cleared.</p></div>';
    }
}
add_action('admin_init', 'cache_clear_page_cache');
add_action('admin_init', 'cache_clear_object_cache');
