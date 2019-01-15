<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of systema
 *
 * @author cube
 */
class Orders_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        
       
    }
    
   public function GetOrders($userId=false, $orderId=false, $isClosed=false)
   {
       /*
         select orders.id, hash, date_create, manager_id,org_id, org_name ,closed, paymethod,discount,type, users.last_name
from orders
join users on users.id=orders.manager_id
where closed=0
       */

       $this->db->select('orders.id, hash, date_create, manager_id,org_id, org_name ,closed, 
       paymethod,discount,type, users.last_name, users.first_name')
           ->join('users','users.id=orders.manager_id');
           $isClosed ? false : $this->db->where('closed','0');
           $userId ? $this->db->where('manager_id',$userId) : false;
           $orderId ? $this->db->like('orders.id',$orderId) : false;

       $this->db->order_by('date_create','desc');
          return $this->db->get('orders');

   }

    public function GetStage($orderId,$stageCode)
    {
        $this->db->select('date_start,date_end,info,sort,action_flag, manager_id, users.first_name')
            ->from('order_stages')
            ->join('users','users.id=order_stages.manager_id')
            ->where('order_id',$orderId)
            ->where('stage_code',$stageCode);

        $query=$this->db->get();
        return $query;
    }

    public function ViewSimple($orgId=false,$orderId=false)
    {
        /*SELECT orders.id, orders.hash, orders.date_create, orgs.short_name
FROM  `orders`
JOIN orgs ON orgs.id = orders.org_id
WHERE org_id=*/
        $this->db->select('orders.id, orders.hash, orders.manager_id, orders.date_create, orgs.short_name, orders.org_id,
        users.last_name, users.first_name')
            ->from('orders')
            ->join('orgs','orgs.id = orders.org_id')
            ->join('users','users.id = orders.manager_id');
        $orgId ? $this->db->where('orders.org_id',$orgId) : false;
        $orderId ? $this->db->like('orders.id', $orderId, 'both') : false;
        $this->db->order_by('orders.id','desc');
        $result=$this->db->get();
        return $result;
    }

   public function Update($id, $data)
   {
       $this->db->where('id', $id);
       $result=$this->db->update('orders',$data);
       return $result;
   }

}