<?php
/**
 * Website search main class file
 * 
 * @since 1.0.0
 * @package Website_Search
 */
class Website_Search{
    /**
     * Construct
     * 
     * load all the dependencies for the plugin
     * */ 
    public function __construct() {
        $this->load_dependencies();
    }

    public function load_dependencies() {
        require_once WEBSITE_SEARCH_PATH . 'includes/class-website-search-row-meta.php';
        require_once WEBSITE_SEARCH_PATH . 'includes/class-websiste-search-shortcode.php';
    }

    public function run(){
        new Website_Search_Plugin_Meta();
        new Website_Search_Shortcode();
    }
}
