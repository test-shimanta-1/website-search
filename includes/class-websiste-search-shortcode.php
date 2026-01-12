<?php
/**
 * Plugin Shortcode Class File.
 * 
 * handels wordpress site global search accross posts, pages, cpt etc.
 *
 * @since 1.0.0
 * @package Website_Search
 */

if (!defined('ABSPATH')) {
    exit;
}

class Website_Search_Shortcode
{
    /**
     * Constructor.
     * 
     * loads all the shortcode related dependencies, get search results
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct()
    {
        add_shortcode('website_search', [$this, 'sdw_website_search_short_code_callback']);

        add_action('init', [$this, 'sdw_website_search_rewrite']);
        add_action('query_vars', [$this, 'sdw_website_search_query_vars'], 10, 1);
        add_action('pre_get_posts', [$this, 'sdw_pre_get_posts_callback']);
        add_filter('template_include', [$this, 'sdw_load_website_search_template']);
    }

    /**
     * shortcode callback function
     * 
     * It helps to display button on-click search modal
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_website_search_short_code_callback($atts)
    {
        $atts = shortcode_atts(
            array(
                'variant' => 'form',        // form | modal
                'button_text' => 'Search',  // modal button text
            ),
            $atts,
            'website_search'
        );

        ob_start();
        // FORM ONLY (default)
        if ($atts['variant'] !== 'modal'): ?>
            <form method="get" action="<?php echo esc_url(home_url('/search/')); ?>">
                <input type="text" name="q" class="form-control"
                    placeholder="<?php esc_attr_e('Enter your search query', 'website-search'); ?>" required>
                <button type="submit" class="btn btn-primary">
                    <?php esc_html_e('Search', 'website-search'); ?>
                </button>
            </form>

            <?php
            // MODAL VARIANT
            else:
                $button_text = esc_html($atts['button_text']);
                ?>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sdwSearchModal">
                    <?php echo $button_text; ?>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="sdwSearchModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <?php esc_html_e('Search in website', 'website-search'); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form method="get" action="<?php echo esc_url(home_url('/search/')); ?>">
                                    <input type="text" name="q" class="form-control mb-2"
                                        placeholder="<?php esc_attr_e('Enter your search query', 'website-search'); ?>" required>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <?php esc_html_e('Search', 'website-search'); ?>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
        <?php endif;
        return ob_get_clean();
    }


    /**
     * responsible to get search queries
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_get_search_results($query)
    {
        if ($query->is_search() && !is_admin() && $query->is_main_query()) {
            $post_types = get_post_types(['public' => true], 'names');
            $query->set('post_type', $post_types);
        }
    }

    public function sdw_website_search_rewrite()
    {
        add_rewrite_rule('^search/?$', 'index.php?sdw_search_page=1', 'top');
    }

    public function sdw_website_search_query_vars($vars)
    {
        $vars[] = 'sdw_search_page';
        $vars[] = 'q';
        return $vars;
    }

    public function sdw_pre_get_posts_callback($query)
    {
        if (!is_admin() && $query->is_main_query() && get_query_var('sdw_search_page')) {
            $search_term = get_query_var('q');

            if ($search_term) {
                $query->set('s', sanitize_text_field($search_term));
            }

            // Include all public post types except media
            $post_types = get_post_types(['public' => true], 'names');
            unset($post_types['attachment']);

            $query->set('post_type', $post_types);
        }
    }

    public function sdw_load_website_search_template($template)
    {
        if (get_query_var('sdw_search_page')) {
            return WEBSITE_SEARCH_PATH . 'templates/search-results.php';
        }
        return $template;
    }

}