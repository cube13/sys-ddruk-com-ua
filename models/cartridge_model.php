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
class Cartridge_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    //функция подсчета количества этапов картриджей (ТОЛЬК ОДНОГО ТИПА) 
    //принимает код_этапа, закрыт этап или нет, айди заказа
    // возвращает количество
    public function get_stage_simple($stage_code,$is_closed=false,$order_id=false)
    {
        
        $this->db->select('count(id) as count')
                ->where('stage_code',$stage_code)
                ->where('date_start !=',0);
        $is_closed ? $this->db->where('date_end !=',0):$this->db->where('date_end',0);
        $order_id ? $this->db->where('order_id',$order_id):false;
        $result=$this->db->get('cartridge_stages');
        
        return $result;
    }
    
    public function get_cart_parts($cart_id,$stage_code)
    {
        /*select store_it.name, cart_rashodka.kolvo, store_it.units, store_it.id
from store_it
join cart_rashodka on cart_rashodka.rashodnik_id=store_it.id
where cart_rashodka.id_cart=89 and cart_rashodka.stage_code='inrfl'*/
        $this->db->select('store_it.name, cart_rashodka.kolvo, store_it.units, store_it.id')
                
                ->join('cart_rashodka','cart_rashodka.rashodnik_id=store_it.id')
                ->where('cart_rashodka.id_cart',$cart_id)
                ->where('cart_rashodka.stage_code',$stage_code);
        $result=$this->db->get('store_it');
        return $result;
    }
    
    public function get_cart_item($cart_id)
    {
        /*select cartridge.id,  brands.name  as brand, cartridge.name, cartridge.picture
 from cartridge
join brands on brands.id=cartridge.brand
where cartridge.id=79
         */
        $this->db->select('cartridge.id,  brands.name  as brand, cartridge.name, cartridge.picture')
                ->join('brands','brands.id=cartridge.brand')
                ->where('cartridge.id',$cart_id);
        $result=$this->db->get('cartridge');
        return $result;
    }
    
    public function cart_rashodka($id_cart)
    {
    /*запрашиваем расходные материалы для картриджа
     * select cart_rashodka.id_cart,cart_rashodka.rashodnik_id, store_it.name, store_it.units,cart_rashodka.kolvo,cart_rashodka.stage_code 
from `cart_rashodka`
join store_it on store_it.id=cart_rashodka.rashodnik_id
where id_cart=89
     */
        $this->db->select('cart_rashodka.id_cart,cart_rashodka.rashodnik_id, store_it.name, store_it.units,cart_rashodka.kolvo,cart_rashodka.stage_code')
                ->from('cart_rashodka')
                ->join('store_it','store_it.id=cart_rashodka.rashodnik_id')
                ->where('id_cart',$id_cart)
                ->order_by('cart_rashodka.stage_code','desc');
        $result=$this->db->get();
        return $result;
    }
    
    public function update_materials($id_cart,$rashodnik_id,$data)
    {
        $this->db->where('id_cart',$id_cart);
        $this->db->where('rashodnik_id',$rashodnik_id);
        $this->db->update('cart_rashodka', $data); 
        return true; 
    }
    
    public function remove_material($id_cart,$material_id)
    {
        $this->db->where('id_cart', $id_cart);
        $this->db->where('rashodnik_id', $material_id);
        $this->db->delete('cart_rashodka');
        return true;
    }
    
    public function add_material($data)
    {
        $this->db->insert('cart_rashodka', $data); 
        return true;
    }
    //timeline
    public function timeline($order_id,$cart_num)
    {
        /*select `date_start`,`date_end`,`stage_code`
from `cartridge_stages` 
where order_id=5552	and `cart_num`='P11283'*/
        $this->db->select('`date_start`,`date_end`,`stage_code`')
                ->from('cartridge_stages')
                ->where('order_id',$order_id)
                ->where('cart_num',$cart_num)
                ->order_by('id','asc');
        $result=$this->db->get();
        return $result;        
    }
    public function get_count($start_date,$end_date,$org_id=false)
    {
        /*select count(*) as count
from `cartridge_stages` 
join orders on cartridge_stages.order_id=orders.id
where org_id!=11
and stage_code='inrfl'
and date_end>=1380585600
and date_end<= 1383263999*/
        $this->db->select('count(*) as count')
                ->from('cartridge_stages')
                ->where('stage_code','inrfl')
                ->where('date_end >=',$start_date)
                ->where('date_end <=', $end_date);
        if($org_id)
        {
            $this->db->join('orders','cartridge_stages.order_id=orders.id')
                    ->where('org_id',$org_id);
        }
        return $this->db->get()->row()->count;
    }
}

?>
