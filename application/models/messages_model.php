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
class Messages_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        
    }
    //добавление сообщения
    public function add_message($data)
    {
        $this->db->insert('messages', $data);
        return TRUE;
    }
    
    public function update($mess_id,$data)
    {
        $this->db->where('id', $mess_id);
        $this->db->update('messages', $data); 
        return TRUE;
    }
    
    public function adminlog($data)
    {
        $data['stage_code']='admin_log';
        $this->db->insert('messages', $data);
        return TRUE;
    }
    
    //выбор всех сообщений к утройству в заказе
    public function get_tech($order_id,$serial_num,$stage_code,$order_col,$order_by,$limit=false)
    {
         /*select messages.stage_code, stages_tech.name, text, messages.user_id, users.first_name, users.last_name, add_date 
from `messages` 
left join stages_tech on messages.stage_code=stages_tech.code
join users on users.id=messages.user_id
where order_id='1' and uniq_num='1' and hidden=0
;*/
        $hidden=0;
       // if($this->ion_auth->is_admin()) $hidden=1; //это что б админ видел скрытые и удаленные сообщения
        
        $this->db->select('messages.stage_code, stages_tech.name, text, messages.user_id, users.first_name, users.last_name, add_date')
                ->from('messages')
                ->join('stages_tech', 'messages.stage_code=stages_tech.code','left')
                ->join('users' ,'users.id=messages.user_id')
                ->where('uniq_num',$serial_num)
                ->where('order_id',$order_id)
                ->where_in('hidden',$hidden);
        $stage_code ? $this->db->where('stage_code',$stage_code):false;
        $limit?$this->db->limit($limit):false;
                $this->db->order_by($order_col,$order_by);
        
        
        $result=$this->db->get();
        return $result;
    }
    public function get_cart($order_id,$cart_num,$stage_code,$limit=false)
    {
        $hidden=0;
       // if($this->ion_auth->is_admin()) $hidden=1; //это что б админ видел скрытые и удаленные сообщения
        
        $this->db->select('messages.id, messages.text, messages.user_id, messages.add_date')
                ->from('messages')
               // ->join('users' ,'users.id=messages.user_id')
                ->where('uniq_num',$cart_num)
                ->where('order_id',$order_id)
                ->where_in('hidden',$hidden);
        $stage_code ? $this->db->where('stage_code',$stage_code):false;
        $limit?$this->db->limit($limit):false;
                
        $result=$this->db->get();
        return $result;
    }
    
    //выбор доп контактов и ссобщений к заказу
    public function to_order($order_id,$org_id,$code)
    {
//    select text, stage_code, add_date, user_id
//from messages
//where stage_code like 'info%'
//and order_id=4169 and uniq_num=6225
        $this->db->select ('id,text, stage_code, add_date, user_id')
                ->from('messages')
                ->where('order_id',$order_id)
                ->where('uniq_num',$org_id)
                ->where('hidden',0)
                ->like('stage_code',$code,'after');
        $result=$this->db->get();
        return $result;
    }     
    
    //add contact data to org
    public function add_org_contact($data)
    {
        $this->db->insert('orgs_contacts', $data);
        return TRUE;
    }
    
    public function org_contact($org_id)
    {
        $this->db->select()
                ->from('orgs_contacts')
                ->where('org_id',$org_id)
                ->where('visible',1)
                ->order_by('value','asc');
        return $this->db->get();
    }
    public function update_org_contact($contact_id,$data)
    {
        $this->db->where('id', $contact_id);
        $this->db->update('orgs_contacts', $data); 
        return TRUE;
    }
    
    public function get_user_messages($userId,$stageCode=false)
    {
        /*select `text`,`add_date` from `messages` 
where `stage_code`='usrmsg' 
and `to_user`=1 
and `isread`=0*/
        $stageCode ? true : $stageCode='usrmsg';

        $this->db->select('messages.id, messages.order_id, messages.text, messages.add_date, messages.user_id, '
                . 'users.first_name, users.last_name, messages.subject,messages.isread')
                ->from('messages')
                ->join('users','messages.user_id=users.id')
                ->where('stage_code',$stageCode);
                $userId ? $this->db->where('to_user',$userId) : false;
                $this->db->where('remind_date <',date('U'))
                ->where('hidden',0)
                ->where('isread',0)
                ->order_by('add_date','asc');
        return $this->db->get();        
    }

    public function getTasks($userId)
    {
        /*select `text`,`add_date` from `messages`
where `stage_code`='task'
and `to_user`=1
and `isread`=0*/

        $this->db->select('messages.id, messages.order_id, messages.text, messages.add_date, messages.user_id, '
            . 'users.first_name, users.last_name, messages.subject,messages.isread')
            ->from('messages')
            ->join('users','messages.user_id=users.id')
            ->where('stage_code','task');
        $userId ? $this->db->where('to_user',$userId) : false;
        $this->db->where('remind_date <',date('U'))
            ->where('hidden',0)
            ->where('isread',0)
            ->order_by('add_date','asc');
        return $this->db->get();
    }
    
    public function get_active_user()
    {
        /*select `first_name`,`last_name`,`id` from users where active=1*/
        $this->db->select(`first_name`,`last_name`,`id`)
                ->from('users')
                ->where('active',1);
        return $this->db->get();
    }
    
    
}

?>
