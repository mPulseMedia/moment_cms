<?php
/*
    Plugin name: theMoment.tv
    Plugin URI: https://themoment.tv
    Description: Save, share and discover links to the best moments in video.
    Author: theMoment.tv
    Author URI: https://theMoment.tv
    Version: 0.1
*/
defined('ABSPATH') or die('No script kiddies please!');
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('THEMOMENT_PLUGIN_DIR', plugin_dir_path(__FILE__));

class Themoment
{
    private $post_id = null;
    private $post_url = null;

    public function __construct()
    {
        // Hooks

        // Script
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_action'));
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts_action'));
        add_action('admin_print_scripts', array($this, 'admin_print_scripts_action'));
        add_action('wp_print_scripts', array($this, 'wp_print_scripts_action'));

        // Admin Dashboard
        add_action('admin_menu', array($this, 'admin_menu_action'));

        // metadata_vseo
        add_action('wp_head', array($this, 'wp_head_action'));
        add_action('wp_ajax_wp_postmeta_playlist_data_get', array($this, 'wp_ajax_wp_postmeta_playlist_data_get_action'));
        add_action('wp_ajax_wp_postmeta_playlist_data_set', array($this, 'wp_ajax_wp_postmeta_playlist_data_set_action'));

        // Check
        add_action('the_post', array($this, 'the_post_action'));
        add_action('save_post', array($this, 'the_post_action'));

        add_filter('the_content', array($this, 'add_player_anchor'));
    }
    function add_player_anchor($content)
    {
        if (is_single()) {
            return $content = "<div id='anchor-" . get_the_ID() . "'></div>" . $content;
        }
    }

    function admin_menu_action()
    {
        add_options_page(__('theMoment', 'textdomain'), __('theMoment', 'textdomain'), 'manage_options', 'options_page_slug', array($this, 'admin_dashboard_page_render'));
    }
    function admin_dashboard_page_render()
    {
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_admin_dashboard.php');
    }


    public function admin_enqueue_scripts_action($hook)
    {
        if ('post.php' != $hook) {
            //return;
        }
        // ovrlay_script_admin_attach
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_script.php');
        wp_enqueue_script('themoment_script', $themoment_script, array(), '2.0');
    }
    public function wp_enqueue_scripts_action()
    {
        // ovrlay_script_frontend_attach
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_script.php');
        wp_enqueue_script('themoment_script', $themoment_script, array(), '2.0');
    }
    public function admin_print_scripts_action()
    {
        $wordpress_object = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'post_id' => $this->post_id_get(),
            'post_url' => $this->post_url_get(),
            'is_admin' => is_admin() ? 'yes' : 'no',
            'playlist_data' => get_post_meta($this->post_id_get(), 'playlist_data')
        );
        // ovrlay_script_admin_localize
        wp_localize_script('themoment_script', 'Wordpress_Object', $wordpress_object);
    }
    public function wp_print_scripts_action()
    {
        if (is_single()) {
            $wordpress_object = array(
                'post_id' => get_the_ID(),
                'post_url' => get_permalink()
            );
            wp_localize_script('themoment_script', 'Wordpress_Object', $wordpress_object);
        }
    }

    public function wp_head_action()
    {
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_metadata_vseo.php');
    }
    // https://codex.wordpress.org/AJAX_in_Plugins
    public function wp_ajax_wp_postmeta_playlist_data_set_action()
    {
        $playlist_data = $_POST['playlist_data'];
        $post_id = $_POST['post_id'];
        if (!get_post_meta($post_id, 'playlist_data')) {
            add_post_meta($post_id, 'playlist_data', $playlist_data);
        } else {
            update_post_meta($post_id, 'playlist_data', $playlist_data);
        }
    }
    public function wp_ajax_wp_postmeta_playlist_data_get_action()
    {
        $post_id = $_POST['post_id'];
        return get_post_meta($post_id, 'playlist_data');
    }

    // https://stackoverflow.com/questions/8463126/how-to-get-post-id-in-wordpress-admin
    public function the_post_action()
    {
        global $post;
        if ($post) {
            // Post meta duplicate bug fix
            if (wp_is_post_revision($post)) {
                $post = get_post($post->post_parent);
            }
            $this->post_id_set($post->ID);
            $this->post_url_set(get_permalink($post));
        }
    }
    private function post_id_get()
    {
        return $this->post_id;
    }
    private function post_url_get()
    {
        return $this->post_url;
    }
    private function post_id_set($post_id)
    {
        $this->post_id = $post_id;
    }
    private function post_url_set($post_url)
    {
        $this->post_url = $post_url;
    }
}

new Themoment();
