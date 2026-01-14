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
        add_shortcode('website_search', [$this, 'sdw_website_search_short_code_callback']); // search box ui callback

        add_action('init', [$this, 'sdw_website_search_rewrite']); // add '/search' before search params
        add_filter('user_trailingslashit', [$this, 'sdw_remove_trailing_slash_for_search'], 10, 2); // remove default trail '/' from the URL
        add_action('query_vars', [$this, 'sdw_website_search_query_vars'], 10, 1); // overwriting wordpress query behaviour s -> q
        add_action('pre_get_posts', [$this, 'sdw_pre_get_posts_callback']); // get search results
        add_filter('template_include', [$this, 'sdw_load_website_search_template']); // search template

        add_filter('posts_join', [$this, 'sdw_posts_join'], 10, 2); // union all the search results
        add_filter('posts_search', [$this, 'sdw_posts_search'], 10, 2); // search into post meta & taxonomy
        add_filter('posts_groupby', [$this, 'sdw_posts_groupby'], 10, 2); // group by all the 
    }

    /**
     * responsible to remove / before ?keys=
     * 
     * @since 1.0.1
     * @return void
     */
    public function sdw_remove_trailing_slash_for_search($url, $type)
    {
        if (strpos($url, '/search/content/') !== false) {
            return untrailingslashit($url);
        }
        return $url;
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
            <form method="get" action="<?php echo esc_url(home_url('/search/content')); ?>">
                <input type="text" name='keys' class="form-control"
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
                            <form method="get" action="<?php echo esc_url(home_url('/search/content')); ?>">
                                <input type="text" name='keys' class="form-control mb-2"
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
     * Register custom rewrite rules for search URLs.
     *
     * @since 1.0.0
     * @return void
     */
    public function sdw_website_search_rewrite()
    {
        add_rewrite_rule('^search/content?$','index.php?sdw_search_page=1','top');
    }

    /**
     * Register custom query variables.
     *
     * @param array $vars Existing query vars.
     * 
     * @since 1.0.1
     * @return array Modified query vars.
     */
    public function sdw_website_search_query_vars($vars)
    {
        $vars[] = 'sdw_search_page';
        $vars[] = 'keys';
        return $vars;
    }
    
    /**
     * Modify main query for custom search page.
     *
     * @param WP_Query $query Main query object.
     * 
     * @since 1.0.1
     * @return void
     */
    public function sdw_pre_get_posts_callback($query)
    {
        if (is_admin() || !$query->is_main_query() || !get_query_var('sdw_search_page')) {
            return;
        }

        $search_term = sanitize_text_field(get_query_var('keys'));
        if (!$search_term) {
            return;
        }

        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
        $query->set('paged', max(1, get_query_var('page')));

        // Native WP search (title + content)
        $query->set('s', $search_term);

        // All public post types
        $post_types = get_post_types(['public' => true], 'names');
        unset($post_types['attachment']);
        $query->set('post_type', $post_types);
    }

     /**
     * Extend search SQL to include meta and taxonomy terms.
     *
     * @param string   $search Existing search SQL.
     * @param WP_Query $query  Query object.
     * 
     * @since 1.0.1
     * @return string Modified search SQL.
     */
    public function sdw_posts_search($search, $query)
    {
        global $wpdb;

        if (!$query->is_main_query() || !get_query_var('sdw_search_page')) {
        return $search;
        }

        $term = get_query_var('keys');
        if (!$term) {
        return $search;
        }

        $like = '%' . $wpdb->esc_like($term) . '%';

        // Remove default search and rebuild it safely
        $search = $wpdb->prepare(
        " AND (
        {$wpdb->posts}.post_title LIKE %s
        OR {$wpdb->posts}.post_content LIKE %s
        OR pm.meta_value LIKE %s
        OR t.name LIKE %s
        ) ",
        $like,
        $like,
        $like,
        $like
        );
        return $search;
    }

    /**
     * Join postmeta and taxonomy tables for search.
     *
     * @param string   $join  Existing SQL JOIN clause.
     * @param WP_Query $query Query object.
     * 
     * @since 1.0.1
     * @return string Modified JOIN clause.
     */        
    public function sdw_posts_join($join, $query)
    {
        global $wpdb;
        if (!$query->is_main_query() || !get_query_var('sdw_search_page')) {
            return $join;
        }

        // Join postmeta (ALL ACF fields)
        if (strpos($join, $wpdb->postmeta) === false) {
            $join .= " LEFT JOIN {$wpdb->postmeta} pm ON ({$wpdb->posts}.ID = pm.post_id) ";
        }

        // Join taxonomy tables (ALL taxonomies)
        if (strpos($join, $wpdb->terms) === false) {
            $join .= "
            LEFT JOIN {$wpdb->term_relationships} tr ON ({$wpdb->posts}.ID = tr.object_id)
            LEFT JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
        ";
        }

        return $join;
    }

    /**
     * Group search results by post ID.
     *
     * @param string   $groupby Existing GROUP BY clause.
     * @param WP_Query $query   Query object.
     * 
     * @since 1.0.1
     * @return string Modified GROUP BY clause.
     */
    public function sdw_posts_groupby($groupby, $query)
    {
        global $wpdb;
        if (!$query->is_main_query() || !get_query_var('sdw_search_page')) {
            return $groupby;
        }
        return "{$wpdb->posts}.ID";
    }

    /**
     * Load custom search results template.
     *
     * @param string $template Current template path.
     * 
     * @since 1.0.1
     * @return string Template path to load.
     */        
    public function sdw_load_website_search_template($template)
    {
        if (get_query_var('sdw_search_page')) {
            return WEBSITE_SEARCH_PATH . 'templates/search-results.php';
        }
        return $template;
    }

}