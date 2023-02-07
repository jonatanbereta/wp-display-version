<?php
/*
Plugin Name: WP Display Version
Plugin URI: https://github.com/jonatanbereta/wp-display-version.git
Description: Plugin to display versions of WP
Version: 1.0
Author: Jonatan Bereta
Author URI: https://jonatanbereta.netlify.app
Uninstaller
*/


require __DIR__ . "/inc/functions.php";
require __DIR__ . "/inc/helpers.php";


function wp_version_add_stylesheet()
{
    wp_enqueue_style('wp-version-style', plugin_dir_url(__FILE__) . '/css/style.css', array(), filemtime(__DIR__ . '/css/style.css'), 'all');
}
add_action('wp_enqueue_scripts', 'wp_version_add_stylesheet');

function wp_version_add_script()
{
    wp_enqueue_script('wp-version-jquery', plugin_dir_url(__FILE__) . '/js/jquery.js', array(), false, 'all');
    wp_enqueue_script('wp-version-script', plugin_dir_url(__FILE__) . '/js/script.js', array(), filemtime(__DIR__ . '/js/script.js'), 'all');
}
add_action('wp_enqueue_scripts', 'wp_version_add_script');


function wp_version($atts)
{

    $atts = shortcode_atts([
        'type' => '',
        'version' => '',
        'color' => 'yes',

    ], $atts);

    $data = get_wp_data();


    if ($atts['type'] === 'latest') {
        return wp_version_latest($data, $atts['color']);
    } else if ($atts['type'] === 'validate') {
        $status = wp_version_validate($atts['version'], $data);
        return wp_version_status($atts['version'], $status, $atts['color']);
    } else if ($atts['type'] === 'subversion') {
        $list = "<div><p>Subversion of the version " . $atts['version'] . "</p><ul class='subversion'>";
        $subversion = wp_version_subversion($atts['version'], $data);

        foreach ($subversion as $item) {
            $list .= "<li>" . $item . "</li>";
        }
        $list .= "</ul></div>" . display_wp_date();
        return $list;
    } else if ($atts['type'] === 'mine') {
        return wp_version_mine($data, $atts['color']);
    }
}

add_shortcode('wp_version', 'wp_version');
