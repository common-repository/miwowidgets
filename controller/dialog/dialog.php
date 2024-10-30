<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerDialog extends MiwoWidgetsController
{
    
    public function __construct()
    {
        parent::__construct();
        
        $this->language->load('dialog/dialog');
    }

    public function index()
    {
        $loader = MiwoWidgetsLoader::getInstance();
        $modules = $loader->loadAllModules();

        $modules = array_merge($modules['single'], $modules['common']);
        usort($modules, array($this, '_sort'));

        echo '<div id="miwowidget-top"></div>';

        echo '<div class="miwowidget-tab">';
        foreach ($modules as $module) {
            $module->getTab();
        }
        echo '</div>';

        echo '<div class="miwowidget-content">';
        foreach ($modules as $module) {
            $module->getContent();
        }
        echo '</div>';
    }

    public function quickView()
    {
        $loader = MiwoWidgetsLoader::getInstance();

        $widget_id = MiwoWidgetsRequest::getString('widget_id', '');

        $_rules = $this->model->getWidgetRules($widget_id);
        
        $rules = array();
        
        foreach($_rules as $_rule) {
            $module = $loader->controller('module/'.$_rule->module);
            $rule = $module->getQuickRule($_rule);

            if (!empty($rule)) {
                $rules[] = $rule;
            }
        }

        $this->array_sort_by_column($rules, 'name');

        $this->data['rules'] = $rules;

        $this->language->load('default');
        $this->data['text_hide'] = $this->language->get('text_hide');
        $this->data['text_head_module'] = $this->language->get('text_head_module');
        $this->data['text_head_item'] = $this->language->get('text_head_item');
        $this->data['text_head_status'] = $this->language->get('text_head_status');

        $this->tpl = 'dialog/quick.tpl';
        $this->render();
    }

    public function copyView()
    {
        $widget_id = MiwoWidgetsRequest::getString('widget_id', '');
        $sidebars = wp_get_sidebars_widgets();
        $widgets = array();
        foreach($sidebars as $key => $sidebar) {
            if ($key == 'wp_inactive_widgets' or empty($sidebar)) {
                continue;
            }

            $_widget['disable'] = 'disabled';
            $_widget['name'] = $key;
            $_widget['value'] = $key;
            $widgets[] = $_widget;

            foreach ($sidebar as $widget) {
                $_widget['value'] = $widget;
                $_widget['name'] = $widget;
                
                if ($widget_id == $widget) {
                    $_widget['disable'] = 'disabled';
                } else {
                    $_widget['disable'] = '';
                }

                $widgets[] = $_widget;
            }
        }

        $this->data['widget_id'] = $widget_id;
        $this->data['widgets'] = $widgets;

        $this->language->load('default');
        $this->data['text_hide'] = $this->language->get('text_hide');
        $this->data['text_copy'] = $this->language->get('text_copy');
        $this->data['text_select'] = $this->language->get('text_select');
        $this->data['text_select_span'] = $this->language->get('text_select_span');

        $this->tpl = 'dialog/copy.tpl';
        $this->render();
    }

    public function copy()
    {
        $from_widget_id = MiwoWidgetsRequest::getString('from_widget_id', '');
        $to_widget_ids = MiwoWidgetsRequest::getCmd('to_widget_ids', array(), '', 'array');
        $this->language->load('default');

        if (empty($to_widget_ids)) {
            $result['error'] = $this->language->get('text_error_select_widget');
            echo json_encode($result);
            return;
        }

        $_result = $this->model->copyWidgetRules($from_widget_id, $to_widget_ids);

        if ($_result) {
            $result['success'] = $this->language->get('text_success');
        } else {
            $result['error'] = $this->language->get('text_save_error');
        }

        echo json_encode($result);
    }

    public function resetWidget()
    {
        $widget_id = MiwoWidgetsRequest::getString('widget_id', '');
        $this->language->load('default');

        $_result = $this->model->deleteRule(array('widget_id' => $widget_id));

        if ($_result !== false) {
            $result['success'] = $this->language->get('text_success');
        } else {
            $result['error'] = $this->language->get('text_delete_error');
        }

        echo json_encode($result);
    }

    private function _sort($a, $b)
    {
        return ($a->order < $b->order) ? -1 : 1;
    }

    private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
    {
        $sort_col = array();

        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }
}
