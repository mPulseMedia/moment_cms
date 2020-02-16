<?php
defined('ABSPATH') or die('No script kiddies please!');
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
function playlist_object_get($playlist_id = 0)
{
    $api_key = substr(md5(time()), 0, 32);
    $raw_data = file_get_contents("https://api-dev.themoment.tv/v1/playlists/" . $playlist_id . '?api_key=' . $api_key);
    $playlist_object = array();
    try {
        $raw_data = json_decode($raw_data, true);
        if (isset($raw_data['_data']) && isset($raw_data['_data']['_playlists']) && isset($raw_data['_data']['_playlists'][0])) {
            $playlist_object = $raw_data['_data']['_playlists'][0];
            $playlist_object['moment_objects'] = moment_objects_get($playlist_object['moments']);
        }
    } catch (Exception $e) {
    }
    return $playlist_object;
}
function moment_object_get($moment_id = 0)
{
    $api_key = substr(md5(time()), 0, 32);
    $raw_data = file_get_contents("https://api-dev.themoment.tv/v1/moments/" . $moment_id . '?api_key=' . $api_key);
    $moment_object = array();
    try {
        $raw_data = json_decode($raw_data, true);
        if (isset($raw_data['_data']) && isset($raw_data['_data']['_moments']) && isset($raw_data['_data']['_moments'][0])) {
            $moment_object = $raw_data['_data']['_moments'][0];
        }
    } catch (Exception $e) {
    }
    return $moment_object;
}
function moment_objects_get($moment_ids = '')
{
    $moment_ids = explode(',', $moment_ids);
    $moment_objects = array();
    foreach ($moment_ids as $moment_id) {
        array_push($moment_objects, moment_object_get($moment_id));
    }
    return $moment_objects;
}
function playlist_object_title_get($playlist_object = array())
{
    return isset($playlist_object['title']) && strlen($playlist_object['title']) ? $playlist_object['title'] : '';
}
function playlist_object_moment_object_array_get($playlist_object = array())
{
    return isset($playlist_object['moment_objects']) ? $playlist_object['moment_objects'] : array();
}
