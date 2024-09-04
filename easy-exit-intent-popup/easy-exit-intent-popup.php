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
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_exit_intent_popup_settings" />
          <?php
          wp_nonce_field('exit_intent_popup_save', 'exit_intent_popup_nonce');
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

// Handle form submission and file upload
add_action('admin_post_save_exit_intent_popup_settings', 'wp_exit_intent_popup_handle_upload');

function wp_exit_intent_popup_handle_upload() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verify nonce
    if (!isset($_POST['exit_intent_popup_nonce']) || !wp_verify_nonce($_POST['exit_intent_popup_nonce'], 'exit_intent_popup_save')) {
        return;
    }

    // Handle file upload
    if (isset($_FILES['exit_popup_image']) && !empty($_FILES['exit_popup_image']['tmp_name'])) {
        $uploaded_file = $_FILES['exit_popup_image'];
        $upload = wp_handle_upload($uploaded_file, array('test_form' => false));

        if ($upload && !isset($upload['error'])) {
            $uploaded_image_url = $upload['url'];
            if (!empty($uploaded_image_url)) {
                update_option('exit_popup_image_url', $uploaded_image_url);
            }
        } else {
            error_log('File upload error: ' . $upload['error']);
        }
    }

    // Redirect back to settings page
    wp_redirect(admin_url('options-general.php?page=wp-exit-intent-popup'));
    exit;
}

// Register and display settings field
function wp_exit_intent_popup_image_callback() {
    $image_url = get_option('exit_popup_image_url');
    echo '<input type="file" name="exit_popup_image" />';
    if ($image_url) {
        echo '<br><img src="' . esc_url($image_url) . '" style="max-width:200px;" />';
    }
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
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7); /* Grayed background */
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            }
            #exit-intent-popup img {
                max-height: 80%;
                max-width: 80%;
            }
          </style>';
}


function wp_exit_intent_popup_html() {
    $image_url = get_option('exit_popup_image_url');
    echo '
    <div id="exit-intent-popup"><img src="'. $image_url .'"></div>';
}


add_action('wp_footer','wp_exit_intent_popup_html');
