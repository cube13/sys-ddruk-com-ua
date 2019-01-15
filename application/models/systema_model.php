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
class Systema_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        
       
    }
    
    public function techtypes($id=FALSE)
    {
        $id ? $this->db->where('id',$id):false;
        $query=$this->db->get('tech_types');
        return $query;
    }
    
    public function create_techtype($name)
    {
        $data=array('name'=>$name);
        $this->db->insert('tech_types', $data);
        
        return TRUE;
    }
    public function techtype_update($typeid,$typedata)
    {
        $this->db->where('id', $typeid);
        $this->db->update('tech_types', $typedata); 
        return TRUE;

    }
     
    public function create_group($name, $desc)
    {
        $group_data=array(
            'name'=>$name,
            'description'=>$desc
        );
        $this->db->insert('groups', $group_data);
        
        return TRUE;
    }
    
    public function up_group($group_id)
    {
        $this->db->query("update `groups` set `sort`=`sort`-1 where `id`=".$group_id);
        return TRUE;
    }
    
    public function down_group($group_id)
    {
        $this->db->query("update `groups` set `sort`=`sort`+1 where `id`=".$group_id);
        return TRUE;
    }
    
    public function group_update($groupid,$groupdata)
    {
        $this->db->where('id', $groupid);
        $this->db->update('groups', $groupdata); 
        return TRUE;

    }
    
     
    public function partner_create($partner_data=array())
    {
        $this->db->insert('orgs', $partner_data);
        
        return TRUE;
    }
    
    public function partner_update($partnerid,$partnerdata)
    {
        $this->db->where('id', $partnerid);
        $this->db->update('orgs', $partnerdata); 
        return TRUE;
    }
    
    public function view_partners($orderby=FALSE,$id=FALSE)
    {
        if($id) $this->db->where('id',$id);
        if($orderby) $this->db->order_by($orderby,"asc");
        $query=$this->db->get('orgs');
        return $query;
    }
    
    public function select_partners($orderby=FALSE,$short_name=FALSE)
    {
        if($short_name) $this->db->like('short_name',$short_name);
        if($orderby) $this->db->order_by($orderby,"asc");
        $query=$this->db->get('orgs');
        return $query;
    }
    
     //подсчет количетсва картриджей в заказе
    public function cartridges_in_order($order_id)
    {
        /*SELECT registr_cartridge.name, COUNT( registr_cartridge.name ) AS count
FROM  `registr_cartridge` 
JOIN  `cartridge_stages` ON registr_cartridge.uniq_num = cartridge_stages.cart_num
WHERE cartridge_stages.order_id =441
AND cartridge_stages.stage_code =  'inofc'
GROUP BY registr_cartridge.name*/
        
        $this->db->select('registr_cartridge.name, COUNT( registr_cartridge.name ) AS count')
                ->from('registr_cartridge')
                ->join('cartridge_stages','registr_cartridge.uniq_num = cartridge_stages.cart_num')
                ->where('cartridge_stages.order_id',$order_id)
                ->where('cartridge_stages.stage_code','inofc')
                ->group_by('registr_cartridge.name');
        $result=$this->db->get();
        
        return $result;                
        
    }
    
    //подсчет количетсва техники в заказе
      public function techs_in_order($order_id)
    {
        /*SELECT registr_techs.name, COUNT( registr_techs.name ) AS count
FROM  `registr_techs` 
JOIN  `tech_stages` ON registr_techs.serial_num = tech_stages.serial_num
WHERE tech_stages.order_id =1136
AND tech_stages.stage_code =  'inofc'
GROUP BY registr_techs.name*/
        
        $this->db->select('registr_techs.name, COUNT( registr_techs.name ) AS count')
                ->from('registr_techs')
                ->join('tech_stages','registr_techs.serial_num = tech_stages.serial_num')
                ->where('tech_stages.order_id',$order_id)
                ->where('tech_stages.stage_code','inofc')
                ->group_by('registr_techs.name');
        $result=$this->db->get();
        
        return $result;                
        
    }



    public function select_cartridges($orderby=FALSE,$name=FALSE,$not_in=false)
    {
        /*SELECT cartridge.name as cart_name, cartridge.id as cart_id, 
            brands.name as brand_name, printers.name as printer_name
FROM `cartridge`
join brands on cartridge.brand=brands.id
join print_join_cart on cartridge.id=print_join_cart.cartridge_id
join printers on printers.id=print_join_cart.printer_id
 */
       //print_r($not_in);
        $this->db->select('cartridge.name as cart_name, cartridge.id as cart_id, 
            brands.name as brand_name, printers.name as printer_name')
                ->from('cartridge')
                ->join('brands','cartridge.brand=brands.id')
                ->join('print_join_cart','cartridge.id=print_join_cart.cartridge_id')
                ->join('printers','printers.id=print_join_cart.printer_id');
        
        if($name) {$this->db->like('cartridge.name',$name);$this->db->or_like('printers.name',$name);}
        if(is_array($not_in)) $this->db->where_not_in('cartridge.id',$not_in);
        if($orderby) $this->db->order_by($orderby,"asc");
        
        $query=$this->db->get();
        return $query;
    }
    
    public function view_stages($table)
    {
        $this->db->order_by("sort","asc");
        $query=$this->db->get('stages_'.$table);
        return $query;
    }
     public function up_stage($table,$id)
    {
        $this->db->query("update `stages_".$table."` set `sort`=`sort`-1 where `id`=".$id);
        return TRUE;
    }
    
    public function down_stage($table,$id)
    {
        $this->db->query("update `stages_".$table."` set `sort`=`sort`+1 where `id`=".$id);
        return TRUE;
    }
    
    //вносим запись в журнал склада какие материалы использовали
     public function sklad_log_entry($tovar,$kolvo,$rashod_prihod,$cart_num=false,$order_id=false)
    {
         $data['id_tovar']=$tovar;
         $rashod_prihod?$rp=1:$rp=-1;
         $data['kolvo']=$kolvo*$rp;
         $data['rashod_prihod']=$rashod_prihod;
         $data['date']=date('U');
         $cart_num?$data['cart_num']=$cart_num:false;
         $order_id?$data['order_id']=$order_id:false;
        
         $this->db->insert('jurnal', $data);
        
        return TRUE;
    }
    
     public function order_create($order_data=array())
    {
         $this->db->select('short_name')
                 ->from('orgs')
                 ->where('id',$order_data['org_id']);
         $query=$this->db->get();
         $order_data['org_name']=$query->row()->short_name;
         $this->db->insert('orders', $order_data);
        
        return TRUE;
    }
    //просмотр истории заказов в организации и вообще для вывода на главную для всех
    public function view_orders_table($org_id=false,$closed=false,$order_id=false,$limit=false)
    {
        /*SELECT orders.id, orders.hash, orders.date_create, orgs.short_name, 
          order_stages.stage_code, order_stages.date_end
FROM  `orders` 
JOIN orgs ON orgs.id = orders.org_id
JOIN order_stages ON orders.id = order_stages.order_id
WHERE org_id=*/
        $this->db->select('orders.id, orders.hash, orders.manager_id, orders.date_create, orgs.short_name,
            order_stages.stage_code, order_stages.date_start, order_stages.date_end,orders.org_id,orders.contacter,order_stages.info')
                ->from('orders')
                ->join('orgs','orgs.id = orders.org_id')
                ->join('order_stages','orders.id = order_stages.order_id')
                ->where('closed',$closed);
               $org_id ? $this->db->where('orders.org_id',$org_id) : false;
               $order_id ? $this->db->like('orders.id', $order_id, 'both') : false;
               $this->db->order_by('orders.id','desc');
               $limit ? $this->db->limit(500) : false;
$result=$this->db->get();
return $result;
    }




    public function add_cartridge($cart_num,$data)
    {
        $this->db->select('id')->where('uniq_num',$cart_num);
        $result=$this->db->get('registr_cartridge');
        
        if(!$result->num_rows())
        {
            $this->db->select('cartridge.name as cart_name, brands.name as brand_name')
                    ->from('cartridge')
                    ->join('brands','cartridge.brand=brands.id')
                    ->where('cartridge.id',$data['name_id']);
            $cartridge=$this->db->get()->row();
            $data['name']=$cartridge->brand_name.' '.$cartridge->cart_name;
            $data['first_date']=  date('U');
            $this->db->insert('registr_cartridge', $data);
            echo br()." Регистрирую новый картридж";
            return TRUE;
        }
        return FALSE;
    }
    
    //создание этапов только заказа
    public function create_stage_order($order_hash,$table,$stage_code,$date_start,$manager_id,$info,$cart_num=FALSE)
    {
        $this->db->select('id')->where('hash',$order_hash);
        $query=$this->db->get('orders');
        $order_id=$query->row()->id;
        
        $this->db->select('sort')->where('code',$stage_code);
        $sort_stage=$this->db->get('stages_'.$table);
        $sort=$sort_stage->row()->sort;
        $data=array('order_id'=>$order_id,
          'stage_code'=>$stage_code,
            'manager_id'=>$manager_id,
            'date_start'=>$date_start,
            'info'=>$info,
            'sort'=>$sort);
        $cart_num?$data['cart_num']=$cart_num:false;
        
        $this->db->insert($table.'_stages',$data);
        return true;
        
        
    }
    
    //создание этапов картриджа и гдето заказа
    public function create_stage($order_id,$table,$stage_code,$date_start,$manager_id,$info,$cart_num=FALSE)
    {
        $this->db->select('order_id')
                ->where('stage_code','todsp')
                ->where('date_end','0');
        $cart_num?$this->db->where('cart_num',$cart_num):false;
        $is_exist=$this->db->get($table.'_stages');
        if($is_exist->num_rows()>0)
        {
            echo br().'Ошибка: Картридж '.$cart_num.' еуже внесен. Проверьте выдачу';
            return false;
        }
        else
        {
            /*select adres from cartridge_stages where stage_code =  'topck'
            and date_end =0 order by  `cartridge_stages`.`adres` asc         */
        $this->db->select('adres')
                ->from('cartridge_stages')
                ->where('stage_code','topck')
                ->where('date_end','0')
                ->order_by('adres','asc');
        $result=$this->db->get();
        $occupied=array();
        foreach ($result->result() as $adres)
        {
            if($adres->adres!='') {$occupied[]=$adres->adres;}
        }
        
        $free_cell = array(1,2,3,4,5,6,7,8,
                        9,11,12,13,14,15,16,
                        17,18,19,20,21,22,23,24,
                        25,26,27,28,29,30,31,32,
                        33,34,35,36,37,38,39,40,
                        41,42,43,44,45,46,47,48,
                        49,50,51,52,53,54,55,56,
                        57,58,59,60,61,62,63,64,
                        65,66,67,68,69,70,71,72);
        $adreses = array_diff($free_cell,$occupied);
        
        $this->db->select('MAX(sort) as sort')
                ->from($table.'_stages')
                ->where('date_end',0)
                ->where('stage_code','todsp');
        $sort=$this->db->get()->row()->sort+1;
            
            $data=array('order_id'=>$order_id,
                        'stage_code'=>$stage_code,
                        'manager_id'=>$manager_id,
                        'date_start'=>$date_start,
                        'info'=>$info,
                        'sort'=>$sort,
                        'adres'=>current($adreses));
            $cart_num?$data['cart_num']=$cart_num:false;
            $this->db->insert($table.'_stages',$data);
            return $data['adres'];
        }
        
    }
    
    //получение типов техники
    public function tech_types()
    {
        $this->db->select('id,name')
                ->order_by('name','asc');
        $result=$this->db->get('tech_types');
        return $result;
    }
    
    
    
    
    //создание этапов техники
        public function create_stage_tech($order_id,$table,$stage_code,$date_start,
                $manager_id,$info='done',$serial_num=FALSE,$attachment=FALSE,$adres=FALSE,$message=FALSE)
    {
        $this->db->select('order_id')
                ->where('stage_code','todsp')
                ->where('order_id',$order_id)
                ->where('serial_num',$serial_num);
        
        $is_in_order=$this->db->get($table.'_stages');
        if($is_in_order->num_rows()>0)
        {
            $result='<br/>В заказ '.$is_exist->row()->order_id.' это устройсто уже внесено';
            return $result;
        }
        else
        {
        
        $this->db->select('order_id')
                ->where('stage_code','todsp')
                ->where('date_end','0');               
        $serial_num?$this->db->where('serial_num',$serial_num):false;
        $is_exist=$this->db->get($table.'_stages');
        if($is_exist->num_rows()>0)
        {
            $result='Устройство находится в заказе '.$is_exist->row()->order_id.' и не выдано!';
            return $result;
        }
        else
        {
       $this->db->select('max(sort) as sort')
               ->from('tech_stages')
               ->where('stage_code','todsp')
               ->where('date_end','0');
       $sort=$this->db->get()->row()->sort+1;
       
            $data=array('order_id'=>$order_id,
          'stage_code'=>$stage_code,
            'manager_id'=>$manager_id,
            'date_start'=>$date_start,
            'info'=>$info,
            'sort'=>$sort);
        $serial_num?$data['serial_num']=$serial_num:false;
        $attachment?$data['attachment']=$attachment:false;
        $adres?$data['adres']=$adres:false;
        $message?$data['message']=$message:false;
        
        $this->db->insert($table.'_stages',$data);
        return 0;
        }
        }
        
    }
    
    public function get_stages($table)
    {
        $query=$this->db->order_by("sort","asc")->get($table);
        return $query;
    }
    
    public function get_orders_stage($stage_code,$user_id)
    {      
       /*SELECT order_stages.order_id, order_stages.date_start, order_stages.info, orders.contacter, orders.phone, orders.phonemob, orders.adres, orders.org_name FROM `order_stages` 
join `orders` on orders.id=order_stages.order_id
WHERE `stage_code`='tofcdl' and date_end=0
*/
        
        
        $this->db->select('orders.manager_id as order_owner, orders.hash,orders.date_create,orders.contacter,order_stages.order_id, 
            order_stages.date_start,order_stages.date_end, orders.other_info, orders.contacter, orders.phone, 
            orders.phonemob, orders.adres, orgs.short_name, orders.org_id,order_stages.action_flag,order_stages.stage_code,
            order_stages.info,orders.paymethod, order_stages.manager_id, users.first_name')
                ->from('order_stages')
                ->join('orders','orders.id=order_stages.order_id')
                ->join('orgs','orgs.id=orders.org_id')
                ->join('users','users.id=order_stages.manager_id')
                ->where_in('stage_code',$stage_code)
                
                ->where('date_start !=',0)->where('date_end',0)
                ->order_by('order_stages.date_start','asc')
                ->order_by('order_stages.action_flag','asc');
        if($user_id>0)
        {
             $this->db->where('order_stages.manager_id',$user_id);
        }
            
        $query=$this->db->get();
        return $query;
    }
    
    //обновление инфы в этапе, закрытие/открытие этапа по КАРТРИДЖАМ
    public function update_cartridge_stage($stage_code, $order_id, $cart_num, $date_start,$date_end,$info=false,$adres=false,$master_id=false)
    {
        $date_end?$data['date_end'] = $date_end:FALSE;
        $date_start?$data['date_start'] = $date_start:FALSE;
                
        if($date_end=='setnull') $data['date_end'] = '0';
        if($date_start=='setnull') $data['date_start'] = '0';
        
        $info?$data['info']=$info:FALSE;
        $adres?$data['adres']=$adres:FALSE;
        $master_id?$data['manager_id']=$master_id:FALSE;
        
        
        $this->db->where('order_id', $order_id)
                ->where('stage_code', $stage_code);
        $cart_num?$this->db->where('cart_num',$cart_num):FALSE;
        
        $result=$this->db->update('cartridge_stages',$data);
       
        return $result;
        
    }
    
    //обновление инфы в этапе, закрытие/открытие этапа по ТЕХНИКЕ
    public function update_tech_stage($stage_code, $order_id, $serial_num, 
            $date_start,$date_end,$info=false,$adres=false,$master_id=false,$message=false)
    {
        $date_start?$data['date_start'] = $date_start:FALSE;
        $date_end?$data['date_end'] = $date_end:FALSE;
        if($date_end=='setnull') $data['date_end'] = '0';
        if($date_start=='setnull') $data['date_start'] = '0';
        $info?$data['info']=$info:FALSE;
        $adres?$data['adres']=$adres:FALSE;
        $message?$data['message']=$message:FALSE;
        $master_id?$data['manager_id']=$master_id:FALSE;
                
        $this->db->where('order_id', $order_id)
                ->where('stage_code', $stage_code);
        $serial_num?$this->db->where('serial_num',$serial_num):FALSE;
        
        $this->db->update('tech_stages',$data);
        
        if($stage_code=='todsp')
        {
            return 'todsp';
        }
        else
        {
            $this->db->select('sort')
                ->from('stages_tech')
                ->where('code',$stage_code);
            $res=$this->db->get();
            $sort=$res->row()->sort;
            
            $this->db->select('code')
                ->from('stages_tech')
                ->where('sort',$sort+1);
            $res=$this->db->get();
            $stage_code=$res->row()->code;
            
            return $stage_code;
        }
        
        return false;
    }
    
    // сортировка картриджей
    public function update_cartridge_sort($cart_num, $sort)
    {
        
         $sort_data=array('sort'=> $sort,'enable'=>1);
        
         $this->db->where('cart_num', $cart_num);
        $this->db->update('cartridge_stages',$sort_data);
        return false;
    }
    
    // сортировка техники
    public function update_tech_sort($serial_num, $sort)
    {
        
        $sort_data=array('sort'=> $sort,'enable'=>1);
        $this->db->where('serial_num', $serial_num);
        $this->db->update('tech_stages',$sort_data);
        return true;
    }
    
// получение полного имени картриджа по его айди номеру из каталога    
    public function get_full_cart_name($cart_id)
    {
        //echo $cart_id;
        $this->db->select('cartridge.name as cart_name, brands.name as brand_name')
                    ->from('cartridge')
                    ->join('brands','cartridge.brand=brands.id')
                    ->where('cartridge.id',$cart_id);
                    $cartridge=$this->db->get()->row();
                    $full_name=$cartridge->brand_name.' '.$cartridge->cart_name;
                    return $full_name;
    }
    public function registr_cartridge_update_by_num($cart_num,$data)
    {
        $this->db->where('uniq_num',$cart_num);
        $this->db->update('registr_cartridge',$data);
        return true;
    }
     
    
    public function update_stage_orders($stage_code, $order_id, $action_flag, $date_start,$date_end,$info=false, $executant_id=false)
    {
        //если время уже установленно время не обновляем
        $this->db->select('date_start')
                ->where('stage_code',$stage_code)
                ->where('order_id',$order_id);
        $query=$this->db->get('order_stages');
        $date_start?$data['date_start'] = $date_start:FALSE;
        $this->db->select('date_end')
                ->where('stage_code',$stage_code)
                ->where('order_id',$order_id);
        $query=$this->db->get('order_stages');
        if($query->row()->date_end!=0) $data['date_end']=$query->row()->date_end;
        else $date_end?$data['date_end'] = $date_end:FALSE;
        
        $data['action_flag'] = $action_flag;
        $info?$data['info']=$info:FALSE;
        
        //!ОБЯЗАТЕЛЬНО записываем id пользователя
        if($executant_id) $data['manager_id']=$executant_id;
        else $data['manager_id']=$this->ion_auth->user()->row()->id;
        
        //обновляем запись этапа (stage_code) заказа (order_id)
        $this->db->where('order_id', $order_id)
                ->where('stage_code', $stage_code);
        $this->db->update('order_stages',$data);
       
        $this->db->select('sort')
                ->where('stage_code',$stage_code)
                ->where('order_id',$order_id);
        $query=$this->db->get('order_stages');
        
        $res=$query->row();
        
        /*проверяем все ли этапы закрыты если все то закрываем заказ closed=1
         * И МИНУСУЕМ сумму в балансе клиента
         * 
         */
        $this->db->select('id')
                ->from('order_stages')
                ->where('date_end',0)
                ->where('order_id',$order_id);
        $isClosed=$this->db->get();
        if($isClosed->num_rows()==0)
        {
            $this->systema_model->clarify_order(array('closed'=>1),$order_id);
             
            //минусуем тут
            $this->db->select('edrpou,sum')
                    ->from('accounting')
                    ->where('debet_kredit','0')
                    ->where('order_id',$order_id);
            $suma=$this->db->get();
            echo 'update orgs set balance=balance-'.$suma->row()->sum.' where edrpou="'.$suma->row()->edrpou.'"';
            $this->db->query('update orgs set balance=balance-'.$suma->row()->sum.' where edrpou="'.$suma->row()->edrpou.'"');
            //$thid->db->get();
               
        }
                
        
        $this->db->select('stage_code')
                ->where('sort',$res->sort+1)
                ->where('order_id',$order_id);
        
                $query=$this->db->get('order_stages');
        
        return $query;
        
    }


    public function get_stage($order_id,$stage_code)
    {
        $this->db->select('date_start,date_end,info,sort,action_flag, manager_id, users.first_name')
                ->from('order_stages')
                ->join('users','users.id=order_stages.manager_id')
                ->where('order_id',$order_id)
                ->where('stage_code',$stage_code);
        
        $query=$this->db->get();
        return $query;
    }
    
    public function get_cartridge($uniq_num=false,$org_id=false)
    {
        $this->db->select('name, name_id,uniq_num, orgs.id, orgs.short_name')
                ->from('registr_cartridge')
                ->join('orgs','orgs.id=registr_cartridge.org_id');
        $uniq_num?$this->db->where('uniq_num',$uniq_num):false;
        $org_id?$this->db->where('registr_cartridge.org_id',$org_id):false;
        $query=$this->db->get();
        return $query;
    }
    
    public function get_tech($serial_num=false,$org_id=false)
    {
        $this->db->select('name, name_id,serial_num, orgs.id, orgs.short_name')
                ->from('registr_techs')
                ->join('orgs','orgs.id=registr_techs.org_id');
        $serial_num?$this->db->where('serial_num',$serial_num):false;
        $org_id?$this->db->where('registr_techs.org_id',$org_id):false;
        $query=$this->db->get();
        return $query;
    }
    
    public function view_order($hash,$by_id=false)
    {      
     
        $this->db->select('orders.id, orders.date_create, orders.manager_id, users.first_name, orders.contacter, 
            orders.phone, orders.phonemob, orders.email as email2, orders.adres, orgs.short_name, orders.other_info,
            orders.org_id, orgs.tel,orders.paymethod, orgs.paymethod as orgpaymethod, orders.discount, 
            orgs.contract, orgs.discount as org_discount, orgs.edrpou')
                ->from('orders')
                ->join('orgs','orgs.id=orders.org_id')
                ->join('users', 'users.id=orders.manager_id');
                $by_id ? $this->db->where('orders.id',$hash): $this->db->where('orders.hash',$hash);
                
        
        $query=$this->db->get();
        return $query;
    }
    
    //вывод картриджей в панель мастера
    public function cartridge_stages($order_id,$stage_code=false,$cart_num=false,$date_end=false)
    {
        $this->db->select('cartridge_stages.id, cart_num, date_start, date_end, info, stage_code, 
            cartridge_stages.adres, registr_cartridge.name as cart_name, stages_cartridge.name as stage_name, 
            cartridge_stages.order_id,cartridge_stages.sort as sort,
            registr_cartridge.name_id as cart_id,orders.hash,orders.contacter,orgs.short_name as org_name, orgs.id as org_id')
                ->from('cartridge_stages')
                ->join('orders','cartridge_stages.order_id=orders.id')
                ->join('registr_cartridge','cartridge_stages.cart_num=registr_cartridge.uniq_num')
                ->join('stages_cartridge','cartridge_stages.stage_code=stages_cartridge.code')
                ->join('orgs','orders.org_id=orgs.id')
                ->where('date_start !=','0');
                 $this->db->order_by('sort','asc');
                 $this->db->order_by('stage_code','asc');
                
        $date_end ? $this->db->where('date_end !=','0') : $this->db->where('date_end','0');
        if($cart_num) $this->db->where('cart_num',$cart_num);
        if($order_id) $this->db->where('order_id',$order_id);
        if($stage_code)$this->db->where_in('stage_code',$stage_code);

        $result=$this->db->get();
        return $result;
    }
    
    
    
    //получение инфы о готовых картриджах
    public function cartridge_stages_done($order_id=false,$stage_code=false,$cart_num=false,$date_start=false,$date_end=false)
    {
        /*SELECT cart_num, info, registr_cartridge.name, cartridge.cena_zapravki,
          cartridge.cena_vostanovlenia, cartridge_stages.stage_code, cartridge.id
 FROM `cartridge_stages` 
join registr_cartridge on registr_cartridge.uniq_num=cartridge_stages.cart_num
join cartridge on registr_cartridge.name_id=cartridge.id
WHERE cartridge_stages.order_id=108 and stage_code in('inrfl','inrck') and info not in('notneed','stop')
order by cart_num asc, stage_code desc*/
        $this->db->select('cart_num, info, registr_cartridge.name, cartridge.cena_zapravki,
            cartridge.cena_vostanovlenia, cartridge_stages.stage_code, cartridge_stages.date_end, 
            users.first_name, users.last_name,cartridge_stages.order_id, cartridge.id')
        ->from('cartridge_stages') 
                ->join('registr_cartridge','registr_cartridge.uniq_num=cartridge_stages.cart_num')
                ->join('cartridge','registr_cartridge.name_id=cartridge.id')
                ->join('users','users.id=cartridge_stages.manager_id')
                ->where_not_in('info',array('notneed','stop'))
                ->where('cartridge_stages.date_end !=',0);
        $cart_num?$this->db->order_by('date_end','asc'):$this->db->order_by('registr_cartridge.uniq_num','asc');
                $this->db->order_by('stage_code','asc');
        
        $order_id?$this->db->where('cartridge_stages.order_id',$order_id):false;
        $date_start?$this->db->where('cartridge_stages.date_start >=',$date_start):$this->db->where('cartridge_stages.date_start !=',0);
        $date_end?$this->db->where('cartridge_stages.date_end <=',$date_end):$this->db->where('cartridge_stages.date_end !=',0);
        $stage_code?$this->db->where_in('stage_code',$stage_code):false;
        $cart_num?$this->db->where('cartridge_stages.cart_num',$cart_num):false;
        
        $result=$this->db->get();
        return $result;
    }
    
    //получение инфы о готовой технике
    public function tech_stages_done($order_id=false,$stage_code=array('torpr'),$serial_num=false,$date_start=false,$date_end=false)
    {
        /*SELECT registr_techs.serial_num, info, registr_techs.name, tech_stages.stage_code,tech_stages.message
 FROM `tech_stages` 
join registr_techs on registr_techs.serial_num=tech_stages.serial_num
WHERE tech_stages.order_id=1726 and stage_code='torpr' and info not in('notneed','stop','needrpr')
and (date_start!=0 and date_end!=0)
order by serial_num asc*/
        $this->db->select('registr_techs.serial_num, info, registr_techs.name, tech_stages.stage_code,tech_stages.message')
                ->from('tech_stages')
                ->join('registr_techs','registr_techs.serial_num=tech_stages.serial_num')
                ->where_not_in('info',array('notneed','stop','needrpr'))
                ->order_by('serial_num','asc');        
        $order_id?$this->db->where('tech_stages.order_id',$order_id):false;
        $date_start?$this->db->where('tech_stages.date_start >=',$date_start):$this->db->where('tech_stages.date_start !=',0);
        $date_end?$this->db->where('tech_stages.date_end <=',$date_end):$this->db->where('tech_stages.date_end !=',0);
        $stage_code?$this->db->where_in('stage_code',$stage_code):false;
        $serial_num?$this->db->where('tech_stages.serial_num',$serial_num):false;
        
        $result=$this->db->get();
        return $result;
    }
    
   
     public function clarify_order($data,$order_id)
    {
        //print_r($data);
        $this->db->where('id', $order_id);
        $this->db->update('orders', $data); 
    }
    public function check_cart_adres($adres)
    {
        $this->db->select('adres')
                ->from('cartridge_stages')
                ->where('adres',$adres)
                ->where('stage_code','topck')
                ->where('date_end',0);
        $result=$this->db->get();
        return $result->num_rows;
    }
    
    //проверка статуса заказа
    public function check_in_order($order_id)
    {
        //проверяем все картриджи на каких они этапах
        //если хоть один этап не закрыт то заказ принимает статус этого этапа
        $this->db->select('stage_code,stages_cartridge.name as stage_name')
                ->from('cartridge_stages')
                ->join('stages_cartridge','cartridge_stages.stage_code=stages_cartridge.code')
                ->where('date_start !=',0)
                ->where('date_end',0)
                ->where('order_id',$order_id)
                ->order_by('stage_name','asc');
        
        $result=$this->db->get();
        
        return $result;
    }
    
    public function check_in_order_tech($order_id)
    {
        //проверяем всю технику на каких они этапах
        //если хоть один этап не закрыт то заказ принимает статус этого этапа
        $this->db->select('stage_code,stages_tech.name as stage_name')
                ->from('tech_stages')
                ->join('stages_tech','tech_stages.stage_code=stages_tech.code')
                ->where('date_start !=',0)
                ->where('date_end',0)
                ->where('order_id',$order_id)
                ->order_by('stage_name','asc');
        
        $result=$this->db->get();
        
        return $result;
    }
    
            
    //создание индивидуального прайса картриджей для организации
    public function create_price_cart($partner_id,$cartridge_id)
    {
        $partner_id?$this->db->where('org_id', $partner_id):FALSE;
        $cartridge_id?$this->db->where('cart_id', $cartridge_id):FALSE;
        $this->db->where('locked',0);
       echo $this->db->update('orgs_price_cart',array('archive'=>1)); 

        $this->db->distinct();
        $this->db->select('registr_cartridge.name, registr_cartridge.name_id,cartridge.cena_zapravki as inrfl,
            cartridge.cena_vostanovlenia as inrck, orgs.discount,orgs.paymethod')
                ->from('registr_cartridge')
                ->join('cartridge','cartridge.id=registr_cartridge.name_id')
                ->join('orgs','orgs.id=registr_cartridge.org_id')
                ->where('registr_cartridge.org_id',$partner_id);
        $cartridge_id?$this->db->where('cartridge.id',$cartridge_id):false;
        $catridges=$this->db->get();
        
            foreach ($catridges->result() as $cartridge)
            {
                $price_inrfl=round($cartridge->inrfl*(100-$cartridge->discount)/100);
                $price_inrck=round($cartridge->inrck*(100-$cartridge->discount)/100);
                
                    
                $this->db->select('orgs_price_cart.id')
                        ->from('orgs_price_cart')
                        ->where('cart_id',$cartridge->name_id)
                        ->where('org_id',$partner_id)
                        ->where('archive',0)
                        ->where('stage_code','inrfl');
                
                $result=$this->db->get();
                if($result->num_rows==0)
                    {                    
                    $prices[]=array('stage_code'=>'inrfl',
                    'org_id'=>$partner_id,
                    'cart_id'=>$cartridge->name_id,
                    'price'=>$price_inrfl,
                    'archive'=>0,
                    'set_date'=>  date('U'));
                    }
               
                $this->db->select('orgs_price_cart.id')
                        ->from('orgs_price_cart')
                        ->where('cart_id',$cartridge->name_id)
                        ->where('org_id',$partner_id)
                        ->where('archive',0)
                        ->where('stage_code','inrck');
                $result=$this->db->get();
                
                if($result->num_rows==0)
                {
                    $prices[]=array('stage_code'=>'inrck',
                    'org_id'=>$partner_id,
                    'cart_id'=>$cartridge->name_id,
                    'price'=>$price_inrck,
                    'archive'=>0,
                    'set_date'=>  date('U'));
                }
            }
           
            $this->db->insert_batch('orgs_price_cart', $prices); 
        return true;
    }
    
    //индивидуальный прайс по картриджам клиента 
    public function get_price_cart($partner_id)
    {
        $this->db->select('orgs_price_cart.id,orgs_price_cart.cart_id,stage_code,price,
            cartridge.name,brands.name as brand,orgs_price_cart.locked')
                ->from('orgs_price_cart')
                ->join('cartridge','orgs_price_cart.cart_id=cartridge.id')
                ->join('brands','brands.id=cartridge.brand')
                ->where('orgs_price_cart.org_id',$partner_id)
                ->where('archive',0)
                ->order_by('cart_id','asc')
                ->order_by('stage_code','desc');
        return $this->db->get();
    }
    
    //вывод индивидуального прайса по картриджам
    public function get_individual_cart_price($cart_id,$org_id,$stage_code)
    {
        $this->db->select('price')
                ->from('orgs_price_cart')
                ->where('cart_id',$cart_id)
                ->where('org_id',$org_id)
                ->where('stage_code',$stage_code)
                ->where('archive',0);
        return $this->db->get();
        
    }
    
    // редактирование строки индивидуального картриджа
    public function change_price_item($item_id,$field,$value)
    {
        $this->db->where('id',$item_id);
        $this->db->update('orgs_price_cart',array($field=>$value));
    }

 // вывод техники в заказе
 /*    public function tech_stages($order_id,$stage_code=false,$serial_num=false,$date_end=false)
    {
        //select tech_stages.id, tech_stages.serial_num, date_start, date_end, info, stage_code, 
        //    tech_stages.adres, registr_techs.name as tech_name, stages_tech.name as stage_name, 
        //    tech_stages.order_id,stages_tech.sort as stage_sort,message,
        //    registr_techs.name_id as tech_id,orders.hash,orders.contacter,orgs.short_name as org_name,
       //  
//from  tech_stages
 //              join orders on tech_stages.order_id=orders.id
  //             join registr_techs on tech_stages.serial_num=registr_techs.serial_num
   //            join stages_tech on tech_stages.stage_code=stages_tech.code
    //            join orgs on orders.org_id=orgs.id
     //           where date_start !='0'
         
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
    }*/
   
    
    // подсчет сделанных картриджей за период выбраной организации
    public function done_cartridge_stage_org($date_start,$date_end,$org_id=false,$stage=false)
    {
    /*SELECT COUNT( cartridge_stages.id ) , cartridge_stages.order_id, orders.org_id
FROM  `cartridge_stages` 
JOIN orders ON orders.id = cartridge_stages.order_id
WHERE  `stage_code` =  'inrfl'
AND orders.org_id =242
AND (
date_start >=1356998581
AND date_end <=1359504181
)*/
        
        $this->db->select('COUNT( cartridge_stages.id ) as count, cartridge_stages.order_id, orders.org_id')
                ->join('orders','orders.id = cartridge_stages.order_id')
                ->where('stage_code',$stage)
                ->where('orders.org_id',$org_id)
                ->where('date_start >=',$date_start)
                ->where('date_end <=',$date_end);
        
        $result=$this->db->get('cartridge_stages');
        return $result;        
    
    }    
          
/*Секция работы с инвойсами*/
    public function select_invoice_item($uniq_num,$order_id)
    {
        $this->db->where('uniq_num',$uniq_num)
                ->where('order_id',$order_id);
        $result=$this->db->get('invoices');
        return $result;
    }
    
    
    public function insert_invoice_item($uniq_num,$order_id,$text,$price)
    {
        $data=array('uniq_num'=>$uniq_num, 'text'=>$text, 'order_id'=>$order_id,
            'price'=>$price);
        $this->db->insert('invoices', $data);
    }
    public function update_invoice_item($item_id,$invoice_data)
    {
        $this->db->where('id',$item_id);
        $this->db->update('invoices',$invoice_data);
    }
    
    public function select_accounting($order_id,$deb_kred=false)
    {
        $this->db->where('order_id',$order_id);
        $deb_kred?$this->db->where('debet_kredit',$deb_kred):false;
        $result=$this->db->get('accounting');
        return $result;
    }
    
    public function insert_accounting($accounting_data)
    {
        if(!$accounting_data['sum']) $accounting_data['sum']='0.0';
        $this->db->insert('accounting',$accounting_data);
    }
    
    public function update_accounting($order_id,$accounting_data)
    {
        $this->db->where('order_id',$order_id);
        $this->db->where('debet_kredit','0');
        $this->db->update('accounting',$accounting_data);
    }
    public function orders_count($start_date, $end_date, $org_id=false)
    {
        //select count(id) as count from `orders` where date_create>=1351728000 and date_create<=1354319999 and closed=1 and org_id=11
       $this->db->select('count(id) as count')
               ->from('orders')
               ->where('date_create >=', $start_date)
               ->where('date_create <=', $end_date)
               ->where('closed', 1);
               $org_id ? $this->db->where('org_id',$org_id) : $this->db->where('org_id >',0);
       return $this->db->get()->row()->count;
    }
    
    public function settings($param="")
    {
        $this->db->select('value')
                ->from('settings')
                ->where('name',$param);
        return $this->db->get();
    }
    
    public function set_settings($data,$param)
    {
        $this->db->where('name', $param);
        $this->db->update('settings', $data); 
    }
            
     
     
}

?>