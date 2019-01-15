<?php

class Subscription_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function view()
    {
        $this->db->select('subscription.id,subscription.number,subscription.capacity,subscription.used, '
                . 'subscription.is_active','subscription.activation_date')
                ->from('subscription')->order_by('number','asc');
        return $this->db->get();
    }
    
    public function create_charges($start_num,$end_num,$capacity)
    {
        for($num=$start_num;$num<=$end_num;$num++)
        {
            $this->db->select('number')->from('subscription')->where('number',$num);
            $result=$this->db->get();
                
            print_r($result);
            echo br();
            if(!$result->num_rows())
            {
                $data=array('number'=>$num,
                            'capacity'=>$capacity,
                            'used'=>'0',
                            'is_active'=>'0',
                            'activation_date'=>'0');
                $this->db->insert('subscription',$data);
            }
        }
    }
     
}

?>