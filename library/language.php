<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsLanguage
{

    private $default = 'en-GB';
    private $directory;
    private $data = array();

    public function __construct()
    {
        $this->directory = get_bloginfo('language');
    }

    public function get($key)
    {
        return (isset($this->data[$key]) ? $this->data[$key] : $key);
    }

    public function load($filename)
    {
        $file = MIWOWIDGETS_LANGUAGE_PATH . $this->directory . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();

            require($file);

            $this->data = array_merge($this->data, $_);
            return;
        }

        $file = MIWOWIDGETS_LANGUAGE_PATH . $this->default . '/' . $filename . '.php';

        if (file_exists($file)) {
            $_ = array();

            require($file);

            $this->data = array_merge($this->data, $_);
        }
    }
}
