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
class Tech_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
  // выбор этапов работ по списку  техники
    public function tech_stages($order_id,$stage_code=false,$serial_num=false,$date_end=false)
    {
        /*select tech_stages.id, tech_stages.serial_num, date_start, date_end, info, stage_code, 
            tech_stages.adres, registr_techs.name as tech_name, stages_tech.name as stage_name, 
            tech_stages.order_id,stages_tech.sort as stage_sort,message,
            registr_techs.name_id as tech_id,orders.hash,orders.contacter,orgs.short_name as org_name,
         
from  tech_stages
               join orders on tech_stages.order_id=orders.id
               join registr_techs on tech_stages.serial_num=registr_techs.serial_num
               join stages_tech on tech_stages.stage_code=stages_tech.code
                join orgs on orders.org_id=orgs.id
                where date_start !='0'*/
         
        $this->db->select('tech_stages.id, tech_stages.serial_num, date_start, date_end, info, stage_code, 
            tech_stages.adres, registr_techs.name as tech_name, stages_tech.name as stage_name, 
            tech_stages.order_id,stages_tech.sort as stage_sort, message,
            registr_techs.name_id as tech_id,orders.hash,orders.contacter,orgs.short_name as org_name')
                ->from('tech_stages')
                ->join('orders','tech_stages.order_id=orders.id')
                ->join('registr_techs','tech_stages.serial_num=registr_techs.serial_num')
                ->join('stages_tech','tech_stages.stage_code=stages_tech.code')
                ->join('orgs','orders.org_id=orgs.id')
                ->where('date_start !=','0')
                ->order_by('tech_stages.sort','asc')
                ->order_by('stage_sort','desc');
        $date_end ? $this->db->where('date_end !=','0') : $this->db->where('date_end','0');
        if($serial_num) $this->db->where('tech_stages.serial_num',$serial_num);
        if($order_id) $this->db->where('order_id',$order_id);
        if($stage_code)$this->db->where_in('stage_code',$stage_code);

        $result=$this->db->get();
        return $result;
    }
    
   
   
    //выбор всех этапов работ по одной еденице техники
    public function stages_of_tech($order_id,$serial_num)
    {
         /*SELECT `adres`,`info`,`stage_code`,stages_tech.name as stage_name,`manager_id`,`date_end`,`date_start` FROM `tech_stages` 
join stages_tech on stages_tech.code=tech_stages.stage_code
WHERE `serial_num`='G58301000RN06302131' and order_id='1604';*/
        $this->db->select('adres,info,stage_code,stages_tech.name as stage_name,manager_id,date_end,date_start')
                ->from('tech_stages')
                ->join('stages_tech','stages_tech.code=tech_stages.stage_code')
                ->where('serial_num',$serial_num)
                ->where('order_id',$order_id)
                ->order_by('stages_tech.sort','asc');

        $result=$this->db->get();
        return $result;
    }
    
    
    //запрос информации о единице техники
    public function tech_info($serial_num)
    {
        /*select registr_techs.name, registr_techs.first_date, registr_techs.name_id, registr_techs.type_id,
orgs.id as org_id, orgs.short_name as org_name
from `registr_techs` 
join orgs on orgs.id=registr_techs.org_id
where registr_techs.serial_num='G58301000RN06302131'*/
        $this->db->select('registr_techs.name, registr_techs.first_date, registr_techs.name_id, registr_techs.type_id,
orgs.id as org_id, orgs.short_name as org_name')
                ->from('registr_techs')
                ->join('orgs','orgs.id=registr_techs.org_id')
                ->where('registr_techs.serial_num',$serial_num);
        
        $result=$this->db->get();
                return $result;
    }
    
    //внесение техники в реестр
    public function add_tech($serial_num,$data)
    {
        $this->db->select('id')->where('serial_num',$serial_num);
        $result=$this->db->get('registr_techs');
        
        if(!$result->num_rows())
        {
            $this->db->select('name')->from('tech_types')->where('id',$data['type_id']);
            $tech=$this->db->get();
            $data['name']=$tech->row()->name.' '.$data['name'];
            $data['first_date']=  date('U');
            $this->db->insert('registr_techs', $data);
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function timeline($order_id,$serial_num)
    {
        /*select `date_start`,`date_end`,`stage_code`
from `tech_stages` 
where order_id=5552	and `serial_num`='P11283'*/
        $this->db->select('`date_start`,`date_end`,`stage_code`')
                ->from('tech_stages')
                ->where('order_id',$order_id)
                ->where('serial_num',$serial_num)
                ->order_by('id','asc');
        $result=$this->db->get();
        return $result;        
    }
     
}

?>
