<?php
/*
Plugin Name: داشبورد کاربر تیما
Description: پلاگینی برای ایجاد پنل کاربری با منوهای سفارشی که مدیر می‌تواند آنها را بسازد و تنظیم کند.[tmaudasbourd_dashboard] [tmaudasbourd_dashboardid]
Version: 1.0
Author: mohammad bagheri
Plugin URI: https://t-ma.ir
Author URI: https://g-t.ma
*/

// Enqueue styles and scripts
function tmaudasbourd_enqueue_assets() {
    wp_enqueue_style('tmaudasbourd-style', plugin_dir_url(__FILE__) . 'style.css?v50089');
    wp_enqueue_script('tmaudasbourd-script', plugin_dir_url(__FILE__) . 'script.js?v50089', array('jquery'), null, true);
    wp_localize_script('tmaudasbourd-script', 'tmaudasbourd_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}

// Add admin menu
function tmaudasbourd_add_admin_menu() {
    add_menu_page('داشبورد کاربر تیما', 'داشبورد کاربر تیما', 'manage_options', 'tmaudasbourd_dashboard_panel', 'tmaudasbourd_settings_page', 'dashicons-admin-generic');
}
add_action('admin_menu', 'tmaudasbourd_add_admin_menu');

// Register settings
function tmaudasbourd_register_settings() {
    register_setting('tmaudasbourd_settings_group', 'tmaudasbourd_menu_items');
    add_settings_section('tmaudasbourd_settings_section', 'تنظیمات منو / tools', null, 'tmaudasbourd_settings');
    add_settings_field('tmaudasbourd_menu_items', 'آیتم‌های منو/menu', 'tmaudasbourd_menu_items_callback', 'tmaudasbourd_settings', 'tmaudasbourd_settings_section');
}
add_action('admin_init', 'tmaudasbourd_register_settings');

// Menu items callback
function tmaudasbourd_menu_items_callback() {
    $menu_items = get_option('tmaudasbourd_menu_items', array());
    
    wp_enqueue_style('tmaudasbourd-style', plugin_dir_url(__FILE__) . 'style.css?v50089');
    wp_enqueue_script('tmaudasbourd-script', plugin_dir_url(__FILE__) . 'script.js?v50089', array('jquery'), null, true);
    ?>
    <div id="tmaudasbourd-menu-items-container">
        <?php foreach ($menu_items as $index => $item) : ?>
            <div class="tmaudasbourd-menu-item">
                <input type="text" name="tmaudasbourd_menu_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" placeholder="عنوان" />
                <?php wp_editor(esc_textarea($item['content']), 'tmaudasbourd_menu_items_' . $index . '_content', array('textarea_name' => 'tmaudasbourd_menu_items[' . $index . '][content]')); ?>
                <input type="text" name="tmaudasbourd_menu_items[<?php echo $index; ?>][icon]" value="<?php echo esc_attr($item['icon']); ?>" placeholder="کلاس Dashicon" />
                <button type="button" class="button button-secondary tmaudasbourd-remove-menu-item">حذف</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="tmaudasbourd-add-menu-item">افزودن آیتم منو
    Add menu
    </button>
    <?php
}

// Settings page HTML
function tmaudasbourd_settings_page() {

    wp_enqueue_style('tmaudasbourd-style', plugin_dir_url(__FILE__) . 'style.css?v50089');
    wp_enqueue_script('tmaudasbourd-script', plugin_dir_url(__FILE__) . 'script.js?v50089', array('jquery'), null, true);
    ?>
    <div class="wrap">
        <h1>داشبورد کاربر تیما</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('tmaudasbourd_settings_group');
            do_settings_sections('tmaudasbourd_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Frontend dashboard HTML
function tmaudasbourd_user_dashboard() {
    if (!is_user_logged_in()) {
        return '<p>چند لحظه منتظر بمانید.</p> 
        <script>
function myFunction() {
number=Math.floor(Math.random() * 1000) + 1;
  location.replace("?login=true&" + number)
}
myFunction();
</script>
<button onclick="myFunction()">ورود</button>

        ';
    }

    $menu_items = get_option('tmaudasbourd_menu_items', array());
    ob_start();
    add_action('wp_enqueue_scripts', 'tmaudasbourd_enqueue_assets');

    wp_enqueue_style('tmaudasbourd-style', plugin_dir_url(__FILE__) . 'style.css?v50089');
    wp_enqueue_script('tmaudasbourd-script', plugin_dir_url(__FILE__) . 'script.js?v50089', array('jquery'), null, true);
    ?>
    <div class="tmaudasbourd-dashboard">
        <!--
        <div class="tmaudasbourd-profile">
            <?php echo get_avatar(get_current_user_id(), 96); ?>
            <h2><?php echo wp_get_current_user()->display_name; ?></h2>
        </div>
        -->
        <div class="tmaudasbourd-menu">
              <div class="tmaudasbourd-profile">
            <?php echo get_avatar(get_current_user_id(), 96); ?>
            <h2><?php echo wp_get_current_user()->display_name; ?></h2>
        </div>
      
            <?php foreach ($menu_items as $item) : ?>
                <div class="tmaudasbourd-menu-item">
                    <span class="dashicons <?php echo esc_attr($item['icon']); ?>"></span>
                    <a href="#" class="tmaudasbourd-menu-link" data-content="<?php echo esc_attr($item['content']); ?>"><?php echo esc_html($item['title']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tmaudasbourd-content">
            <!-- محتوای اینجا بارگذاری می‌شود -->
        </div>
    </div>
    <div class="tmaudasbourd-toggle-menu">☰</div>
    <?php
    return ob_get_clean();
}
add_shortcode('tmaudasbourd_dashboard', 'tmaudasbourd_user_dashboard');

// AJAX handler to load content
function tmaudasbourd_load_content() {
    $content = wp_kses_post($_POST['content']);
    echo do_shortcode($content);
    wp_die();
}
add_action('wp_ajax_tmaudasbourd_load_content', 'tmaudasbourd_load_content');
//***************************************************************************

// Shortcode to display content based on menu ID
function tmaudasbourd_display_content_by_id() {
    if (!is_user_logged_in()) {
        return '<p>چند لحظه منتظر بمانید.</p> 
        <script>
        function myFunction() {
            number = Math.floor(Math.random() * 1000) + 1;
            location.replace("?login=true&" + number);
        }
        myFunction();
        </script>
        <button onclick="myFunction()">ورود</button>';
    }

    $menu_items = get_option('tmaudasbourd_menu_items', array());
    $menu_id = isset($_GET['menu_id']) ? sanitize_text_field($_GET['menu_id']) : '';
  wp_enqueue_style('tmaudasbourd-style', plugin_dir_url(__FILE__) . 'style.css?v50089');
    wp_enqueue_script('tmaudasbourd-script', plugin_dir_url(__FILE__) . 'script2.js?v50089', array('jquery'), null, true);
  
    ob_start();
    ?>
    <div class="tmaudasbourd-dashboard">
        <div class="tmaudasbourd-menu">
            <div class="tmaudasbourd-profile">
                <?php echo get_avatar(get_current_user_id(), 96); ?>
                <h2><?php echo wp_get_current_user()->display_name; ?></h2>
            </div>
            <?php foreach ($menu_items as $index => $item) : ?>
                <div class="tmaudasbourd-menu-item">
                    <span class="dashicons <?php echo esc_attr($item['icon']); ?>"></span>
                    <a href="?menu_id=<?php echo $index; ?>" class="tmaudasbourd-menu-link"><?php echo esc_html($item['title']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tmaudasbourd-content">
            <?php
            if ($menu_id !== '' && isset($menu_items[$menu_id])) {
                echo  do_shortcode(  $menu_items[$menu_id]['content']  );
            } else {
                //echo '<p>محتوایی یافت نشد.</p>';
            echo  do_shortcode(  $menu_items[0]['content']  );
                
            }
            ?>
        </div>
    </div>
    <div class="tmaudasbourd-toggle-menu">☰</div>
    <?php
    return ob_get_clean();
}
add_shortcode('tmaudasbourd_dashboardid', 'tmaudasbourd_display_content_by_id');
