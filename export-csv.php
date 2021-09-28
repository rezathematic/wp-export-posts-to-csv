<?php

/**
 * Export All roundups as CSV file
 */

// Create the button and add it to the admin navbar
function admin_reoundups_export_button($which)
{
    global $typenow;

    if ('top-picks' === $typenow && 'top' === $which) {
?>
        <input type="submit" name="export_all_roundups" class="button button-primary" value="<?php _e('Export All Roundups'); ?>" />
<?php
    }
}

add_action('manage_posts_extra_tablenav', 'admin_reoundups_export_button', 20, 1);


// Function to export the .csv file
function export_all_roundups()
{
    if (isset($_GET['export_all_roundups'])) {
        $arg = array(
            'post_type' => 'top-picks',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        global $post;
        $arr_post = get_posts($arg);
        if ($arr_post) {

            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="bar-roundups.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $file = fopen('php://output', 'w');

            fputcsv($file, array('Post Title', 'URL', 'Categories', 'Tags'));

            foreach ($arr_post as $post) {
                setup_postdata($post);

                $categories = get_the_category();
                $cats = array();
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        $cats[] = $category->name;
                    }
                }

                $post_tags = get_the_tags();
                $tags = array();
                if (!empty($post_tags)) {
                    foreach ($post_tags as $tag) {
                        $tags[] = $tag->name;
                    }
                }

                fputcsv($file, array(get_the_title(), get_the_permalink(), implode(",", $cats), implode(",", $tags)));
            }

            exit();
        }
    }
}

add_action('init', 'export_all_roundups');
