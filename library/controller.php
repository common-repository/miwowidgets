<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsController
{
    public $tpl = '';
    public $data = array();
    public $output = '';
    public $model;
    public $wp_query;
    public $language;
    public $item_count;
    public $module = '';
    public $icon = 'menu';

    public function __construct()
    {
        $this->model = MiwoWidgetsLoader::getInstance('MiwoWidgetsModel');
        $this->language = MiwoWidgetsLoader::getInstance('MiwoWidgetsLanguage');
        $this->item_count = MiwoWidgetsFactory::getOption('miwowidgets', 10, 'item_count');
        
        $this->wp_query = $GLOBALS['wp_query'];
        
        $this->language->load('module/'.$this->module);
    }

    public function getTab()
    {
        $this->language->load('module/'.$this->module);
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['icon'] = 'dashicons-'.$this->icon;

        $this->tpl = 'dialog/tab.tpl';
        $this->render();
    }
    
    public function getContent()
    {
        $this->language->load('module/'.$this->module);
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['button_text_reset_filters'] = $this->language->get('button_text_reset_filters');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $this->data['show_filter'] = false;
        $this->data['show_order_filter'] = false;
        $this->data['show_show_filter'] = false;

        $this->tpl = 'dialog/content.tpl';
        $this->render();
    }

    protected function render()
    {
        if (file_exists(MIWOWIDGETS_VIEW_PATH . $this->tpl)) {
            extract($this->data);

            require(MIWOWIDGETS_VIEW_PATH . $this->tpl);
        } else {
            exit('Error: Could not load template ' . MIWOWIDGETS_VIEW_PATH . $this->tpl . '!');
        }
    }

    protected function saveRule($delete = false)
    {
        $result = array();

        $this->data['status'] = MiwoWidgetsRequest::getString('status', false);
        $this->data['module'] = MiwoWidgetsRequest::getString('module', '');
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['value'] = MiwoWidgetsRequest::getString('value', '');

        $this->language->load('default');

        if (empty($this->data['module'])){
            $result['error'] = $this->language->get('text_error_module');
        }

        if (empty($this->data['widget_id'])){
            $result['error'] = $this->language->get('text_error_widget_id');
        }

        if (empty($this->data['value'])) {
            $result['error'] = $this->language->get('text_error_value');
        }

        if (isset($result['error'])) {
            return $result;
        }

        if ($this->data['status'] == 1 && $delete == true) {
            $_result = $this->model->deleteRule($this->data);
        } else {
            $_result = $this->model->saveRule($this->data);
        }

        if ($_result > 0) {
            $result['success'] = $this->language->get('text_success');
        } else{
            $result['error'] =  $this->language->get('text_error_save');
        }

        return $result;
    }

    public function saveMultiRule($delete = false)
    {
        $result = array();

         $this->data['status'] = MiwoWidgetsRequest::getString('status', false);
         $this->data['module'] = MiwoWidgetsRequest::getString('module', '');
         $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
         $this->data['value'] = MiwoWidgetsRequest::getCmd('value', array(), '', 'array');

         $this->language->load('default');

         if (empty($this->data['module'])){
             $result['error'] = $this->language->get('text_error_module');
         }

         if (empty($this->data['widget_id'])){
             $result['error'] = $this->language->get('text_error_widget_id');
         }

         if (empty($this->data['value'])) {
             $result['error'] = $this->language->get('text_error_value');
         }

         if (isset($result['error'])) {
             return $result;
         }

         if ($this->data['status'] == 1 && $delete == true) {
             $_result = $this->model->deleteMultiRule($this->data);
         } else {
             $_result = $this->model->saveMultiRule($this->data);
         }

         if ($_result > 0) {
             $result['success'] = $this->language->get('text_success');
         } else{
             $result['error'] =  $this->language->get('text_error_save');
         }

         return $result;
    }

    protected function deleteRule($value = false)
    {
        $result = array();

        $this->data['module'] = MiwoWidgetsRequest::getString('module', '');
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        if ($value) {
            $this->data['value'] = MiwoWidgetsRequest::getString('value', '');
        }

        $this->language->load('default');

        if (empty($this->data['module'])){
            $result['error'] = $this->language->get('text_error_module');
        }

        if (empty($this->data['widget_id'])){
            $result['error'] = $this->language->get('text_error_widget_id');
        }

        if (isset($result['error'])) {
            return $result;
        }

        $_result = $this->model->deleteRule($this->data);

        if ($_result > 0) {
            $result['success'] = $this->language->get('text_success');
        } else{
            $result['error'] = $this->language->get('text_error_delete');
        }

        return $result;
    }

    public function deleteAllRulesByWidgetID($widget_ids)
    {
        $this->data['widget_id'] = $widget_ids;
        $this->model->deleteRule($this->data);
    }

    public function authorize($sidebars)
    {
        return $sidebars;
    }

    protected function prepareWidgets($sidebars, $excluded_widgets)
    {
        foreach ($excluded_widgets as $excluded_widget_id => $excluded_widget) {
            foreach ($sidebars as $sidebar_id => $sidebar) {
                $index = array_search($excluded_widget_id, $sidebar);

                if ($index !== false) {
                    unset($sidebars[$sidebar_id][$index]);
                }
            }

        }

        return $sidebars;
    }

    public function getOrderArgs($order, $args)
    {
        switch ($order) {
            case 'a_z':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'z_a':
                $args['orderby'] = 'title';
                $args['order'] = 'DESC';
                break;
            case 'date_latest':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'date_oldest':
                $args['orderby'] = 'date';
                $args['order'] = 'ASC';
                break;
            case 'modified_latest':
                $args['orderby'] = 'modified';
                $args['order'] = 'DESC';
                break;
            case 'modified_oldest':
                $args['orderby'] = 'modified';
                $args['order'] = 'ASC';
                break;
            default:
                $args['orderby'] = 'ID';
                $args['order'] = 'DESC';
                break;
        }

        return $args;
    }

    public function getCountSelectOptions()
    {
        $this->language->load('default');

        $option = array(
            '10' => '10',
            '20' => '20',
            '20' => '20',
            '40' => '40',
            '50' => '50',
            '-1' => $this->language->get('text_count_all'),
        );

        return $option;
    }

    public function getSettingHtml($settings, $module)
    {
        $this->language->load('module/'.$module);

        $checked_or = 'checked';
        $checked_and = '';

        if (isset($settings[$module.'_mode']) and $settings[$module.'_mode'] == 1){
            $checked_or = '';
            $checked_and = 'checked';
        }

        $html = '';
        $html .= '<tr>';
        $html .= '  <th scope="row"><label>'.$this->language->get('text_setting_title').'</label></th>';
        $html .= '  <td>';
        $html .= '      <input type="radio" name="miwowidget_settings['.$module.'_mode]" value="1" id="miwowidget_settings_'.$module.'_and" '.$checked_and.' />';
        $html .= '      <label for="miwowidget_settings_'.$module.'_and">'.$this->language->get('text_and').'</label>';
        $html .= '      <input type="radio" name="miwowidget_settings['.$module.'_mode]" value="0" id="miwowidget_settings_'.$module.'_or"  '.$checked_or.' />';
        $html .= '      <label for="miwowidget_settings_'.$module.'_or">'.$this->language->get('text_or').'</label>';
        $html .= '  </td>';
        $html .= '</tr>';

        return $html;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');
        $rule['item_name'] = '';

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
