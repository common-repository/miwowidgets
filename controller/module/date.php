<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerDate extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'date';
    public $icon    = 'calendar';
    public $order   = 21;

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_from'] = $this->language->get('text_from');
        $this->data['text_to'] = $this->language->get('text_to');
        $this->data['text_show_widget'] = $this->language->get('text_show_widget');
        $this->data['text_in'] = $this->language->get('text_in');
        $this->data['text_except'] = $this->language->get('text_except');

        $this->data['button_text_save'] = $this->language->get('button_text_save');
        $this->data['button_text_reset'] = $this->language->get('button_text_reset');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $this->data['date_start'] = '';
        $this->data['date_end'] = '';
        $this->data['date_yes'] = '';
        $this->data['date_no'] = '';

        $rule = $this->model->getRules($this->data);
        
        if (isset($rule[$this->data['widget_id']])){
            $value = json_decode($rule[$this->data['widget_id']]->value);
            $this->data['date_start'] = $value->start;
            $this->data['date_end'] = $value->end;

            if(isset($rule[$this->data['widget_id']]) and $rule[$this->data['widget_id']]->status == 0 ) {
                $this->data['date_yes'] = '';
                $this->data['date_no'] = 'checked';
            }
            else if(isset($rule[$this->data['widget_id']])){
                $this->data['date_yes'] = 'checked';
                $this->data['date_no'] = '';
            }
        }

        $this->tpl = 'module/'.$this->module.'.tpl';
        $this->render();
    }

    public function saveRule($delete = false)
    {
        parent::deleteRule();

        $result = parent::saveRule();

        echo json_encode($result);
    }

    public function resetRule()
    {
        $result = parent::deleteRule();

        echo json_encode($result);
    }

    public function getExcludedWidgets()
    {
       $this->data = array();
       $this->data['module'] = $this->module;

       $rules = $this->model->getRules($this->data);

        $excluded_widgets = array();

        foreach ($rules as $key => $rule) {
            $value = json_decode($rule->value);
            $check = $this->_checkInRange($value->start, $value->end);

            if ($rule->status == 0 && $check == true) {
                $excluded_widgets[$key] = $rule;
            }

            if ($rule->status == 1 && $check == false) {
                $excluded_widgets[$key] = $rule;
            }

        }

        return $excluded_widgets;
    }

    public function _checkInRange($start_date, $end_date)
    {
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $now = strtotime("now");

        return (($now >= $start_ts) && ($now <= $end_ts));
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $dates = json_decode($_rule->value);
        $rule['item_name'] = $dates->start .' - '. $dates->end;

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
