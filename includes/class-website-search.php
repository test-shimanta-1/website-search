<?php
/**
 * Website search main class file
 * 
 * @since 1.0.0
 * @package Website_Search
 */

if (!defined('ABSPATH')) {
    exit;
}

class Website_Search{
    /**
     * Construct
     * 
     * load all the dependencies for the plugin
     * 
     * @since 1.0.0
     * @return void
     * */ 
    public function __construct() {
        $this->load_dependencies();
    }

     /**
     * Load required plugin class files.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_dependencies() {
        require_once WEBSITE_SEARCH_PATH . 'includes/class-website-search-row-meta.php';
        require_once WEBSITE_SEARCH_PATH . 'includes/class-websiste-search-shortcode.php';
    }

    /**
     * Run the plugin.
     *
     * Initializes all core plugin classes.
     *
     * @since 1.0.0
     * @return void
     */
    public function run(){
        new Website_Search_Plugin_Meta();
        new Website_Search_Shortcode();
    }
}
