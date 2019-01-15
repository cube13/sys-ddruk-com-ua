<?php
/**
 * Description of systema
 * модели для раздела работы с финансами и учетом денег и взаимрасчетов с клиентами
 *
 * @author cube
 */
class Systema_fin_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    function get_nal($date_start,$date_end,$debet_kredit)
    {
        $this->db->select('sum(sum) as sum,count(id) as count')
                ->where('date >=',$date_start)
                ->where('date <=',$date_end)
                ->where('for_edrpou','3047313536')
                ->where('debet_kredit',$debet_kredit);
        $debet_kredit?false:$this->db->where('locked',1);
        $result=$this->db->get('accounting');
        return $result;
        
    }
    
   // выбираем все закрытые проплаты за определенный период
    //функция принимает значения даты (начало, конец) и едрпоу
    function get_accaunting($date_start,$date_end,$edrpou=false,$for_edrpou=false)
    {
        $this->db->select('date,order_id,edrpou,sum,for_edrpou')
                ->where('date >=',$date_start)
                ->where('date <=',$date_end)
                ->where('debet_kredit',1);
                $edrpou ? $this->db->where('edrpou',$edrpou):false;
                $for_edrpou ? $this->db->where('for_edrpou',$for_edrpou):false;
            $this->db->where('locked',1);
        $result=$this->db->get('accounting');
        return $result;
    }
    
    //берем весь чек заказа
    public function select_invoice($order_id,$uniq_num=false)
    {
        $this->db->where('order_id',$order_id)
                ->where('price !=',0)
                ->where('uniq_num !=','deleted');
        $uniq_num ? $this->db->where('uniq_num',$uniq_num) : false;
        $this->db->order_by('text','asc');
        $result=$this->db->get('invoices');
        return $result;
    }
    
    //выбираем выданные заказы за указаный период
    public function issued_orders($date_start, $date_end,$paymethod)
    {
        /*SELECT order_stages.date_end, order_stages.order_id, users.id, users.first_name, users.last_name, orders.paymethod, orders.hash, accounting.sum
FROM `order_stages` 
join users on users.id=order_stages.manager_id
join orders on orders.id=order_stages.order_id
join accounting on accounting.order_id=order_stages.order_id
WHERE 
(date_end>=1377043200 and date_end<=1377129599)
and stage_code='toclnt'
and accounting.debet_kredit=0
order by users.id asc*/
        $this->db->select('order_stages.date_end, order_stages.order_id, users.id, users.first_name, users.last_name, orders.paymethod, orders.hash,accounting.sum')
                ->from('order_stages')
                ->join('users','users.id=order_stages.manager_id')
                ->join('orders','orders.id=order_stages.order_id')
                ->join ('accounting','accounting.order_id=order_stages.order_id')
                ->where('date_end >=',$date_start)
                ->where('date_end <=',$date_end)
                ->where('stage_code','toclnt')
                ->where('accounting.debet_kredit','0')
                ->where('orders.paymethod',$paymethod)
                ->order_by('users.id','asc');
        $result=$this->db->get();
        return $result;
    }
    
     
}

?>
