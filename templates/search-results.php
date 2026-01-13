<?php
get_header();

global $wp_query;
?>

<div class="sdw-search-wrapper container">

    <h1>
        Search results for:
        <strong><?php echo esc_html(get_query_var('keys')); ?></strong>
    </h1>
     <font><?php  echo $wp_query->found_posts.' results found.'; ?></font>
     <br>
      <div class="row">
        <div class="col-4">
            <form method="get" action="<?php echo esc_url(home_url('/search/content')); ?>">
                <input type="text" name="keys" class="form-control" placeholder="Enter your search query" value="<?php echo esc_attr(get_query_var('keys')); ?>" required>
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
            <?php the_posts_pagination(); ?>
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
