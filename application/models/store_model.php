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
class Store_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function view($group_id=false,$search='')
    {
        /*SELECT * FROM `store_it`
join store_item_groups on store_it.id=store_item_groups.item_id
where store_item_groups.group_id=1*/
        if($group_id)
        {
            $this->db->join('store_item_groups', 'store_it.id=store_item_groups.item_id')
                    ->where('store_item_groups.group_id',$group_id);
        }
        if($search)
        {
        $this->db->like('name',$search);
        }
        $result=$this->db->get('store_it');
        return $result;
    }
    
    public function get_item($item_id)
    {
        $this->db->where('id',$item_id);
        $result=$this->db->get('store_it');
        
        return $result;
    }
    
    public function groups($not_in=false)
    {
        if($not_in) $this->db->where_not_in('id',$not_in);
        $result=$this->db->get('store_groups');
        
        return $result;
    }
    
    //в какие группы входит позиция
    public function item_groups($item_id)
    {
        /*select store_groups.id, store_groups.name 
from `store_groups` 
join store_item_groups on store_item_groups.group_id=store_groups.id
where store_item_groups.item_id=23*/
        $this->db->select('store_groups.id, store_groups.name')
                ->from('store_groups')
                ->join('store_item_groups','store_item_groups.group_id=store_groups.id')
                ->where('store_item_groups.item_id',$item_id);
        $result=$this->db->get();
        return $result;
    }


    //добавление товарной позиции в группу
    public function add_item_to_group($data)
    { 
        $this->db->insert('store_item_groups',$data);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
        
    }
    
    //запрашиваем из БД все картриджи совместимы с данным материалом
    public function cart_rashodka($item_id)
    {
        /*select stages_cartridge.name as stage_name,stages_cartridge.code as stage_code, id_cart, kolvo, cartridge.name
from `cart_rashodka`
join cartridge ON cart_rashodka.id_cart = cartridge.id
join stages_cartridge on stages_cartridge.code=cart_rashodka.stage_code
where rashodnik_id =1*/
        $this->db->select('stages_cartridge.name as stage_name,stages_cartridge.code as stage_code, id_cart, kolvo, cartridge.name')
                ->from('cart_rashodka')
                ->join('cartridge','cart_rashodka.id_cart=cartridge.id')
                ->join('stages_cartridge','stages_cartridge.code=cart_rashodka.stage_code')
                ->where('rashodnik_id',$item_id)
               
                ->order_by('cartridge.name','asc');
        $result=$this->db->get();
        return $result;
    }
    
    public function update_item($item_id,$data)
    {
         $this->db->where('id', $item_id);
        $this->db->update('store_it', $data); 
        return TRUE;
    }
    
    public function insert_journal($data)
    {
        $this->db->insert('store_journal',$data);
        return true;
    }
    
    //создание позиции в складе
    public function add_item($data)
    {
        $this->db->insert('store_it',$data);
        return mysql_insert_id();
    }
     //создание группы в складе
    public function add_group($data)
    {
        $this->db->insert('store_groups',$data);
        return mysql_insert_id();
    }
    public function update_group($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('store_groups',$data);
        return 0;
    }
    
    public function rel_printers($item_id)
    {
        /*select printer_id, printers.name as name, brands.name as brand, cart_rashodka.rashodnik_id
from print_join_cart
join printers on printer_id=printers.id
join brands on printers.brand=brands.id
join cart_rashodka on print_join_cart.cartridge_id=cart_rashodka.id_cart
where cart_rashodka.rashodnik_id=10
order by printers.name asc
 */
       $this->db->select ('printer_id, printers.name as name, brands.name as brand, cart_rashodka.rashodnik_id')
               ->from('print_join_cart')
               ->join('printers','printer_id=printers.id')
               ->join('brands','printers.brand=brands.id')
               ->join('cart_rashodka','print_join_cart.cartridge_id=cart_rashodka.id_cart')
               ->where('cart_rashodka.rashodnik_id',$item_id)
               ->order_by('printers.name','asc');
        
        $query=$this->db->get();
        return $query;
    }
    
   
    public function rel_item_by_printer($name,$not_in=false)
    {
        /*select cartridge.name as cart_name, cartridge.id as cart_id, 
            brands.name as brand_name, printers.name as printer_name
from `cartridge`
join brands on cartridge.brand=brands.id
join print_join_cart on cartridge.id=print_join_cart.cartridge_id
join printers on printers.id=print_join_cart.printer_id
where printers.name like '%1200%'
and cartridge.id not in(73,74)
 */
       //print_r($not_in);
        $this->db->select('cartridge.name as cart_name, cartridge.id as cart_id, 
            brands.name as brand_name, printers.name as printer_name')
                ->from('cartridge')
                ->join('brands','cartridge.brand=brands.id')
                ->join('print_join_cart','cartridge.id=print_join_cart.cartridge_id')
                ->join('printers','printers.id=print_join_cart.printer_id');
        
        if($name) {$this->db->like('printers.name',$name);}
        if(is_array($not_in)) $this->db->where_not_in('cartridge.id',$not_in);
            $this->db->order_by('printers.name','asc');
        
        $query=$this->db->get();
        return $query;
    }        
    
    //выбираем  списанные материалы за период от и до с выборкой по мастеру
    public function writeoff_items($start_date,$end_date,$user_id, $cart_id)
    {
        /*SELECT store_journal.uniq_num, registr_cartridge.name as cart_name, store_it.name as item_name, store_journal.amount, store_journal.item_id, invoices.price
FROM `store_journal` 
join registr_cartridge on registr_cartridge.uniq_num=store_journal.uniq_num
join store_it on store_journal.item_id=store_it.id
join invoices on store_journal.uniq_num=invoices.uniq_num
where date>=1377561600 and date<=1377647999
and user_id=4
and store_journal.order_id=invoices.order_id
order by store_journal.uniq_num asc, store_journal.item_id asc*/
        //$where=
        $this->db->select('store_journal.uniq_num, registr_cartridge.name as cart_name, store_it.name as item_name, 
        store_it.cost, store_journal.amount, store_journal.item_id, invoices.price, date, users.last_name')
                ->from('store_journal')
                ->join('registr_cartridge','registr_cartridge.uniq_num=store_journal.uniq_num')
                ->join ('store_it', 'store_journal.item_id=store_it.id')
                ->join ('invoices', 'store_journal.uniq_num=invoices.uniq_num')
                ->join ('users', 'users.id=user_id')
                ->where('date >=',$start_date)
                ->where('date <=',$end_date)
                ->where_in('user_id',$user_id)
                ->where('store_journal.order_id = invoices.order_id');
        $cart_id ? $this->db->where('registr_cartridge.name_id',$cart_id):false;
        $this->db->order_by('store_journal.uniq_num','asc')
                ->order_by('store_journal.item_id', 'asc');
        $result=$this->db->get();
        return $result;
    }

        
    
     
}

?>