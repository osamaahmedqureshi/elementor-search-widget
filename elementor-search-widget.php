<?php
/**
 * Plugin Name: Elementor Search Widget
 * Plugin URI: https://github.com/osamaahmedqureshi/elementor-search-widget
 * Description: Adds a lightweight Elementor-compatible search widget with classic, minimal, creative, full-screen, and half-screen layouts. Works with the free version of Elementor.
 * Version: 1.1.3
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Osama Ahmed Qureshi
 * Author URI: https://github.com/osamaahmedqureshi
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: elementor-search-widget
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('HJ_ESW_PATH', plugin_dir_path(__FILE__));
define('HJ_ESW_URL', plugin_dir_url(__FILE__));
define('HJ_ESW_VERSION', '1.1.3');

final class HJ_Elementor_Search_Widget_Plugin {
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
        add_action('wp_ajax_hj_esw_search', [$this, 'ajax_search']);
        add_action('wp_ajax_nopriv_hj_esw_search', [$this, 'ajax_search']);
    }

    public function init() {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-warning"><p><strong>Elementor Search Widget</strong> requires Elementor to be installed and active.</p></div>';
            });
            return;
        }
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('elementor/widgets/register', [$this, 'register_widget']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    public function register_category($elements_manager) {
        $elements_manager->add_category('hj-widgets', ['title' => __('HJ Widgets', 'elementor-search-widget'), 'icon' => 'fa fa-plug']);
    }

    public function register_assets() {
        wp_register_style('hj-esw-style', HJ_ESW_URL . 'assets/css/search-widget.css', [], HJ_ESW_VERSION);
        wp_register_script('hj-esw-script', HJ_ESW_URL . 'assets/js/search-widget.js', ['jquery'], HJ_ESW_VERSION, true);
        wp_localize_script('hj-esw-script', 'HJ_ESW', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('hj_esw_nonce'),
        ]);
    }

    public function register_widget($widgets_manager) {
        require_once HJ_ESW_PATH . 'widgets/search-widget.php';
        $widgets_manager->register(new \HJ_ESW_Search_Widget());
    }

    public function ajax_search() {
        check_ajax_referer('hj_esw_nonce', 'nonce');
        $keyword = isset($_POST['keyword']) ? sanitize_text_field(wp_unslash($_POST['keyword'])) : '';
        $source  = isset($_POST['source']) ? sanitize_key($_POST['source']) : 'posts';
        $limit   = isset($_POST['limit']) ? absint($_POST['limit']) : 5;

        if (mb_strlen($keyword) < 2) wp_send_json_success([]);

        $post_type = 'post';
        if ($source === 'pages') $post_type = 'page';
        if ($source === 'products') $post_type = post_type_exists('product') ? 'product' : 'post';
        if ($source === 'all') $post_type = ['post', 'page'];

        $query = new WP_Query([
            'post_type' => $post_type,
            'post_status' => 'publish',
            's' => $keyword,
            'posts_per_page' => max(1, min($limit, 12)),
            'no_found_rows' => true,
        ]);

        $results = [];
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = [
                'title' => html_entity_decode(get_the_title(), ENT_QUOTES, get_bloginfo('charset')),
                'url' => get_permalink(),
            ];
        }
        wp_reset_postdata();
        wp_send_json_success($results);
    }
}
new HJ_Elementor_Search_Widget_Plugin();
