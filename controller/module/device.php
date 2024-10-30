<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerDevice extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'device';
    public $icon    = 'smartphone';
    public $order   = 50;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $devices = $this->_getDeviceList();
        $rules = $this->model->getRules($this->data, 'value');

        $this->data['devices'] = $this->prepareItems($devices, $rules);

        $this->tpl = 'module/'.$this->module.'.tpl';
        $this->render();
    }

    public function saveRule($delete = false)
    {
        $result = parent::saveRule(true);

        echo json_encode($result);
    }

    public function saveMultiRule($delete = false)
    {
        $result = parent::saveMultiRule(true);

        echo json_encode($result);
    }

    public function getExcludedWidgets()
    {
        $device_id = $this->_detectDevice();

        if (empty($device_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $device_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        foreach($_items as $id => $title) {
            $item        = new stdClass();
            $item->id    = $id;
            $item->title = $title;

            if(isset($rules[$id])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';
            }
            else{
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';
            }

            $items[] = $item;
        }

        return $items;
    }

    public function _getDeviceList()
    {
        $device = array(
            'desktop'    =>  'Desktop',
            'mobile'     =>  'Mobile'
        );

        return $device;
    }

    public static function _detectDevice()
    {
        if (wp_is_mobile()) {
            return 'mobile';
        }
        else {
            return 'desktop';
        }
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $list = $this->_getDeviceList();
        $rule['item_name'] = $list[$_rule->value];

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
