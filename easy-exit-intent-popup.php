<?php
/*
Plugin Name: Easy Exit Intent Popup
Description: A super simple and quick to use, FREE plugin that shows an exit intent popup with an uploaded image. Doesn't require creating any external accounts.
Version: 1.0
Author: StaszeG
Author URI: https://staszeg.net
License: GPL2
*/

add_action('admin_menu', 'wp_exit_intent_popup_menu');

function wp_exit_intent_popup_menu() {
    add_options_page(
        'Exit Intent Popup Settings',
        'Exit Intent Popup',
        'manage_options',
        'wp-exit-intent-popup',
        'wp_exit_intent_popup_settings_page'
    );
}

function wp_exit_intent_popup_settings_page() {
  ?>
  <div class="wrap">
      <h1>Exit Intent Popup Settings</h1>
      <form method="post" action="options.php" enctype="multipart/form-data">
          <?php
          settings_fields('wp_exit_intent_popup_options');
          do_settings_sections('wp_exit_intent_popup');
          ?>
          <table class="form-table">
              <tr valign="top">
                  <th scope="row">Upload Popup Image</th>
                  <td>
                      <?php wp_exit_intent_popup_image_callback(); ?>
                  </td>
              </tr>
          </table>
          <?php submit_button(); ?>
      </form>
  </div>
  <?php
}

add_action('admin_init', 'wp_exit_intent_popup_settings');

function wp_exit_intent_popup_settings() {
    register_setting('wp_exit_intent_popup_options', 'exit_popup_image_url');

    add_settings_section(
        'wp_exit_intent_popup_main_section',
        'Main Settings',
        null,
        'wp-exit-intent-popup'
    );

    add_settings_field(
        'wp_exit_intent_popup_image',
        'Popup Image',
        'wp_exit_intent_popup_image_callback',
        'wp-exit-intent-popup',
        'wp_exit_intent_popup_main_section'
    );
}

// Callback function to render the image upload field
function wp_exit_intent_popup_image_callback() {
    $image_url = get_option('exit_popup_image_url');
    echo '<input type="file" name="exit_popup_image" />';
    if ($image_url) {
        echo '<br><img src="' . esc_url($image_url) . '" style="max-width:200px;" />';
    }
}

// Handle the file upload and update the option
add_action('admin_post_save_exit_intent_popup_settings', 'wp_exit_intent_popup_handle_upload');

function wp_exit_intent_popup_handle_upload() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_FILES['exit_popup_image']) && !empty($_FILES['exit_popup_image']['tmp_name'])) {
        $uploaded_file = $_FILES['exit_popup_image'];
        $upload = wp_handle_upload($uploaded_file, array('test_form' => false));

        if ($upload && !isset($upload['error'])) {
            $uploaded_image_url = $upload['url'];
            if (!empty($uploaded_image_url)) {
                update_option('exit_popup_image_url', $uploaded_image_url);
            }
        } else {
            // Handle the upload error
            error_log('File upload error: ' . $upload['error']);
        }
    }

    wp_redirect(admin_url('options-general.php?page=wp-exit-intent-popup'));
    exit;
}

add_action('wp_enqueue_scripts', 'wp_exit_intent_popup_enqueue_scripts');

function wp_exit_intent_popup_enqueue_scripts() {
    wp_enqueue_script('wp-exit-intent-popup-js', plugins_url('exit-intent-popup.js', __FILE__), array('jquery'), null, true);

    $popup_image_url = get_option('exit_popup_image_url');
    wp_localize_script('wp-exit-intent-popup-js', 'exitPopupData', array(
        'popupImage' => $popup_image_url,
    ));
}

add_action('wp_head', 'wp_exit_intent_popup_css');

function wp_exit_intent_popup_css() {
    echo '<style>
            #exit-intent-popup {
                display: none;
                background: rgba(0, 0, 0, 0.7);
                width: 300px;
                height: 300px;
                text-align: center;
                padding: 20px;
                color: #fff;
            }
            #exit-intent-popup img {
                max-width: 100%;
                height: auto;
            }
          </style>';
}
