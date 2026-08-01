<?php
/**
 * Plugin Name:       Cliqnfix AJAX Search
 * Description:       Adds AJAX functionality to the theme's searchform.php file.
 * Version:           1.4 (Theme Integration)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Cliqnfix_Ajax_Search_Plugin {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_my_ajax_search', array($this, 'ajax_search_handler'));
        add_action('wp_ajax_nopriv_my_ajax_search', array($this, 'ajax_search_handler'));
        add_action('pre_get_posts', array($this, 'protect_ajax_search_query'), 99);
    }

    public function enqueue_scripts() {
        wp_enqueue_style('ajax-search-style', plugin_dir_url(__FILE__) . 'css/ajax-search.css', array(), '1.4');
        wp_enqueue_script('ajax-search-script', plugin_dir_url(__FILE__) . 'js/ajax-search.js', array('jquery'), '1.4', true);
        wp_localize_script('ajax-search-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('ajax-search-nonce')
        ));
    }

    public function ajax_search_handler() {
        check_ajax_referer('ajax-search-nonce', 'nonce');
        $search_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        if (empty($search_query) || strlen($search_query) < 2) { wp_die(); }
        $args = array( 'post_type' => 'post', 'post_status' => 'publish', 's' => $search_query, 'posts_per_page' => 5, 'is_cliqnfix_ajax' => true );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                printf('<div class="search-result-item"><a href="%s"><h4 class="result-title">%s</h4><div class="result-excerpt">%s</div></a></div>', esc_url(get_permalink()), get_the_title(), get_the_excerpt());
            }
        } else {
            echo '<div class="no-results">No posts found.</div>';
        }
        wp_reset_postdata();
        wp_die();
    }

    public function protect_ajax_search_query($query) {
        if ( isset($query->query_vars['is_cliqnfix_ajax']) && $query->query_vars['is_cliqnfix_ajax'] ) {
            $query->set('post_type', 'post');
        }
    }
}
new Cliqnfix_Ajax_Search_Plugin();