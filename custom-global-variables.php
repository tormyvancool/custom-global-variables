<?php
/**
 * Plugin Name:   Custom Global Variables
 * Plugin GitHub: https://github.com/tormyvancool/custom-global-variables
 * Description:   Easily create custom variables that can be accessed globally in WordPress and PHP with optional comments per variable.
 * Version:       2.0.2
 * Stable tag:    2.0.2
 * Author:        Tormy Van Cool
 * Author URI:    https://www.newtarget.com
 * License:       GPL2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:   custom-global-variables
 * Requires PHP:  7.0
 * Tested up to:  6.8
 * Donate link:   https://www.paypal.com/donate?hosted_button_id=LZ6LLD2B7PGG2
 */

class Custom_Global_Variables_Pro {

    private $file_path = '';
    private $folder_path = WP_CONTENT_DIR . '/custom-global-variables';


    function __construct() {

        $this->file_path = WP_CONTENT_DIR . '/custom-global-variables/' . md5(AUTH_KEY) . '.json';

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $GLOBALS['cgv'] = [];

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $GLOBALS['cgv_meta'] = [];

        if (file_exists($this->file_path)) {
            $vars = file_get_contents($this->file_path);
            $decoded = json_decode($vars, true);

            if (is_array($decoded)) {
                foreach ($decoded as $key => $data) {
                    if (is_array($data)) {
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        $GLOBALS['cgv'][$key]      = stripslashes($data['val'] ?? '');
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        $GLOBALS['cgv_meta'][$key] = stripslashes($data['comment'] ?? '');
                    } else {
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        $GLOBALS['cgv'][$key]      = stripslashes($data);
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        $GLOBALS['cgv_meta'][$key] = '';
                    }
                }
            }
        } else {

            if (wp_mkdir_p(WP_CONTENT_DIR . '/custom-global-variables')) {

                file_put_contents($this->file_path, '');
                
                # Add .htaccess protection
                $htaccess_path = $this->folder_path . '/.htaccess';
                if (!file_exists($htaccess_path)) {
                    
                    $rules = "Order deny,allow\nDeny from all";

                    @file_put_contents($htaccess_path, $rules);
                    $wp_filesystem->chmod($htaccess_path, 0640);
                }

                # Optional: secure the folder itself
                $wp_filesystem->chmod($this->folder_path, 0750);
                $wp_filesystem->chmod($this->file_path,   0640);
            }

            $wp_filesystem->chmod($this->folder_path, 0750);   # Folder: owner read/write/execute, group read/execute
            $wp_filesystem->chmod($this->file_path,   0640);   # File:   owner read/write, group read
        }

        # Object exposure (unchanged)
        $CGV = new stdClass();
        $CGV_META = new stdClass();
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        foreach ($GLOBALS['cgv'] as $key => $val) {

            $CGV->$key = $val;
            $CGV_META->$key = $GLOBALS['cgv_meta'][$key]?? '';

        }
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $GLOBALS['CGV']      = $CGV;
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        $GLOBALS['CGV_META'] = $CGV_META;

        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_head', array($this, 'admin_styles'));
        
        add_shortcode('cgv', array($this, 'shortcode'));
        add_shortcode('cgv_comment', function($params) {

            $param0 = sanitize_text_field($params[0]);
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            return isset($GLOBALS['cgv_meta'][$param0]) ? wp_kses_post($GLOBALS['cgv_meta'][$param0]) : '';

        });
        // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    }

    function add_menu() {
        add_menu_page(
            'CUSTOM GLOBAL VARIABLES PRO (1.0.0) by Tormy Van Cool',
            'Custom Global Variables Pro',
            'manage_options',
            'custom-global-variables',
            array($this, 'admin_page'),
            'dashicons-list-view',
            1
        );
    }
    
    function admin_styles() {
        echo '<style>
            #toplevel_page_custom-global-variables a,
            #toplevel_page_custom-global-variables a { 
                color: white !important;
                font-weight: bold;
                border-radius: 6px;
                font-weight: bold;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                transition: background-color 0.15s ease-in-out;
                padding: 8px 12px;
                margin-top: 5px;
            }
            #toplevel_page_custom-global-variables a {
                background-color: #02766cff !important;
            }
            #toplevel_page_custom-global-variables a:hover {
                background-color: #004de8ff !important;
            }
        </style>';
    }

    function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions.');
        }

        wp_enqueue_style( 'custom-global-variables-style',  plugins_url('style.css', __FILE__), array(), time());
        wp_enqueue_script('custom-global-variables-script', plugins_url('script.js', __FILE__), array('jquery'), time(), true);

        $vars = $GLOBALS['cgv'];
        $comments = $GLOBALS['cgv_meta'];

			if (isset($_POST['vars'])) {
				$nonce = isset($_POST['cgv_nonce']) ? sanitize_key(wp_unslash($_POST['cgv_nonce'])) : '';

				if (!wp_verify_nonce($nonce, 'cgv_nonce')) {
					wp_die('You do not have sufficient permissions.');
					return false;
				}

				$posted_vars = sanitize_key(wp_unslash($_POST['vars']));
				$vars_new = [];

				foreach ((array) $posted_vars as $var) {

					$name = sanitize_textarea_field($var['name'] ?? '');
					$val = wp_kses_post($var['val'] ?? '');
					$comment = sanitize_textarea_field($var['comment'] ?? '');

					if (!empty($name) && !empty($val)) {
						$key = trim(strtolower(str_replace(' ', '_', $name)));
						$vars_new[$key] = array(
							'val'     => $val,
							'comment' => $comment
							);
					}
				}
				
            if (file_put_contents($this->file_path, json_encode($vars_new)) !== false) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                $GLOBALS['cgv'] = [];
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                $GLOBALS['cgv_meta'] = [];
                foreach ($vars_new as $key => $data) {
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                    $GLOBALS['cgv'][$key] = $data['val'];
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                    $GLOBALS['cgv_meta'][$key] = $data['comment'];
                }
                echo '<div id="message" class="updated"><p>Your variables have successfully been saved.</p></div>';
            } else {
                echo '<div id="message" class="error"><p>Your variables could not be saved. Check permissions on:</p><p><strong>' . esc_html(WP_CONTENT_DIR) . '/custom-global-variables</strong></p></div>';
            }
        }
		?>

        <div class="wrap">
            <h2>Custom Global Variables v2.0</h2>

            <div class="card">
                <h3>Usage</h3>
                <h4>SHORTCODES</h4>
                <p><code>[cgv variable_name]</code> output: value of the variable</p>
                <p><code>[cgv_comment variable_name]</code> output: comment related to the variable</p>
                <p>&nbsp</p>
                <h4>ARRAYS</h4>
                <p><code>&lt;?php echo $GLOBALS['cgv_pro']['variable_name']; ?&gt;</code> output: value of the variable</p>
                <p><code>&lt;?php echo $GLOBALS['cgv_meta_pro']['variable_name']['comment']; ?&gt;</code> output: comment related to the variable</p>
                <p>&nbsp</p>
                <h4>OBJECTS</h4>
                <p><code>&lt;?php echo $CGV_PRO->variable_name; ?&gt;</code> output: value of the variable</p>
                <p><code>&lt;?php echo $CGV_META_PRO->variable_name; ?&gt;</code> output: comment related to the variable</p>
            </div>

            <div class="card">
                <h3>Define your variables</h3>
                <form method="POST" action="">
                    <?php wp_nonce_field('cgv_nonce', 'cgv_nonce', false, true); ?>
                    <table id="custom-global-variables-table-definitions" class="widefat">
                        <thead>
                            <tr>
                                <th>Variable</th>
                                <th>Value</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            if (!empty($vars)):
                                foreach ($vars as $key => $val):
                                    $key = esc_html($key);
                                    $value = $val;
                                    $comment = $comments[$key] ?? '';
                            ?>
                            <tr>
                                <td class="variable">
                                    <input name="vars[<?php echo esc_html($i); ?>][name]" type="text" value="<?php echo esc_attr($key) ?>" placeholder="name" autocomplete="off">
                                </td>
                                <td class="value">
                                    <input name="vars[<?php echo esc_html($i); ?>][val]" type="text" value="<?php echo esc_attr($value) ?>" placeholder="value" autocomplete="off">
                                </td>
                                <td class="comment">
                                    <input name="vars[<?php echo esc_html($i); ?>][comment]" type="text" value="<?php echo esc_attr($comment) ?>" placeholder="comment (optional)" autocomplete="off">
                                </td>
                                <td class="options">
                                    <img alt="delete" class="delete" src="<?php echo esc_html(plugin_dir_url(__FILE__)); ?>/delete.png">
                                </td>
                            </tr>
                            <?php $i++; endforeach; endif; ?>

                            <tr>
                                <td class="variable">
                                    <input name="vars[<?php echo esc_html($i); ?>][name]" type="text" placeholder="name" autocomplete="off">
                                </td>
                                <td class="value">
                                    <input name="vars[<?php echo esc_html($i); ?>][val]" type="text" placeholder="value" autocomplete="off">
                                </td>
                                <td class="comment">
                                    <input name="vars[<?php echo esc_html($i); ?>][comment]" type="text" placeholder="comment (optional)" autocomplete="off">
                                </td>
                                <td class="options"></td>
                            </tr>
                        </tbody>
                    </table>

                    <p><input type="submit" value="Save" class="button-primary"></p>
                </form>
            </div>
        </div>

        <?php
    }    function shortcode($params) {
        $param0 = sanitize_text_field($params[0]);
        return isset($GLOBALS['cgv_pro'][$param0]) ? wp_kses_post($GLOBALS['cgv_pro'][$param0]) : '';
    }
}

$custom_global_variables_pro = new Custom_Global_Variables_Pro;

# Add "Settings" link in plugin list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="options-general.php?page=custom-global-variables">' . 'Settings' . '</a>';
    array_unshift($links, $settings_link);
    return $links;
});
