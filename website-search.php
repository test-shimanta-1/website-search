<?php
/**
 * Plugin Name: Website Search
 * Description: WordPress Site Search Plugin Accross Post-Types, Pages, CPT etc.
 * Text Domain: website-search
 * Author:      Sundew Team
 * Author URI:  https://sundewsolutions.com
 * 
 * 
 * Version: 1.0.1
 */

if (!defined('ABSPATH')) exit;

define('WEBSITE_SEARCH_VERSION', '1.0.1');
define('WEBSITE_SEARCH_PATH', plugin_dir_path(__FILE__));
define('WEBSITE_SEARCH_URL', plugin_dir_url(__FILE__));
define('WEBSITE_SEARCH_FILE', __FILE__);

require_once WEBSITE_SEARCH_PATH . 'includes/class-website-search.php';

function run_website_search() {
    $plugin = new Website_Search();
    $plugin->run();
}
run_website_search();

