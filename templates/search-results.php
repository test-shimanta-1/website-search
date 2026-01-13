<?php
get_header();

global $wp_query;
$search_term = get_query_var('keys');
$current_page = get_query_var('page') ? absint(get_query_var('page')) : 1;


?>

<div class="sdw-search-wrapper container">

    <h1>
        Search results for:
        <strong><?php echo esc_html($search_term); ?></strong>
    </h1>
    <p class="search-count">
        <?php echo esc_html($wp_query->found_posts . ' results found.'); ?>
    </p>
    <br>
    
    <div class="row">
        <div class="col-4">
            <form method="get" action="<?php echo esc_url(home_url('/search/content')); ?>">
                <input type="text" name="keys" class="form-control" 
                       placeholder="Enter your search query" 
                       value="<?php echo esc_attr($search_term); ?>" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-8"></div>
    </div>
    <hr>

    <?php if (have_posts()) : ?>

        <ul class="sdw-search-results">
            <?php while (have_posts()) : the_post(); ?>
                <li class="sdw-search-item">
                    <h2>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div class="sdw-search-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="sdw-pagination">
            <?php
            echo paginate_links([
                'base'     => home_url('/search/content') . '?keys=' . urlencode($search_term) . '%_%',
                'format'   => '&page=%#%',
                'current'  => max(1, get_query_var('page')),
                'total'    => $wp_query->max_num_pages,
            ]);
            ?>
        </div>

    <?php else : ?>

        <div class="sdw-no-results">
            <h2>No results found</h2>
            <p>Please try a different keyword.</p>
        </div>

    <?php endif; ?>

</div>

<?php
get_footer();