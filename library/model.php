<?php
/**
 * @package        MiwoWidgets
 * @copyright      2009-2016 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

class MiwoWidgetsModel
{

    public $table;
    public $db;

    public function  __construct()
    {
        $this->db = $GLOBALS['wpdb'];
        $this->table = $this->db->prefix . 'miwowidgets';
        $this->checkTable();
    }

    public function saveRule($data)
    {
        $query = "INSERT INTO " . $this->table . " (`widget_id`, `module`, `value`, `value_md5`, `status`)
                    VALUES('".$data['widget_id']."', '".$data['module']."', '".$data['value']."', '".md5($data['value'])."', '".$data['status']."')
                    ON DUPLICATE KEY UPDATE `status` = '".$data['status']."'";

        return $this->db->query($query);
    }

    public function saveMultiRule($data)
    {
        $values = array();

        foreach($data['value'] as $value){
            $values[]= "('".$data['widget_id']."', '".$data['module']."', '".$value."', '".md5($value)."', '".$data['status']."')";
        }

        $query = "INSERT INTO " . $this->table . " (`widget_id`, `module`, `value`, `value_md5`, `status`)
                    VALUES ". implode(',', $values ) ."
                    ON DUPLICATE KEY UPDATE `status` = '".$data['status']."'";

        return $this->db->query($query);
    }

    public function deleteRule($data)
    {
        $query = "DELETE FROM " . $this->table . " WHERE `widget_id` = '" . $data['widget_id'] . "'";

        if (! empty($data['module']) ) {
            $query .= " AND `module` = '" . $data['module'] . "'";
        }

        if (! empty($data['value']) ) {
            $query .= " AND `value` = '" . $data['value'] . "'";
        }

        return $this->db->query($query);
    }

    public function deleteMultiRule($data)
    {
        $ids = implode("', '", $data['value']);
        $ids = "'".$ids."'";

        $query = "DELETE FROM " . $this->table . " WHERE `widget_id` = '" . $data['widget_id'] . "' AND `module` = '" . $data['module'] ."'";
        if (! empty($data['value']) ) {
            $query .= " AND  `value` IN (".$ids.")";
        }

        $this->db->query($query);
        
        return true;
    }

    public function reset($widget_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE widget_id = '" . $widget_id . "'";

        return $this->db->query($query);
    }

    public function getRules($data, $key = 'widget_id', $type = "OBJECT_K")
    {
        $select = array();
        $select['widget_id'] = '`widget_id`';
        $select['module'] = '`module`';
        $select['value'] = '`value`';
        $select['status'] = '`status`';

        unset($select[$key]);
        array_unshift($select, '`'.$key.'`');

        $select = implode(', ', $select);

        $query = "SELECT " . $select . " FROM " . $this->table . " WHERE 1=1";

        $where = '' ;
        if (!empty($data['widget_id'])) {
            $where .= " AND `widget_id` = '" . $data['widget_id'] . "'";
        }

        if (!empty($data['module'])) {
            $where .= " AND `module` = '" . $data['module'] . "'";
        }

        if (!empty($data['value'])) {
            $where .= " AND `value` = '" . $data['value'] . "'";
        }

        if (isset($data['status'])) {
            $where .= " AND `status` = '" . $data['status'] . "'";
        }

        $query .= $where;


        return $this->db->get_results($query, $type);
    }

    public function hasRule($widget_id)
    {
        $query = "SELECT COUNT(1) AS total FROM " . $this->table . " WHERE `widget_id` = '" . $widget_id . "' AND `mod` != 'individual'";
        $count = $this->db->get_var($query);

        if ($count > 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function checkTable()
    {
        $query = "SHOW TABLES LIKE '" . $this->table . "'";
        $result = $this->db->get_var($query);

        if (!empty($result)) {
            $_SESSION['miwowidgets_loaded'] = true;
            return;
        }

        $query = "CREATE TABLE IF NOT EXISTS `" . $this->table . "` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `widget_id` varchar(70) NOT NULL,
          `module` varchar(70) NOT NULL,
          `value` longtext NOT NULL,
          `value_md5` varchar(255) NOT NULL,
          `status` tinyint(1) DEFAULT NULL,
		  `mode` tinyint(1) DEFAULT NULL,
           PRIMARY KEY (`id`),
           UNIQUE KEY `unique_key` (`widget_id`, `module`, `value_md5`),
           KEY `key` (`widget_id`, `module`)
        )  ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $this->db->get_var($query);

    }

    public function getBPgroups($id = null)
    {
        $bp = &$GLOBALS['bp'];

        $table = $bp->groups->table_name;
        $query = "SELECT `id`, `name` FROM " . $table;

        if (!empty($id)) {
            $query .= " WHERE id='".$id."'";
        }

        $query .= " ORDER BY name";

        $groups = $this->db->get_results($query);

        return $groups;
    }

    public function getBPgroupName($id)
    {
        $bp = &$GLOBALS['bp'];
        $table = $bp->groups->table_name;
        $query = "SELECT `name` FROM " . $table . " WHERE id='" .$id. "'";

        $name = $this->db->get_var($query);

        return $name;
    }

    public function getRulledWidgets($widget_id)
    {
        $query = "SELECT DISTINCT(widget_id) FROM " . $this->table . " WHERE widget_id <> '".$widget_id."'";
        
        return $this->db->get_results($query);
    }

    public function getWidgetRules($widget_id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE widget_id = '".$widget_id."'";
        
        return $this->db->get_results($query);
    }

    public function copyWidgetRules($from_widget_id, $to_widget_ids)
    {
        #delete old rules
        $query = "DELETE FROM " . $this->table . " WHERE widget_id IN ('".implode("', '", $to_widget_ids)."')";
        $del_result = $this->db->query($query);

        $rules = $this->getRules(array('widget_id'=>$from_widget_id), 'widget_id', 'OBJECT');

        $insert_query = "INSERT INTO " . $this->table . " (`widget_id`, `module`, `value`, `value_md5`, `status`, `mode`) VALUES #values#;";

        foreach ($rules as $rule) {
            $values[] = "('#widget_id#','".$rule->mod."','".$rule->value."','".md5($rule->value)."','".$rule->status."','".$rule->mode."')";
        }

        $values = implode(',', $values);
        $insert_query = str_replace('#values#', $values, $insert_query);

        foreach ($to_widget_ids as $to_widget_id) {
            $query = str_replace('#widget_id#', $to_widget_id, $insert_query);
            $result = $this->db->query($query);
        }

        return $result;
    }
}
