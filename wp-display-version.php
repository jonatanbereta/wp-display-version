<?php
/*
Plugin Name: WP Display Version
Description: Plugin to display versions of WP, test from SateliteWP
Version: 1.0
Author: Jonatan Bereta
*/





function wp_version_add_stylesheet()
{
    wp_enqueue_style('wp-version-style', plugin_dir_url(__FILE__) . '/css/style.css', array(), filemtime(__DIR__ . '/css/style.css'), 'all');
}
add_action('wp_enqueue_scripts', 'wp_version_add_stylesheet');

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
        $list .= "</ul></div>";
        return $list;
    } else if ($atts['type'] === 'mine') {
        return wp_version_mine($data,$atts['color']);
    }
}

add_shortcode('wp_version', 'wp_version');




function get_wp_data()
{
    $getData = wp_remote_get('https://endoflife.date/api/wordpress.json');
    $data = json_decode(wp_remote_retrieve_body($getData), true);
    return $data;
}

function wp_version_latest(array $data, string $color)
{
    $latest = maxValueInArray($data, "latest");
    return "<div> The latest WP version is <span " . ($color === 'yes' ? "class='text-green'" : "") . ">" . $latest . "</span></div>";
}


function wp_version_validate(string $version, array $data)
{

    if (!$version) {
        return "<div class='text-red'>Please, insert a version value to validate.</div>";
    }


    foreach ($data as $item) {
        if (($item['cycle'] === $version || $item['latest'] === $version) && ($item['eol'])) {
            return "Insecure";
        } elseif (($item['cycle'] === $version || $item['latest'] === $version) && ($item['support'] === true)) {
            return "Latest";
        } elseif (($item['cycle'] === $version || $item['latest'] === $version) && (!$item['eol']) && ($item['support'] !== true)) {
            return "Outdated";
        }
    }
    return null;
}

function wp_version_status(string $version, ?string $status, string $color)
{

    $class = '';

    if (!$status) {
        return "<div>Version not valid or not found</div>";
    }

    switch ($status) {
        case "Insecure":
            $class = 'text-red';
            break;
        case "Latest":
            $class = 'text-green';
            break;
        case "Outdated":
            $class = 'text-orange';
            break;
    }

    return "<div>Version " . $version . " - <span class='" . ($color === 'yes' ? $class : '') . "'>" . $status . "</span></div>";
}



function wp_version_subversion(string $version, array $data)
{
    $list = array();

    foreach ($data as $item) {
        if ($item['cycle'] === $version) {
            $subversion = explode('.', $item['latest']);
            for ($i = $subversion[2]; $i >= 0; $i--) {
                $list[$i] = $version . ($i == 0 ? '' : '.' . $i);
            }
        }
    }
    return $list;
}

function wp_version_mine(array $data, string $color)
{
    $status = wp_version_validate(get_bloginfo('version'), $data);


    if (!$status) {
        return "<div>Version not valid or not found</div>";
    }

    switch ($status) {
        case "Insecure":
            $class = 'text-red';
            break;
        case "Latest":
            $class = 'text-green';
            break;
        case "Outdated":
            $class = 'text-orange';
            break;
    }

    return "<div>Your WP version <span class='" . ($color === 'yes' ? $class : '') . "'>". get_bloginfo('version') . " - " . $status . "</span></div>";
}


function maxValueInArray($array, $keyToSearch)
{
    $currentMax = NULL;
    foreach ($array as $arr) {
        foreach ($arr as $key => $value) {
            if ($key == $keyToSearch && ($value >= $currentMax)) {
                $currentMax = $value;
            }
        }
    }

    return $currentMax;
}
