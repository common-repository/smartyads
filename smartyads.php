<?php 
/*
Plugin Name: SmartyAds
Plugin URI: http://smartyads.com/info/wp-plugin
Description: This plugin will automate adding SmartyAds advertisement to your blog posts. SmartyAds will show your blog viewers targeted ads.
Version: 1.1
Author: SmartyAds
Author URI: http://smartyads.com
*/



define('PLUGIN_PATH',  dirname( plugin_basename( __FILE__ ))  );

require_once 'smartyads-widget.php';





add_action( 'plugins_loaded', 'monet_load_textdomain' );

function monet_load_textdomain() {
  load_plugin_textdomain( 'monet', false, PLUGIN_PATH . '/languages/' );
}




/*
 * load scripts
 */
function load_scripts() {
    if ( is_admin() ) {
        wp_register_style('smartyads_style', plugins_url('style.css',__FILE__ ));
        wp_enqueue_style('smartyads_style');

        wp_register_script('smartyads_my_script_js',  plugins_url('/js/script.js',__FILE__ ));
        wp_enqueue_script('smartyads_my_script_js');
    }
}
add_action('admin_enqueue_scripts', 'load_scripts');



add_action('admin_menu', 'my_plugin_menu');


function my_plugin_menu() {
	add_options_page( __('SmartyAds', 'monet'), __('SmartyAds', 'monet'), 'manage_options', 'settings', 'monet_option_panel');
}


function monet_option_panel() {
    echo "<script> var ajax_file_api = '". plugins_url( 'ajax.php', __FILE__ ) ."'</script>";

if (get_option('smarty_new_key_valid')) {
?>       
        <div class="b-monetguru">
            <div class="b-wizard b-wizard_step_1">
                    <h2><?php _e('Plugin Dashboard', 'monet'); ?></h2>
                    
                    <?php if (is_array(get_option('smartyads_zones')) && count(get_option('smartyads_zones')) > 0) { ?>
                        <p><b><?php echo count(get_option('smartyads_zones')) ?></b> <?php _e("ad zone(s) has been added. You can set your ad zones in", 'monet'); ?> <em>Appearance→Widgets</em>. </p>
                    <?php } else { ?>
                        <p><?php _e("This site doesn`t have any ad zone. To add zones please visit", 'monet'); ?> <a target="_blank" href="http://smartyads.com/publishers/inventory/site/<?php echo get_option('smartyads_site_id'); ?>"><?php _e("Add Inventory", 'monet'); ?></a>. </p>
                    
                    <?php } ?>
               
                    <button class="b-control__btn sync_finish_button" type="submit"><?php _e('Finish', 'monet'); ?></button>
                </div>
            </div>
<?php
    } else {
?>
        <div class="b-monetguru">
            <div class="b-wizard b-wizard_step_1">
                    <h2><?php _e('Plugin Dashboard', 'monet'); ?></h2>
                    <div class="b-control sync_input_wrap">
                            <label for="" class="b-control__label"><?php _e('Website API Key', 'monet'); ?></label>
                            <span class="b-control__input"><input id="sync_input" type="text" value="<?php echo get_option('smartyads_key'); ?>"></span>
                            <span class="b-help"><?php _e("Don't have an account yet?", 'monet'); ?> <a href="http://smartyads.com/user/signup" target="_blank"><?php _e('Sign Up', 'monet'); ?></a> <?php _e('To create an account.', 'monet'); ?></span>
                            <span class="error"><?php _e("Wrong API Key", 'monet'); ?></span>
                    </div>
                    <button id="sync_btn" type="submit" class="b-control__btn"><?php _e("Sync", 'monet'); ?></button>
            </div>

        </div>

<?php
    }

    update_option('smarty_new_key_valid', 0);
    
}




function add_impressoin_code() {

    if ( get_option('smartyads_site_id') && get_option('smartyads_host') ) {

        echo "<script type='text/javascript'>
                var smarty_im = document.createElement('script');
                smarty_im.src = '//stat.smartyads.com/simpr2.php?sid=". get_option('smartyads_site_id') ."&r='+ Math.random() +'&host=". get_option('smartyads_host') ."';
                smarty_im.type = 'text/javascript';
                smarty_im.async = \"true\";
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(smarty_im, s);
            </script>";
        
        
    }

}

add_action('wp_head', 'add_impressoin_code');



add_action('wp_ajax_sync', 'sync_callback');

function sync_callback() {
    global $wpdb;

    $data = $_POST;

    if (isset($data['type_operation']) && $data['type_operation'] == 'remove_key') {
        delete_option('smartyads_key');
        delete_option('smartyads_site_id');
        delete_option('smartyads_host');
        delete_option('smartyads_zones');
        delete_option('smarty_new_key_valid');
        remove_smartyads_widgets();
        exit;
    }

    if (isset($data['api_key']) && !empty($data['api_key'])) {

        remove_smartyads_widgets();

        update_option('smartyads_key', $data['api_key']);
        update_option('smarty_new_key_valid', 1);

        $data_keys = array_keys($data);

        if ( isset($data_keys[0]) && !empty($data_keys[0]) ) {

            update_option('smartyads_site_id', intval($data_keys[0]));
            update_option('smartyads_host', $data[$data_keys[0]]['addr']);

            if (isset($data[$data_keys[0]]['zones']) && is_array($data[$data_keys[0]]['zones']) && count($data[$data_keys[0]]['zones']) > 0) {
                update_option('smartyads_zones', $data[$data_keys[0]]['zones']);
            }
            else {
                update_option('smartyads_zones', array());
            }

        }


    }
        
    exit;
}




function remove_smartyads_widgets() {
    
    $widgets = wp_get_sidebars_widgets();

    foreach ($widgets as &$widget) {
        foreach ($widget as $k=>$v) {
            
            if (strpos($v, 'smartyadswidget') === 0) {
                unset($widget[$k]);
            }
        }
    }

    wp_set_sidebars_widgets($widgets);
}