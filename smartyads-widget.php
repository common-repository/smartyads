<?php

class SmartyAdsWidget extends WP_Widget {


    function SmartyAdsWidget() {
        parent::WP_Widget(false, $name = __('SmartyAds Widget', 'monet') );
    }


    function form($instance) {
        
        $zones = array();
        if (get_option('smartyads_zones')) {
            $zones = get_option('smartyads_zones');
        }

        if( $instance) {
             $title = esc_attr($instance['title']);
             $zone_id = esc_attr($instance['zone_id']);
        } else {
             $title = '';
             $zone_id = '';
        }

        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'monet'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        
        <?php if ( count($zones) > 0) { ?>
            <p>
                <label for="<?php echo $this->get_field_id('zone_id'); ?>"><?php _e('Ad Zone:', 'monet'); ?></label>

                <select id="<?php echo $this->get_field_id('zone_id'); ?>" name="<?php echo $this->get_field_name('zone_id'); ?>">
                    <?php foreach ($zones as $id => $name) { ?>
                        <option <?php 
                        if ($id == $zone_id) {
                            echo 'selected';
                        }
                        
                        ?> value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>


            </p>
        <?php } else {?>
            <p><?php _e('This site does not have any ad zone.', 'monet'); ?></p>
        <?php } ?>
    
        <?php
    }


    function update($new_instance, $old_instance) {
          $instance = $old_instance;


          $instance['title'] = strip_tags($new_instance['title']);
          $instance['zone_id'] = strip_tags($new_instance['zone_id']);
         return $instance;
    }

    function widget($args, $instance) {
        
        $zone_id = intval($instance['zone_id']);
        if ($zone_id > 0) {
            echo '<SCRIPT SRC="http://smartyads.com/sa.js?id='. $zone_id .'"  TYPE="text/javascript"></SCRIPT>';
        }
    }
    

}

function widgets_init_hook() {
    register_widget("SmartyAdsWidget");
}

add_action('widgets_init', 'widgets_init_hook');

