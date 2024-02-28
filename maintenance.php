<?php
/**
 * WP Maintenance Lite
 *
 * Plugin Name: WP Maintenance Lite
 * Plugin URI:  https://github.com/JosephChuks/wp-maintenance-lite
 * Description: Enable a simple and lightweight Maintenance mode for WordPress. This plugin comes with Language Translator
 * Version:     1.0
 * Author:      Joseph Chuks
 * Author URI:  https://github.com/JosephChuks
 * License:     GPLv2 or later
 * Text Domain: wp-maintenance-lite
 * Requires at least: 4.9
 * Requires PHP: 7.4
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */


define('wp_maintenance_lite_TITLE_CLASS', 'Maintenance Mode');
define('wp_maintenance_lite_CONTENTS_CLASS', '<h5>Sorry for the inconvenience, our website is currently under scheduled maintenance</h5>');

register_activation_hook(__FILE__, 'wp_maintenance_lite_activate');
register_deactivation_hook(__FILE__, 'wp_maintenance_lite_deactivate');

add_action('admin_menu', 'wp_maintenance_lite_menu');

function wp_maintenance_lite_menu()
    {
    add_menu_page(
        'Maintenance Mode Settings',
        'Maintenance Mode',
        'manage_options',
        'wp_maintenance_lite_settings',
        'wp_maintenance_lite_settings_page'
    );
    }

function wp_maintenance_lite_settings_page()
    {
    if (! current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
        }

    if (isset($_POST['wp_maintenance_lite_save'])) {
        // Save settings when the form is submitted
        update_option('wp_maintenance_lite_title_class', wp_kses_post($_POST['title_class']));
        update_option('wp_maintenance_lite_contents_class', wp_kses_post($_POST['contents_class']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
        }

    // Retrieve user-defined HTML content or use defaults
    $title_class = get_option('wp_maintenance_lite_title_class', wp_maintenance_lite_TITLE_CLASS);
    $contents_class = get_option('wp_maintenance_lite_contents_class', wp_maintenance_lite_CONTENTS_CLASS);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <hr>
        <form method="post">
            <div style="margin-bottom: 40px">
                <label for="title_class" style="font-size:16px;font-weight:bold;margin-bottom:20px">Title:</label>
                <input type="text" name="title_class" value="<?= $title_class ?>" style="width:100%" />
            </div>

            <label for="contents_class" style="font-size:16px;font-weight:bold;margin-bottom:20px">Contents:</label>
            <?php
            // Use WordPress editor for the contents
            $content_editor_id = 'contents_class';
            $content = stripslashes($contents_class); // Remove extra slashes
            wp_editor($content, $content_editor_id, array('textarea_name' => 'contents_class'));
            ?>

            <input type="submit" name="wp_maintenance_lite_save" class="button-primary" value="Save Changes" />
        </form>
    </div>
    <?php
    }

function wp_maintenance_lite_activate()
    {
    add_option('wp_maintenance_lite_title_class', wp_maintenance_lite_TITLE_CLASS);
    add_option('wp_maintenance_lite_contents_class', wp_maintenance_lite_CONTENTS_CLASS);
    }

function wp_maintenance_lite_deactivate()
    {
    delete_option('wp_maintenance_lite_title_class');
    delete_option('wp_maintenance_lite_contents_class');
    }

function wp_maintenance_lite_mode()
    {
    if (! current_user_can('manage_options') && ! is_admin()) {
        // Retrieve user-defined HTML content or use defaults
        $title_class = get_option('wp_maintenance_lite_title_class', wp_maintenance_lite_TITLE_CLASS);
        $contents_class = get_option('wp_maintenance_lite_contents_class', wp_maintenance_lite_CONTENTS_CLASS);

        wp_enqueue_style('wp-maintenance-lite-css', plugin_dir_url(__FILE__) . 'css/style.css');

        // Include the HTML template
        include(plugin_dir_path(__FILE__) . 'template.php');
        exit();
        }
    }
add_action('init', 'wp_maintenance_lite_mode');
?>