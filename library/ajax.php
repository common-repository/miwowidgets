<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsAjax
{

    protected $loader;

    public function __construct()
    {
        $this->loader = MiwoWidgetsLoader::getInstance();
    }

    public function ajax()
    {
        $this->loader->run();
        exit;
    }
}
