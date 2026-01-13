<?php
/**
 * Plugin Meta Class File.
 * 
 * Handles: Plugin row meta links and Thickbox modal rendering
 *
 * @since 1.0.0
 * @package Website_Search
 */

if (!defined('ABSPATH')) {
    exit;
}

class Website_Search_Plugin_Meta
{
    /**
     * Constructor.
     * 
     * Registers hooks for plugin meta row and admin UI.
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        add_filter('plugin_row_meta', array($this, 'sdw_add_view_details_link'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'sdw_load_thickbox'));
        add_action('admin_footer', array($this, 'sdw_render_details_modal'));
    }

    /**
     * Load Thickbox only on Plugins page
     * 
     * $hook args 
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_load_thickbox($hook) {
        if ($hook === 'plugins.php') {
            add_thickbox();
        }
    }

    /**
     * Add "View Details" link to plugin row meta
     * 
     * @param $links TYPE its work
     * @param @file TYPE its work
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_add_view_details_link($links, $file) {
        if ($file === plugin_basename(WEBSITE_SEARCH_FILE)) {
            $links[] = sprintf(
                '<a href="%s" class="thickbox" rel="noopener">%s</a>',
                esc_url('#TB_inline?width=600&height=550&inlineId=website-search-details'),
                esc_html__('View details', 'website-search')
            );
        }
        return $links;
    }

    /**
     * Render Thickbox modal HTML in admin footer
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_render_details_modal() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'plugins') {
            return;
        }
        ?>
        <div id="website-search-details" style="display:none;">
            <h2><?php esc_html_e( 'Website Search', 'website-search' ); ?></h2>

            <p>
                <?php esc_html_e(
                    'Website Search enables a global search across your entire website, including posts, pages, and custom post types. You can place the search interface anywhere on your site using the shortcode options below.',
                    'website-search'
                ); ?>
            </p>

            <h3><?php esc_html_e( 'Shortcode Usage', 'website-search' ); ?></h3>

            <p>
                <?php esc_html_e(
                    'Use the shortcode below to display a simple search form:',
                    'website-search'
                ); ?>
            </p>
            <code>[website_search]</code>

            <p>
                <?php esc_html_e(
                    'Use this shortcode to display a button that opens the search form in a popup (modal):',
                    'website-search'
                ); ?>
            </p>
            <code>[website_search variant="modal"]</code>

            <p>
                <?php esc_html_e(
                    'You can also customize the modal button text using the button_text attribute:',
                    'website-search'
                ); ?>
            </p>
            <code>[website_search variant="modal" button_text="Find Content"]</code>

        </div>
        <?php
    }
}
