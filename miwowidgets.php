<?php
/*
Plugin Name: MiwoWidgets
Plugin URI: http://miwisoft.com
Description: MiwoWidgets is a Widget Visibility Manager that allows to control which widgets will appear on which pages of your site.
Author: Miwisoft
Version: 1.2.2
Author URI: http://miwisoft.com/wordpress-plugins/miwowidgets-widget-visibility-manager
Text Domain: miwowidgets
*/

class MiwoWidgets
{

	public function __construct()
    {
        $this->constants();

        require_once(MIWOWIDGETS_LIBRARY_PATH . 'init.php');

        $this->init();
	}

    public function init()
    {
        global $wp_customize;

        if (!is_admin()) {
            $authorization = new MiwoWidgetsAuthorization();
            add_action('sidebars_widgets', array($authorization, 'authorize'));
        } elseif (!isset($wp_customize)) {
            add_action('admin_menu', array($this, 'menu'));
            add_action('sidebar_admin_setup', array($this, 'my_sidebar_admin_setup'));
            add_filter("plugin_action_links_".MIWOWIDGETS_NAME, array($this, 'miwowidgetsSettingsLink'));

            $widgets = new MiwoWidgetsWidgets();
            add_action('sidebar_admin_setup', array($widgets, 'addWidgetButton'));
            add_action('admin_enqueue_scripts', array($this, 'addScripts'), 9999);

            $ajax = new MiwoWidgetsAjax();
            add_action("wp_ajax_miwowidgets_ajax", array($ajax, 'ajax'));
            add_action("wp_ajax_get_data", array($ajax,'getData'));

            add_filter('posts_where', array($this, 'title_like_posts_where'), 10, 2);
        }
    }

    public function constants()
    {
        if (!defined('MIWOWIDGETS_NAME')) {
            define('MIWOWIDGETS_NAME', plugin_basename(__FILE__));
        }

        if (!defined('MIWOWIDGETS_PATH')) {
            define('MIWOWIDGETS_PATH', plugin_dir_path(__FILE__));
        }

        if (!defined('MIWOWIDGETS_LIBRARY_PATH')) {
            define('MIWOWIDGETS_LIBRARY_PATH', MIWOWIDGETS_PATH . 'library/');
        }

        if (!defined('MIWOWIDGETS_MEDIA_PATH')) {
            define('MIWOWIDGETS_MEDIA_PATH', MIWOWIDGETS_PATH . 'media/');
        }

        if (!defined('MIWOWIDGETS_CONTROLLER_PATH')) {
            define('MIWOWIDGETS_CONTROLLER_PATH', MIWOWIDGETS_PATH . 'controller/');
        }

        if (!defined('MIWOWIDGETS_MODEL_PATH')) {
            define('MIWOWIDGETS_MODEL_PATH', MIWOWIDGETS_PATH . 'model/');
        }

        if (!defined('MIWOWIDGETS_VIEW_PATH')) {
            define('MIWOWIDGETS_VIEW_PATH', MIWOWIDGETS_PATH . 'view/');
        }

        if (!defined('MIWOWIDGETS_LANGUAGE_PATH')) {
            define('MIWOWIDGETS_LANGUAGE_PATH', MIWOWIDGETS_PATH . 'language/');
        }

        if (!defined('MIWOWIDGETS_ADMIN_URL')) {
            define('MIWOWIDGETS_ADMIN_URL', admin_url());
        }

        if (!defined('MIWOWIDGETS_URL')) {
            $current_directory_name = basename(dirname(__FILE__));
            define('MIWOWIDGETS_URL', plugins_url($current_directory_name).'/');
        }
    }

    public function addScripts()
    {
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('miwowidgets', MIWOWIDGETS_URL.'media/css/miwowidgets.css');
        wp_enqueue_style('datetimepicker', MIWOWIDGETS_URL.'media/css/jquery.datetimepicker.css');
        wp_enqueue_style('chosen', MIWOWIDGETS_URL.'media/css/jquery.chosen.css');

		$version = get_bloginfo('version');
        if (version_compare($version, '3.9') == -1) {
            wp_enqueue_style( 'miwowidgets_wpold', MIWOWIDGETS_URL.'media/css/wpold.css');
        }
	
        if (file_exists(get_home_path(). 'wp-includes/css/dashicons.css')) {
            wp_enqueue_style('dashicons');
        } else {
            wp_enqueue_style('miwowidgets-dashicons', MIWOWIDGETS_URL.'media/css/dashicons.min.css');
        }

        #to remove ui css which is loaded by miwoshop
        wp_deregister_style(content_url().'/miwi/media/jui/css/jquery-ui-1.10.4.custom.min.css');

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-tabs');

        wp_enqueue_script('miwowidgets', MIWOWIDGETS_URL.'media/js/miwowidgets.js', array('jquery','jquery-ui-core','jquery-ui-dialog' ), false, false);
        wp_enqueue_script('datetimepicker', MIWOWIDGETS_URL.'media/js/jquery.datetimepicker.js', array('jquery','jquery-ui-core','jquery-ui-dialog'), false, false);
        wp_enqueue_script('chosen', MIWOWIDGETS_URL.'media/js/jquery.chosen.js', array('jquery','jquery-ui-core','jquery-ui-dialog'), false, false);
    }

    public function miwowidgetsSettingsLink($links)
    {
        $lang = MiwoWidgetsLoader::getInstance('MiwoWidgetsLanguage');
        $lang->load('default');

        $settings_link = '<a href="options-general.php?page=miwowidgets">'.$lang->get('text_settings').'</a>';
        $links[] =  $settings_link;

        return $links;
    }

    public function menu()
    {
        add_options_page('MiwoWidgets Settings', 'MiwoWidgets', 'manage_options', 'miwowidgets', array($this, 'display_setting'));
    }

    public function display_setting()
    {
        $page = MiwoWidgetsRequest::getString('page', '');

        if (!empty($page) && ($page == 'miwowidgets')) {
            $loader = MiwoWidgetsLoader::getInstance();
            $loader->run('', 'setting/setting');
        }
    }

    public function title_like_posts_where($where, &$wp_query)
    {
        global $wpdb;
        
        if ($post_title_like = $wp_query->get('post_title_like')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $post_title_like ) ) . '%\'';
        }

        return $where;
    }

    public function my_sidebar_admin_setup()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $widget_id = $_POST['widget-id'];

            if (isset($_POST['delete_widget']) && ((int)$_POST['delete_widget'] == 1)) {
                $loader = MiwoWidgetsLoader::getInstance();
                $loader->run('deleteAllRulesByWidgetID', '', $widget_id);
            }
        }
    }

}

function MiwoWidgetsInit() {
    new MiwoWidgets();
}
add_action('init', 'MiwoWidgetsInit');