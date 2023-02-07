<?php

add_option('wp_data_update_time', '', '', 'yes');

function get_wp_data()
{
    $transient_name = 'wp_api_data';
    $data = get_transient($transient_name);
    if (!$data) {
        $getData = wp_remote_get('https://endoflife.date/api/wordpress.json');
        $data = json_decode(wp_remote_retrieve_body($getData), true);
        set_transient($transient_name, $data, DAY_IN_SECONDS);
        update_option('wp_data_update_time', current_time('mysql'));

    }

    return $data;
}



function wp_version_latest(array $data, string $color)
{
    $latest = maxValueInArray($data, "latest");
    return "<div> The latest WP version is <span " . ($color === 'yes' ? "class='text-green'" : "") . ">" . $latest . "</span></div>" . display_wp_date();
}


function wp_version_validate(string $version, array $data)
{

    if (!$version) {
        return "Please, insert a version value into shortcode to validate.";
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

    return "<div>Version " . $version . " - <span class='" . ($color === 'yes' ? $class : '') . "'>" . $status . "</span></div>".display_wp_date();
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

    return "<div>Your WP version <span class='" . ($color === 'yes' ? $class : '') . "'>" . get_bloginfo('version') . " - " . $status . "</span></div>" . display_wp_date();
}



function display_wp_date()
{
    $display="<p class='time'>Last API call: ".get_option('wp_data_update_time')." <em class='dashicons dashicons-image-rotate'></em></p>";

    return $display;
}