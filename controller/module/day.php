<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerDay extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'day';
    public $icon    = 'calendar';
    public $order   = 22;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $days = $this->_geyDayList();

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['days'] =$this->prepareItems($days, $rules);

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
        $today = date('l');

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = strtolower($today);
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

    public function _geyDayList()
    {
        $days = array(
            'monday' => $this->language->get('text_monday'),
            'tuesday' => $this->language->get('text_tuesday'),
            'wednesday' => $this->language->get('text_wednesday'),
            'thursday' => $this->language->get('text_thursday'),
            'friday' => $this->language->get('text_friday'),
            'saturday' => $this->language->get('text_saturday'),
            'sunday' => $this->language->get('text_sunday')
        );

        return $days;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $list = $this->_geyDayList();
        $rule['item_name'] = $list[$_rule->value];

        if($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
