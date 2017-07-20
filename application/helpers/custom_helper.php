<?php

function cloud_url() {
    $CI = & get_instance();
    return $CI->config->item('cloud_url');
}

function file_url() {
    $CI = & get_instance();
    return $CI->config->item('cloud_url') . $CI->config->item('assets_folder');
}

function pr($data = false, $exit = false) {
    echo '<pre>';
    print_r($data);
    if ($exit)
        exit;
}
