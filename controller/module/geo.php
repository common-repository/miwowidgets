<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsControllerGeo extends MiwoWidgetsController
{

    public $type    = 'common';
    public $module  = 'geo';
    public $icon    = 'location';
    public $order   = 31;

    public function loadData()
    {
        $this->language->load('default');
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_pro'] = $this->language->get('text_pro');

        $this->data['module'] = $this->module;

        $this->tpl = 'module/pro.tpl';
        $this->render();
    }
}
