<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerPost extends MiwoWidgetsController
{

    public $type    = 'single';
    public $module  = 'post';
    public $icon    = 'admin-post';
    public $order   = 2;

    public function getContent()
    {
        $this->language->load('module/'.$this->module);
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['button_text_reset_filters'] = $this->language->get('button_text_reset_filters');

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');

        #filters
        $this->data['show_filter'] = true;
        $this->data['show_search_filter'] = true;
        $this->data['text_search_filter'] = $this->language->get('text_search_filter');

        $this->data['show_order_filter'] = true;
        $this->data['text_order_filter'] = $this->language->get('text_order_filter');
        $this->data['order_filter_values'] = array(
            "a_z" => $this->language->get('text_order_a_z'),
            "z_a" => $this->language->get('text_order_z_a'),
            "date_latest" => $this->language->get('text_order_date_latest'),
            "date_oldest" => $this->language->get('text_order_date_oldest'),
            "modified_latest" => $this->language->get('text_order_modified_latest'),
            "modified_oldest" => $this->language->get('text_order_modified_oldest')
        );

        $this->data['show_show_filter'] = true;
        $this->data['text_show_filter'] = $this->language->get('text_show_filter');
        $this->data['show_filter_values'] = array(
            "all" => $this->language->get('text_show_all'),
            "all_selected" => $this->language->get('text_show_all_selected'),
            "all_unselected" => $this->language->get('text_show_all_unselected')
        );

        #set tpl
        $this->tpl = 'dialog/content.tpl';

        $this->render();
    }

    public function loadData()
    {
        $this->data['text_title'] = $this->language->get('text_title');

        $filter_order = MiwoWidgetsRequest::getString('filter_order', 'a_z');
        $filter_title = MiwoWidgetsRequest::getString('filter_title', '');
        $item_count = MiwoWidgetsRequest::getString('item_count');
        $paged = MiwoWidgetsRequest::getString('paged', '1');

        if(empty($item_count)){
            $item_count = $this->item_count;
        }

        $args = array();
        $args['paged'] = $paged;
        $args['post_type'] = 'post';
        $args['posts_per_page'] = $item_count;
        $args['post_title_like'] = $filter_title;
        $args = $this->getOrderArgs($filter_order, $args);

        $the_query = new WP_Query($args);

        $this->data['current'] = $paged;
        $this->data['page_count'] = $the_query->max_num_pages;

        $this->data['module'] = $this->module;
        $this->data['widget_id'] = MiwoWidgetsRequest::getString('widget_id', '');
        $this->data['status'] = '0';

        $rules = $this->model->getRules($this->data, 'value');
        $this->data['posts'] = $this->prepareItems($the_query, $rules);

        $this->data['text_next'] = $this->language->get('text_next');
        $this->data['text_prev'] = $this->language->get('text_prev');
        $this->data['text_pagination'] = sprintf($this->language->get('text_pagination'), $paged, $the_query->max_num_pages);

        $this->data['item_count'] = $item_count;
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
        $post_id = $query_object->ID;

        if (empty($post_id)) {
            return array();
        }

        $this->data = array();
        $this->data['module'] = $this->module;
        $this->data['value'] = $post_id;
        $this->data['status'] = '0';

        $excluded_widgets = $this->model->getRules($this->data);

        return $excluded_widgets;
    }

    public function prepareItems($_items, $rules)
    {
        $items = array();

        $filter_select = MiwoWidgetsRequest::getString('filter_select', 'all');

        foreach($_items->posts as $item) {
            if (isset($rules[$item->ID])){
                $item->checkstate = 'unchecked';
                $item->checkstatenext = 'checked';

                if ($filter_select == 'all_selected') {
                    $item = false;
                }
            } else{
                $item->checkstate = 'checked';
                $item->checkstatenext = 'unchecked';

                if ($filter_select == 'all_unselected') {
                    $item = false;
                }
            }

            if (!empty($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getQuickRule($_rule)
    {
        $rule['name'] = $this->language->get('text_title');

        $item = get_post($_rule->value);
        $rule['item_name'] = $item->post_title;

        if ($_rule->status) {
            $rule['status'] = 'Visible Widget';
        } else {
            $rule['status'] = 'Hidden Widget';
        }

        return $rule;
    }
}
