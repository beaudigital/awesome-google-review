<?php
// error_reporting(0);

// Add a setting page
function our_google_reviews_add_menu_page()
{
    add_menu_page(
        'Our Google Reviews',
        'Review Settings',
        'manage_options',
        'awesome-google-review',
        'our_google_reviews_callback',
        'dashicons-google',
        null,
    );
}
add_action('admin_menu', 'our_google_reviews_add_menu_page');

// Setting Link at plugin
function add_settings_link($links, $file)
{
    if (strpos($file, 'awesome-google-review/awesome-google-review.php') !== false) {
        $settings_link = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=awesome-google-review')) . '">' . __('Review Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'add_settings_link', 10, 2);


// Create post type on activation
register_activation_hook(__FILE__, 'awesome_google_review_plugin_activate');

function awesome_google_review_plugin_activate()
{
    add_agr_google_review_post_type();
    flush_rewrite_rules();
}

//Hide post type on deactivation
register_deactivation_hook(__FILE__, 'awesome_google_review_plugin_deactivate');
function awesome_google_review_plugin_deactivate()
{
    unregister_post_type('agr_google_review');
    flush_rewrite_rules();
}

//Remove place_id data on deletion
register_uninstall_hook(__FILE__, 'awesome_google_review_plugin_uninstall');
function awesome_google_review_plugin_uninstall()
{
    delete_option('place_id');
}

add_action('init', 'add_agr_google_review_post_type');
function add_agr_google_review_post_type()
{
    $labels = array(
        'name'               => _x('Google Reviews', 'post type general name', 'awesome-google-review'),
        'singular_name'      => _x('Google Review', 'post type singular name', 'awesome-google-review'),
        'menu_name'          => _x('Google Reviews', 'admin menu', 'awesome-google-review'),
        'name_admin_bar'     => _x('Google Review', 'add new on admin bar', 'awesome-google-review'),
        'add_new'            => _x('Add New', 'Google Review', 'awesome-google-review'),
        'add_new_item'       => __('Add New Google Review', 'awesome-google-review'),
        'new_item'           => __('New Google Review', 'awesome-google-review'),
        'edit_item'          => __('Edit Google Review', 'awesome-google-review'),
        'view_item'          => __('View Google Review', 'awesome-google-review'),
        'all_items'          => __('All Google Reviews', 'awesome-google-review'),
        'search_items'       => __('Search Google Reviews', 'awesome-google-review'),
        'parent_item_colon'  => __('Parent Google Reviews:', 'awesome-google-review'),
        'not_found'          => __('No Google Reviews found.', 'awesome-google-review'),
        'not_found_in_trash' => __('No Google Reviews found in Trash.', 'awesome-google-review'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'google-reviews'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'menu_icon' => 'dashicons-google',
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array(''),
    );

    register_post_type('agr_google_review', $args);

    add_action('add_meta_boxes', 'add_agr_google_review_meta_box');
}

function add_agr_google_review_meta_box()
{
    add_meta_box(
        'agr_google_review_meta_box',
        __('Google Review Details', 'awesome-google-review'),
        'render_agr_google_review_meta_box',
        'agr_google_review',
        'normal',
        'default'
    );
}

function render_agr_google_review_meta_box($post)
{
    // Retrieve the current values of meta fields
    $place_id = 'ChIJCQMc8zh-woARPw85TTugs1w';
    $id = get_post_meta($post->ID, 'post_review_id', true);
    $reviewer_name = get_post_meta($post->ID, 'reviewer_name', true);
    $reviewer_picture_url = get_post_meta($post->ID, 'reviewer_picture_url', true);
    $url = get_post_meta($post->ID, 'url', true);
    $rating = get_post_meta($post->ID, 'rating', true);
    $text = get_post_meta($post->ID, 'text', true);
    $publish_date = date_i18n('d-M-Y', get_post_meta($post->ID, 'publish_date', true));


    // Output the second table for Place ID on the right side
    echo '<table class="form-table">';
    echo '<tr><th>' . __('Place ID:', 'awesome-google-review') . '</th><td><input style="background:#ccc;" readonly type="text" id="place_id" name="place_id" value="' . esc_attr($place_id) . '" /></td></tr>';
    echo '</table>';

    // Output a table
    echo '<table class="form-table">';

    echo '<tr><th>' . __('ID:', 'awesome-google-review') . '</th><td><input readonly type="text" id="post_review_id" name="post_review_id" value="' . esc_attr($id) . '" /></td></tr>';
    echo '<tr><th>' . __('Reviewer Name:', 'awesome-google-review') . '</th><td><input readonly type="text" id="reviewer_name" name="reviewer_name" value="' . esc_attr($reviewer_name) . '" /></td></tr>';
    echo '<tr><th>' . __('Reviewer Picture URL:', 'awesome-google-review') . '</th><td><input readonly type="text" id="reviewer_picture_url" name="reviewer_picture_url" value="' . esc_url($reviewer_picture_url) . '" /></td></tr>';
    echo '<tr><th>' . __('Read More URL:', 'awesome-google-review') . '</th><td><input readonly type="text" id="url" name="url" value="' . esc_url($url) . '" /></td></tr>';
    echo '<tr><th>' . __('Rating:', 'awesome-google-review') . '</th><td><input readonly type="number" id="rating" name="rating" value="' . esc_attr($rating) . '" min="1" max="5" /></td></tr>';
    echo '<tr><th>' . __('Description:', 'awesome-google-review') . '</th><td><textarea readonly id="text" name="text" rows="4" cols="50">' . esc_textarea($text) . '</textarea></td></tr>';
    echo '<tr><th>' . __('Publish Date:', 'awesome-google-review') . '</th><td><input readonly type="text" id="publish_date" name="publish_date" value="' . esc_attr($publish_date) . '" /></td></tr>';
    echo '</table>';
}

function our_google_reviews_callback()
{
    // $get_place_id = trim(get_option('place_id'));
    $get_place_id = get_option('place_id');
?>
    <div class="seo-plugin-data-info container">
        <div class="inner-content-data">
            <h2 class="boxtitle ">Google Reviews Setting</h2>
            <form id="agr_ajax_form" method="post">
                <?php wp_nonce_field('awesome_google_review', 'awesome_google_review_nonce'); ?>
                <div class="field_container">
                    <div class="input-field">
                        <input type="text" id="place_id" required spellcheck="false" value="<?php echo ($get_place_id ? $get_place_id : ''); ?>">
                        <label>Place ID</label>
                    </div>
                    <div class="select-field">
                        <select name="get_review_count">
                            <option value="option1">5</option>
                            <option value="option2">10</option>                            
                        </select>
                    </div>
                </div>

                <button type="submit" class="submit_btn"><span class="label">Submit</span> <span class="spinner"></span></button>
            </form>
        </div>
    </div>
<?php
}


// Add custom columns to post type
function custom_add_custom_columns($columns)
{

    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            // Add the custom columns after the title
            $new_columns['rating'] = 'Rating';
            $new_columns['read_more'] = 'URL';
            $new_columns['publish_date'] = 'Review Date';
        }
    }
    return $new_columns;
}
add_filter('manage_agr_google_review_posts_columns', 'custom_add_custom_columns');

// Display custom meta values in the custom columns
function custom_display_custom_columns($column, $post_id)
{
    switch ($column) {
        case 'rating':
            $rating = get_post_meta($post_id, 'rating', true);
            echo esc_html($rating);
            break;

        case 'read_more':
            $read_more_url = get_post_meta($post_id, 'url', true);
            echo '<a href="' . esc_url($read_more_url) . '" target="_blank">' . esc_html__('Read More', 'your_text_domain') . '</a>';
            break;

        case 'publish_date':
            $publish_date = get_post_meta($post_id, 'publish_date', true);
            $formatted_date = date_i18n('d-M-Y', $publish_date);
            echo esc_html($formatted_date);
            break;
    }
}
add_action('manage_agr_google_review_posts_custom_column', 'custom_display_custom_columns', 16, 2);



?>