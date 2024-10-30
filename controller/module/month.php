<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerMonth extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'month';
    public $icon    = 'calendar';
    public $order   = 23;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $months = $this->_geyMonthList();

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['months'] =$this->prepareItems($months, $rules);

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
        $this_month = date('F');

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = strtolower($this_month);
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

            if (isset($rules[$id])){
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

    public function _geyMonthList()
    {
        $months = array(
            'january' => $this->language->get('text_january'),
            'february' => $this->language->get('text_february'),
            'march' => $this->language->get('text_march'),
            'april' => $this->language->get('text_april'),
            'may' => $this->language->get('text_may'),
            'june' => $this->language->get('text_june'),
            'july' => $this->language->get('text_july'),
            'august' => $this->language->get('text_august'),
            'september' => $this->language->get('text_september'),
            'october' => $this->language->get('text_october'),
            'november' => $this->language->get('text_november'),
            'december' => $this->language->get('text_december')
        );

        return $months;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $list = $this->_geyMonthList();
        $rule['item_name'] = $list[$_rule->value];

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
