<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Функции обменна данными с БД для картриджей
 *
 * @author cube
 */
class Stat_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function tech($date_start,$date_end)
    {
        /*select order_id,tech_stages.serial_num, date_end, registr_techs.name
from `tech_stages` 
join registr_techs on registr_techs.serial_num=tech_stages.serial_num
where `stage_code`='torpr' 
and date_end>=1383264000
and date_end<=1385855999
order by tech_stages.date_end*/
        $this->db->select ('order_id,tech_stages.serial_num, date_end, registr_techs.name')
                ->from('tech_stages')
                ->join('registr_techs','registr_techs.serial_num=tech_stages.serial_num')
                ->where('stage_code','torpr')
                ->where('date_end >=',$date_start)
                ->where('date_end <=',$date_end)
                ->order_by('tech_stages.date_end',desc);
        $result=$this->db->get();
        return $result;
    }
        
     
}

?>
