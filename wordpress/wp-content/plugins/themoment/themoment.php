<?php
/*
    Plugin name: theMoment
    Plugin URI: https://themoment.tv?noredir=1
    Description: Save, share and discover links to the best moments in video. One simple button to tag the scenes you like — while you watch. No downloads. No editing. No copyright violation. Just links to the scenes you tag in the original video.
    Author: StreamEditor
    Author URI: https://streameditor.tv
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
        // Script
        add_action('admin_enqueue_scripts', array($this, 'admin_script_enqueue'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_script_enqueue'));
        add_action('admin_print_scripts', array($this, 'admin_script_localize'));

        // Admin Dashboard
        add_action('admin_menu', array($this, 'admin_dashboard_menu_add'));
        
        // vseo_meta
        add_action('wp_head', array($this, 'post_vseo_meta_render'));
        add_action('wp_ajax_post_vseo_meta_get', array($this, 'post_vseo_meta_get'));
        add_action('wp_ajax_post_vseo_meta_set', array($this, 'post_vseo_meta_set'));

        // Check
        add_action('the_post', array($this, 'post_update_check'));
        add_action('save_post', array($this, 'post_update_check'));
    }

    function admin_dashboard_menu_add()
    {
        add_options_page(__('theMoment', 'textdomain'), __('theMoment', 'textdomain'), 'manage_options', 'options_page_slug', array($this, 'admin_dashboard_page_render'));
    }
    function admin_dashboard_page_render()
    {
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_admin_dashboard.php');
    }
    

    public function admin_script_enqueue($hook)
    {
        if ('post.php' != $hook) {
            //return;
        }
        wp_enqueue_script('themoment_script', 'http://localhost/moment_project/moment_app/output/ext/themoment.js', array(), '2.0');
    }
    public function frontend_script_enqueue()
    {
        wp_enqueue_script('themoment_script', 'http://localhost/moment_project/moment_app/output/ext/themoment.js', array(), '2.0');
    }
    public function admin_script_localize()
    {
        $wordpress_object = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'post_id' => $this->post_id_get(),
            'post_url' => $this->post_url_get()
        );
        wp_localize_script('themoment_script', 'Wordpress_Object', $wordpress_object);
    }
    
    public function post_vseo_meta_render()
    {
        require_once(THEMOMENT_PLUGIN_DIR . 'themoment_vseo_meta.php');
    }
    // https://codex.wordpress.org/AJAX_in_Plugins
    public function post_vseo_meta_set()
    {
        $veso_meta = $_POST['vseo_meta'];
        $post_id = $_POST['post_id'];
        if (!get_post_meta($post_id, 'vseo_meta')) {
            add_post_meta($post_id, 'vseo_meta', $veso_meta);
        } else {
            update_post_meta($post_id, 'vseo_meta', $veso_meta);
        }
    }
    public function post_vseo_meta_get()
    {
        $post_id = $_POST['post_id'];
        return get_post_meta($post_id, 'vseo_meta');
    }

    // https://stackoverflow.com/questions/8463126/how-to-get-post-id-in-wordpress-admin
    public function post_update_check()
    {
        global $post;
        if ($post) {
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
