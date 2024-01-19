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
    echo '<table class="form-table" style="border-bottom:1px solid #c3c4c7">';
    echo '<tr><th>' . __('Place ID:', 'awesome-google-review') . '</th><td><input style="background:#ccc;" readonly type="text" id="place_id" name="place_id" value="' . esc_attr($place_id) . '" /></td></tr>';
    echo '<tr><th>' . __('ID:', 'awesome-google-review') . '</th><td><input style="background:#ccc;" readonly type="text" id="post_review_id" name="post_review_id" value="' . esc_attr($id) . '" /></td></tr>';
    echo '</table>';

    // Output a table
    echo '<table class="form-table" style="width:auto">';    
    echo '<tr><th>' . __('Reviewer Name:', 'awesome-google-review') . '</th><td><input readonly type="text" id="reviewer_name" name="reviewer_name" value="' . esc_attr($reviewer_name) . '" /></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">reviewer_name</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '<tr><th>' . __('Reviewer Picture URL:', 'awesome-google-review') . '</th><td><input readonly type="text" id="reviewer_picture_url" name="reviewer_picture_url" value="' . esc_url($reviewer_picture_url) . '" /></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">reviewer_picture_url</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '<tr><th>' . __('Read More URL:', 'awesome-google-review') . '</th><td><input readonly type="text" id="url" name="url" value="' . esc_url($url) . '" /></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">url</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '<tr><th>' . __('Rating:', 'awesome-google-review') . '</th><td><input readonly type="number" id="rating" name="rating" value="' . esc_attr($rating) . '" /></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">rating</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '<tr><th>' . __('Description:', 'awesome-google-review') . '</th><td><textarea readonly id="text" name="text" rows="4" cols="23">' . esc_textarea($text) . '</textarea></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">text</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '<tr><th>' . __('Publish Date:', 'awesome-google-review') . '</th><td><input readonly type="text" id="publish_date" name="publish_date" value="' . esc_attr($publish_date) . '" /></td><td><strong>Meta Key</strong> : <span class="meta_key_cls">publish_date</span><span class="copy_text" onclick="copyTextToClipboard(this.previousElementSibling)"> copy</span></td></tr>';
    echo '</table>';

    echo '<style>.copy_text{color:#2271b1;cursor:pointer;} .meta_key_cls{font-weight:700;}</style>';

    echo '<script>
    function copyTextToClipboard(e){const t=e.innerText;if(navigator.clipboard)return navigator.clipboard.writeText(t).then(()=>showNotification("Text copied to clipboard")).catch(()=>showNotification("Error copying text to clipboard"));if(copyTextToClipboardFallback(t))showNotification("Text copied to clipboard !");else showNotification("Error copying text to clipboard")}function copyTextToClipboardFallback(t){const e=document.createElement("textarea");e.value=t,document.body.appendChild(e),e.select(),document.execCommand("copy"),document.body.removeChild(e);return!0}function showNotification(e){alert(e)}
    </script>';        
            
}

function shortcode_display(){
    
}

function our_google_reviews_callback()
{
    // $get_place_id = trim(get_option('place_id'));
    $get_place_id = get_option('place_id');
?>

    <div class="seo-plugin-data-info container">
        <div class="inner-content-data">
            <h2 class="boxtitle ">Google Reviews Setting</h2>
            <form id="agr_ajax_form" method="post" autocomplete="off">
                <?php wp_nonce_field('awesome_google_review', 'awesome_google_review_nonce'); ?>
                <div class="field_container">
                    <div class="input-field">
                        <input type="text" id="place_id" required  spellcheck="false" value="<?php echo ($get_place_id ? $get_place_id : ''); ?>">
                        <label>Place ID</label>
                        <span class="correct-sign">✓</span>
                        <span class="wrong-sign">×</span>
                    </div>
                    <div class="select-field">
                        <select name="get_review_count" id="get_review_count" class="get_review_count">
                            <option value="0" <?php selected(get_option('get_review_count'), '0'); ?>>0</option>
                            <option value="5" <?php selected(get_option('get_review_count'), '5'); ?>>5</option>
                            <option value="10" <?php selected(get_option('get_review_count'), '10'); ?>>10</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="submit_btn"><span class="label">Submit</span></button>
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
            $new_columns['rating'] = '<span style="display: block; text-align: center;">Rating</span>';
            $new_columns['read_more'] = '<span style="display: block; text-align: center;">URL</span>';
            $new_columns['publish_date'] = '<span style="display: block; text-align: center;">Review Date</span>';
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
            $rating_count = get_post_meta($post_id, 'rating', true);
            $stars_html = '<div style="text-align: center;">'; // Centering div
            for ($i = 0; $i < 5; $i++) {
                if ($i < $rating_count) {
                    // Fill star
                    $stars_html .= '<svg width="24px" height="24px" enable-background="new 0 0 64 64" version="1.0" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">	<path d="m63.893 24.277c-0.238-0.711-0.854-1.229-1.595-1.343l-19.674-3.006-8.815-18.778c-0.33-0.702-1.036-1.15-1.811-1.15s-1.48 0.448-1.811 1.15l-8.815 18.778-19.674 3.007c-0.741 0.113-1.356 0.632-1.595 1.343-0.238 0.71-0.059 1.494 0.465 2.031l14.294 14.657-3.378 20.704c-0.124 0.756 0.195 1.517 0.822 1.957 0.344 0.243 0.747 0.366 1.151 0.366 0.332 0 0.666-0.084 0.968-0.25l17.572-9.719 17.572 9.719c0.302 0.166 0.636 0.25 0.968 0.25 0.404 0 0.808-0.123 1.151-0.366 0.627-0.44 0.946-1.201 0.822-1.957l-3.378-20.704 14.294-14.657c0.525-0.538 0.705-1.322 0.467-2.032z" fill="#2271b1"/></svg>';
                } else {
                    // Not-fill star
                    $stars_html .= '<svg width="24px" height="24px" enable-background="new 0 0 64 64" version="1.0" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m32.001 2.484c0.279 0 0.463 0.509 0.463 0.509l8.806 18.759 20.729 3.167-14.999 15.38 3.541 21.701-18.54-10.254-18.54 10.254 3.541-21.701-14.999-15.38 20.729-3.167 8.798-18.743s0.192-0.525 0.471-0.525m0-2.477c-0.775 0-1.48 0.448-1.811 1.15l-8.815 18.778-19.674 3.006c-0.741 0.113-1.356 0.632-1.595 1.343-0.238 0.71-0.059 1.494 0.465 2.031l14.294 14.657-3.378 20.704c-0.124 0.756 0.195 1.517 0.822 1.957 0.344 0.244 0.748 0.367 1.152 0.367 0.332 0 0.666-0.084 0.968-0.25l17.572-9.719 17.572 9.719c0.302 0.166 0.636 0.25 0.968 0.25 0.404 0 0.808-0.123 1.151-0.366 0.627-0.44 0.946-1.201 0.822-1.957l-3.378-20.704 14.294-14.657c0.523-0.537 0.703-1.321 0.465-2.031-0.238-0.711-0.854-1.229-1.595-1.343l-19.674-3.006-8.814-18.779c-0.331-0.702-1.036-1.15-1.811-1.15z" fill="#231F20"/></svg>';
                }
            }
            $stars_html .= '</div>'; // Closing centering div
            echo $stars_html;
            break;
        case 'read_more':
            $read_more_url = get_post_meta($post_id, 'url', true);
            echo '<div style="text-align: center;"><a href="' . esc_url($read_more_url) . '" target="_blank">' . esc_html__('Read More', 'your_text_domain') . '</a></div>';
            break;

        case 'publish_date':
            $publish_date = get_post_meta($post_id, 'publish_date', true);
            $formatted_date = date_i18n('d-M-Y', $publish_date);
            echo '<div style="text-align: center;">' . esc_html($formatted_date) . '</div>';
            break;
    }
}
add_action('manage_agr_google_review_posts_custom_column', 'custom_display_custom_columns', 16, 2);




?>