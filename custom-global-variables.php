<?php
/**
 * Plugin Name: Custom Global Variables
 * Plugin URI: https://www.newtarget.com/solutions/wordpress-websites		<<< It seems not anylonger maintained by the original programmer
 * Plugin GitHub: https://www.newtarget.com/solutions/wordpress-websites	<<< Not a GitHub repository.
 * Plugin GitHub: https://github.com/tormyvancool/custom-global-variables	<<< From version 2.0 on
 * Description: Easily create custom variables that can be accessed globally in WordPress and PHP. Now with comments per variable.
 * Version: 2.0.1
 * Author: new target, inc + Tormy Van Cool (improvements and versioning to 2.0)
 * Author URI: https://www.newtarget.com
 * License: GPL2 or later
 */

class Custom_Global_Variables {

    private $file_path = '';
    private $folder_path = WP_CONTENT_DIR . '/custom-global-variables';


    function __construct() {
        $this->file_path = WP_CONTENT_DIR . '/custom-global-variables/' . md5(AUTH_KEY) . '.json';

        $GLOBALS['cgv'] = [];
        $GLOBALS['cgv_meta'] = [];

        if (file_exists($this->file_path)) {
            $vars = file_get_contents($this->file_path);
            $decoded = json_decode($vars, true);

            if (is_array($decoded)) {
                foreach ($decoded as $key => $data) {
                    if (is_array($data)) {
                        $GLOBALS['cgv'][$key] = stripslashes($data['val'] ?? '');
                        $GLOBALS['cgv_meta'][$key] = stripslashes($data['comment'] ?? '');
                    } else {
                        $GLOBALS['cgv'][$key] = stripslashes($data);
                        $GLOBALS['cgv_meta'][$key] = '';
                    }
                }
            }
        } else {
            if (wp_mkdir_p(WP_CONTENT_DIR . '/custom-global-variables')) {
                file_put_contents($this->file_path, '');
                
                // Add .htaccess protection
                $htaccess_path = $this->folder_path . '/.htaccess';
                if (!file_exists($htaccess_path)) {
                    $rules = "Order deny,allow\nDeny from all";
                    @file_put_contents($htaccess_path, $rules);
                    @chmod($htaccess_path, 0640);
                }

                // Optional: secure the folder itself
                @chmod($this->folder_path, 0750);
                @chmod($this->file_path, 0640);
            }
            @chmod($this->folder_path, 0750); // Folder: owner read/write/execute, group read/execute
            @chmod($this->file_path, 0640); // File: owner read/write, group read
        }

        // Object exposure (unchanged)
        $CGV = new stdClass();
        $CGV_META = new stdClass();
        foreach ($GLOBALS['cgv'] as $key => $val) {
            $CGV->$key = $val;
            $CGV_META->$key = $GLOBALS['cgv_meta'][$key]?? '';
        }
        $GLOBALS['CGV'] = $CGV;
        $GLOBALS['CGV_META'] = $CGV_META;

        add_action('admin_menu', array($this, 'add_menu'));
        
        add_shortcode('cgv', array($this, 'shortcode'));
        add_shortcode('cgv_comment', function($params) {
            $param0 = sanitize_text_field($params[0]);
            return isset($GLOBALS['cgv_meta'][$param0]) ? wp_kses_post($GLOBALS['cgv_meta'][$param0]) : '';
        });
    }

    function add_menu() {
        add_submenu_page(
            'options-general.php',
            'Custom Global Variables',
            'Custom Global Variables',
            'manage_options',
            'custom-global-variables',
            array($this, 'admin_page')
        );
    }

    function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions.');
        }

        wp_enqueue_style('custom-global-variables-style', plugins_url('style.css', __FILE__) . '?v=' . time());
        wp_enqueue_script('custom-global-variables-script', plugins_url('script.js', __FILE__), array('jquery'));

        $vars = $GLOBALS['cgv'];
        $comments = $GLOBALS['cgv_meta'];

        if (isset($_POST['vars'])) {
            if (!isset($_POST['cgv_nonce']) || !wp_verify_nonce($_POST['cgv_nonce'], 'cgv_nonce')) {
                wp_die('You do not have sufficient permissions.');
                return false;
            }

            $vars_new = [];

            foreach ($_POST['vars'] as $var) {
                $name = stripslashes(sanitize_textarea_field($var['name']));
                $val = stripslashes(wp_kses_post($var['val']));
                $comment = stripslashes(sanitize_textarea_field($var['comment']));

                if (!empty($name) && !empty($val)) {
                    $key = trim(strtolower(str_replace(' ', '_', $name)));
                    $vars_new[$key] = [
                        'val' => $val,
                        'comment' => $comment
                    ];
                }
            }

            if (file_put_contents($this->file_path, json_encode($vars_new)) !== false) {
                $GLOBALS['cgv'] = [];
                $GLOBALS['cgv_meta'] = [];
                foreach ($vars_new as $key => $data) {
                    $GLOBALS['cgv'][$key] = $data['val'];
                    $GLOBALS['cgv_meta'][$key] = $data['comment'];
                }
                echo '<div id="message" class="updated"><p>Your variables have successfully been saved.</p></div>';
            } else {
                echo '<div id="message" class="error"><p>Your variables could not be saved. Check permissions on:</p><p><strong>' . WP_CONTENT_DIR . '/custom-global-variables</strong></p></div>';
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
                <p><code>&lt;?php echo $GLOBALS['cgv']['variable_name']; ?&gt;</code> output: value of the variable</p>
                <p><code>&lt;?php echo $GLOBALS['cgv_meta']['variable_name']['comment']; ?&gt;</code> output: comment related to the variable</p>
                <p>&nbsp</p>
                <h4>OBJECTS</h4>
                <p><code>&lt;?php echo $CGV->variable_name; ?&gt;</code> output: value of the variable</p>
                <p><code>&lt;?php echo $CGV_META->variable_name; ?&gt;</code> output: comment related to the variable</p>
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
                                    <input name="vars[<?php echo $i ?>][name]" type="text" value="<?php echo esc_attr($key) ?>" placeholder="name" autocomplete="off">
                                </td>
                                <td class="value">
                                    <input name="vars[<?php echo $i ?>][val]" type="text" value="<?php echo esc_attr($value) ?>" placeholder="value" autocomplete="off">
                                </td>
                                <td class="comment">
                                    <input name="vars[<?php echo $i ?>][comment]" type="text" value="<?php echo esc_attr($comment) ?>" placeholder="comment (optional)" autocomplete="off">
                                </td>
                                <td class="options">
                                    <img alt="delete" class="delete" src="<?php echo plugin_dir_url(__FILE__) ?>/delete.png">
                                </td>
                            </tr>
                            <?php $i++; endforeach; endif; ?>

                            <tr>
                                <td class="variable">
                                    <input name="vars[<?php echo $i ?>][name]" type="text" placeholder="name" autocomplete="off">
                                </td>
                                <td class="value">
                                    <input name="vars[<?php echo $i ?>][val]" type="text" placeholder="value" autocomplete="off">
                                </td>
                                <td class="comment">
                                    <input name="vars[<?php echo $i ?>][comment]" type="text" placeholder="comment (optional)" autocomplete="off">
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
        return isset($GLOBALS['cgv'][$param0]) ? wp_kses_post($GLOBALS['cgv'][$param0]) : '';
    }
}

$custom_global_variables = new Custom_Global_Variables;

// Add "Settings" link in plugin list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="options-general.php?page=custom-global-variables">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
});
