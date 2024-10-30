<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsWidgets
{
    
    private $registered_sidebars;
    public  $registered_widget_controls;
    public  $registered_widgets;
    public  $sidebars;

    public function __construct()
    {
        $this->registered_sidebars = $GLOBALS['wp_registered_sidebars'];
        $this->registered_widget_controls = &$GLOBALS['wp_registered_widget_controls'];
        $this->registered_widgets = &$GLOBALS['wp_registered_widgets'];
        $this->sidebars = wp_get_sidebars_widgets();
    }

    public function button($widget, $return, $instance )
    {
        //$args = func_get_args();
        //$widget_id = $instance['name']['widget_id'];
        //$wp_callback = $this->registered_widget_controls[$widget_id]['miwowidgets_wp_original_callback'];

        $language = MiwoWidgetsLoader::getInstance('MiwoWidgetsLanguage');
        $language->load('default');
        $title = $language->get('text_popup_title');
        $button_text = $language->get('button_visibility');

        //if (!empty($wp_callback) and is_object($wp_callback[0])) {
            $title = '&#34;' . $instance['name'] . '&#34;  ' . $title;
        //}

        // Calling original callback first
        //call_user_func_array($wp_callback, $args);

        //if (array_key_exists($instance['name'], $this->registered_widgets)) {
            echo '<a class="button" onclick="getPopup(this, \'' . $title . '\'); return false;" title="' . $title . '">';
            echo $button_text;
            echo '</a>';
        /*} else {
            echo '<em>Save the widget first</em>';
        }*/
    }

    public function addWidgetButton()
    {
        foreach ($this->registered_widgets as $widget_id => $widget) {
            if (array_key_exists($widget_id, $this->registered_widget_controls)) {
                $this->registered_widget_controls[$widget_id]['miwowidgets_wp_original_callback'] = $this->registered_widget_controls[$widget_id]['callback'];
                $this->registered_widget_controls[$widget_id]['callback'] = array($this, 'widgetCallback');

                if (!is_array($this->registered_widget_controls[$widget_id]['params'])) {
                    $this->registered_widget_controls[$widget_id]['params'] = array();
                }

                if (count($this->registered_widget_controls[$widget_id]['params']) == 0) {
                    $this->registered_widget_controls[$widget_id]['params'][] = array('widget_id' => $widget_id);
                } elseif (!is_array($this->registered_widget_controls[$widget_id]['params'][0])) {
                    $this->registered_widget_controls[$widget_id]['params'][0] = array('widget_id' => $widget_id);
                } else {
                    $this->registered_widget_controls[$widget_id]['params'][0]['widget_id'] = $widget_id;
                }
            }
        }
    }

    public function widgetCallback()
    {
        $args = func_get_args();
        $widget_id = $args[0]['widget_id'];
        $wp_callback = $this->registered_widget_controls[$widget_id]['miwowidgets_wp_original_callback'];

        $language = MiwoWidgetsLoader::getInstance('MiwoWidgetsLanguage');
        $language->load('default');
        $title = $language->get('text_popup_title');
        $button_text = $language->get('button_visibility');

        if (!empty($wp_callback) and is_object($wp_callback[0])) {
            $title = '&#34;' . $wp_callback[0]->name . '&#34;  ' . $title;
        }

        // Calling original callback first
        call_user_func_array($wp_callback, $args);

        if (array_key_exists($widget_id, $this->registered_widgets)) {
            echo '<a class="button miwowidgets-visibility" onclick="getPopup(this, \'' . $title . '\'); return false;" title="' . $title . '">';
            echo $button_text;
            echo '</a>';
        } else {
            echo '<em>Save the widget first</em>';
        }
    }
}
