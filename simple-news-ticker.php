<?php
/*
Plugin Name: Header Bottom News Ticker
Description: News ticker with customizable scroll speed
Version: 2.0
Author: JBD IT
*/

if (!defined('ABSPATH')) exit;

/* Register Widget Area */
function hbnt_register_widget_area() {
    register_sidebar([
        'name'          => 'Header Bottom Ticker',
        'id'            => 'header-bottom-ticker',
        'before_widget' => '<div class="hbnt-wrapper">',
        'after_widget'  => '</div>',
    ]);
}
add_action('widgets_init', 'hbnt_register_widget_area');

/* Load CSS and JS */
function hbnt_assets() {
    wp_enqueue_style('hbnt-style', plugin_dir_url(__FILE__) . 'style.css', [], '2.0');
    wp_enqueue_script('hbnt-script', plugin_dir_url(__FILE__) . 'script.js', ['jquery'], '2.0', true);
}
add_action('wp_enqueue_scripts', 'hbnt_assets');

/* Custom Widget */
class HBNT_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'hbnt_widget',
            'Header Bottom News Ticker'
        );
    }

    public function widget($args, $instance) {
        $posts_count = !empty($instance['posts']) ? intval($instance['posts']) : 10;
        $speed = !empty($instance['speed']) ? floatval($instance['speed']) : 1.0;

        $query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => $posts_count,
        ]);

        if (!$query->have_posts()) return;

        echo $args['before_widget'];
        ?>
        <div class="hbnt-ticker" data-speed="<?php echo esc_attr($speed); ?>">
            <span class="hbnt-label">সর্বশেষ নোটিশ</span>
            <div class="hbnt-container">
                <ul class="hbnt-list">
                    <?php 
                    while ($query->have_posts()) : $query->the_post(); 
                        echo '<li><span class="hbnt-icon"> ♦ </span><a href="'.get_the_permalink().'">'.get_the_title().'</a></li>';
                    endwhile; 
                    wp_reset_postdata();
                    ?>
                </ul>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $posts = isset($instance['posts']) ? intval($instance['posts']) : 10;
        $speed = isset($instance['speed']) ? floatval($instance['speed']) : 1.0;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('posts'); ?>">Number of Posts:</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('posts'); ?>"
                   name="<?php echo $this->get_field_name('posts'); ?>" 
                   type="number" 
                   min="1"
                   max="50"
                   value="<?php echo esc_attr($posts); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('speed'); ?>">Scroll Speed:</label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('speed'); ?>"
                   name="<?php echo $this->get_field_name('speed'); ?>" 
                   type="number" 
                   step="0.1"
                   min="0.1"
                   max="5"
                   value="<?php echo esc_attr($speed); ?>">
            <small style="display:block; margin-top:5px; color:#666;">
                0.5 = Slow | 1.0 = Normal | 2.0 = Fast
            </small>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['posts'] = (!empty($new_instance['posts'])) ? intval($new_instance['posts']) : 10;
        $instance['speed'] = (!empty($new_instance['speed'])) ? floatval($new_instance['speed']) : 1.0;
        return $instance;
    }
}

/* Register Widget */
function hbnt_register_widget() {
    register_widget('HBNT_Widget');
}
add_action('widgets_init', 'hbnt_register_widget');

/* Show widget area after header */
function hbnt_show_after_header() {
    if (is_active_sidebar('header-bottom-ticker')) {
        dynamic_sidebar('header-bottom-ticker');
    }
}
add_action('wp_body_open', 'hbnt_show_after_header');
