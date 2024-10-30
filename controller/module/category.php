<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerCategory extends MiwoWidgetsController
{

    public $type    = 'single';
    public $module  = 'category';
    public $icon    = 'portfolio';
    public $order   = 4;

    public function getContent()
    {
        $this->language->load('module/'.$this->module);
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['button_text_reset_filters'] = $this->language->get('button_text_reset_filters');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        $this->data['show_filter'] = true;
        $this->data['show_search_filter'] = true;
        $this->data['text_search_filter'] = $this->language->get('text_search_filter');

        $this->data['show_order_filter'] = true;
        $this->data['text_order_filter'] = $this->language->get('text_order_filter');
        $this->data['order_filter_values'] = array(
            "a_z" => $this->language->get('text_order_a_z'),
            "z_a" => $this->language->get('text_order_z_a')
        );

        $this->data['show_show_filter'] = true;
        $this->data['text_show_filter'] = $this->language->get('text_show_filter');
        $this->data['show_filter_values'] = array(
            "all" => $this->language->get('text_show_all'),
            "all_selected" => $this->language->get('text_show_all_selected'),
            "all_unselected" => $this->language->get('text_show_all_unselected')
        );

        $this->tpl = 'dialog/content.tpl';
        $this->render();
    }

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $filter_order = MiwoWidgetsRequest::getString('filter_order', 'a_z');
        $filter_title = MiwoWidgetsRequest::getString('filter_title', '');
        $item_count = MiwoWidgetsRequest::getInt('item_count');
        $paged = MiwoWidgetsRequest::getString('paged', '1');

        if (empty($item_count)){
            $item_count = $this->item_count;
        }

        if ($item_count == '-1') {
            $item_count = '';
        }

        $args = array();
        $args = $this->getOrderArgs($filter_order, $args);
        $args['hide_empty'] = false;
        $args['hierarchical'] = true;
        $args['name__like'] = $filter_title;

        $all_categories = get_categories( $args );
        $count = count($all_categories);

        $args['offset'] = $item_count * ($paged -1);
        $args['number'] = $item_count;

        $the_query = get_categories( $args );
        $the_query = $this->_prepareCategoryNames($the_query);

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['categories'] = $this->prepareItems($the_query, $rules);

        $this->data['current'] = $paged;

        if (!empty($item_count)) {
            $this->data['page_count'] = ceil($count / $item_count);
            $this->data['item_count'] = $item_count;

        }
        else {
            $this->data['page_count'] = 1;
            $this->data['item_count'] = '-1';
        }

        $this->data['text_next'] = $this->language->get('text_next');
        $this->data['text_prev'] = $this->language->get('text_prev');
        $this->data['text_pagination'] = sprintf($this->language->get('text_pagination'), $paged, $this->data['page_count']);

        $this->data['text_item_count'] = $this->language->get('text_item_count');
        $this->data['count_select_options'] = $this->getCountSelectOptions();

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
        $query_object = $this->wp_query->get_queried_object();
        $category_id = $query_object->term_id;

        if (empty($category_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $category_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        $filter_select = MiwoWidgetsRequest::getString('filter_select', 'all');

        foreach ($_items as $item) {
            if (isset($rules[$item->term_id])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';

                if($filter_select == 'all_selected') {
                    $item = false;
                }
            }
            else{
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';

                if($filter_select == 'all_unselected') {
                    $item = false;
                }
            }

            if(!empty($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getOrderArgs($order, $args)
    {
        switch ($order) {
            case 'z_a':
                $args['orderby'] = 'name';
                $args['order'] = 'DESC';
                break;
            case 'a_z':
            default:
                $args['orderby'] = 'name';
                $args['order'] = 'ASC';
                break;
        }

        return $args;
    }

    private function _prepareCategoryNames($categories)
    {
        foreach($categories as $key => $category) {
            $categories[$key]->name = $this->_getAllPathName($category);
        }

        return $categories;
    }

    private function _getAllPathName($category, $name = '')
    {
        if ($category->parent == 0) {
            if(empty($name)) {
                return $category->name;
            }
            else{
                return $name;
            }
        }
        else {
            $parent = get_category($category->parent);
            $name = $parent->name. ' -> ' .$category->name;

            return $this->_getAllPathName($parent, $name);
        }
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $item = get_category($_rule->value);
        $rule['item_name'] = $item->name;

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
