<?php
/*
 * Plugin Name:       Awesome Google Review
 * Plugin URI:        https://beardog.digital/
 * Description:       Impresses with top-notch service and skilled professionals. A 5-star destination for grooming excellence!
 * Version:           1.0
 * Requires PHP:      7.2
 * Author:            #beaubhavik
 * Author URI:        https://beardog.digital/
 * Text Domain:       awesome-google-review
 */

define('AGR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AGR_PLUGIN_URL', plugin_dir_url(__FILE__));

// PLUGIN CHECKER = START
require_once 'update-checker/update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/beaudigital/awesome-google-review/',
    __FILE__,
    'awesome-google-review'
);
$myUpdateChecker->setBranch('main');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
// PLUGIN CHECKER = STOP

function get_dynamic_version()
{
    return time(); // Using the current timestamp as the version number
}
// Enqueue = START
function our_load_admin_style()
{
    global $pagenow;

    if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'awesome-google-review') {
        // Enqueue jQuery
        wp_enqueue_script('jquery');

        $dynamic_version = get_dynamic_version();

        // Enqueue Styles
        wp_register_style('agr_style_css', plugins_url('/assets/css/style.css', __FILE__), [], $dynamic_version);
        wp_enqueue_style('agr_style_css');

        wp_register_style('agr-toastr-mincss', plugins_url('/assets/css/toastr.min.css', __FILE__), [], $dynamic_version);
        wp_enqueue_style('agr-toastr-mincss');

        // Enqueue Scripts with Dependencies
        wp_enqueue_script('agr-toastr-minjs', plugins_url('/assets/js/toastr.min.js', __FILE__), ['jquery'], $dynamic_version, true);
        wp_enqueue_script('agr-ajax-script', plugins_url('/assets/js/agr_ajax.js', __FILE__), ['jquery'], $dynamic_version, true);

        // Localize Script
        wp_localize_script('agr-ajax-script', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);

        // Enqueue Custom Script with Dependencies
        wp_register_script('agr_custom', plugins_url('/assets/js/custom.js', __FILE__), ['jquery'], $dynamic_version, true);
        wp_enqueue_script('agr_custom');
    }
}
add_action('admin_enqueue_scripts', 'our_load_admin_style');
// Enqueue = END

// Include admin panel files.
require_once AGR_PLUGIN_PATH . 'assets/inc/admin_panel.php';

function our_ajax_function()
{
    $response = [];
    $response['success'] = 0;
    $response['data'] = array();
    $response['msg'][] = '';
    $nonce = $_POST['nonce'];
    if (!empty($nonce) && wp_verify_nonce($nonce, 'awesome_google_review')) {
        $place_id = $_POST['place_id'];
        if (isset($place_id)) {
            add_option('place_id', $place_id);
            if (get_option('place_id') !== false) {
                update_option('place_id', $place_id);
            }
            $response['data']['place_id'] = get_option('place_id');
            $response['msg'] = 'Reviews added...';
            $response['success'] = 1;
            $reviews_array = get_reviews_data($place_id);
            if (!empty($reviews_array)) {
                store_data_into_reviews($reviews_array);                
            }            
        } else {
            $response['msg'] = 'Something went wrong !';           
        }
    } else {
        $response['msg'] = 'Nonce is not valid, handle accordingly !';       
    }

    wp_send_json($response);
    wp_die();
}

add_action('wp_ajax_our_ajax_action', 'our_ajax_function');
add_action('wp_ajax_nopriv_our_ajax_action', 'our_ajax_function');

function get_reviews_data($place_id) {
    // Replace 'YOUR_API_URL' with the actual API URL
    $api_url = 'https://service-reviews-ultimate.elfsight.com/data/reviews?uris%5B%5D='.$place_id.'&with_text_only=1&min_rating=5&page_length=5&order=date';

    // Make the API request
    $response = wp_remote_get($api_url);

    // Check for errors
    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    }

    // Parse the JSON response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    // Check if the JSON decoding was successful
    if (is_array($data['result']['data'])) {
        return $data['result']['data'];
    }
}


function get_post_id_by_meta($meta_key, $meta_value, $post_type = 'agr_google_review') {
    $args = array(
        'post_type'  => $post_type,
        'meta_key'   => $meta_key,
        'meta_value' => $meta_value,
        'fields'     => 'ids',
    );

    $posts = get_posts($args);

    if (!empty($posts)) {
        return $posts[0];
    }

    return 0;
}

function store_data_into_reviews($reviews_array) {
    foreach ($reviews_array as $get_review) {
        $id = $get_review['id'];
        $reviewer_name = $get_review['reviewer_name'];
        $reviewer_picture_url = $get_review['reviewer_picture_url'];
        $reviewer_read_more = $get_review['url'];
        $rating = $get_review['rating'];
        $text = $get_review['text'];
        $published_at = $get_review['published_at'];

        // Check if the post with the given ID exists
        // $existing_post_id = get_post_meta($id, 'post_review_id', true);

        // Check if the post with the given ID exists based on custom meta field
        $existing_post_id = get_post_id_by_meta('post_review_id', $id, 'agr_google_review');

        $post_data = array(
            'post_title'    => $reviewer_name,       
            'post_type'     => 'agr_google_review',
            'post_status'   => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        // If post with ID exists, update the post
        if ($existing_post_id) {
            $post_data['ID'] = $existing_post_id;
            $new_post_id = wp_update_post($post_data);
        } else {
            // Otherwise, insert a new post
            $new_post_id = wp_insert_post($post_data);
        }

        // Update the post meta with the review ID
        if ($new_post_id) {
            update_post_meta($new_post_id, 'post_review_id', $id);
            update_post_meta($new_post_id, 'reviewer_name', $reviewer_name);
            update_post_meta($new_post_id, 'reviewer_picture_url', $reviewer_picture_url);
            update_post_meta($new_post_id, 'url', $reviewer_read_more);
            update_post_meta($new_post_id, 'rating', $rating);
            update_post_meta($new_post_id, 'text', $text);
            update_post_meta($new_post_id, 'publish_date', $published_at);
        }

    }

    return true;
}



