<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Store extends CI_Controller
{
    function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		
                $this->load->helper('url');
                $this->load->helper('html');
                $this->load->helper('language');
                $this->load->helper('date');
                
		// Load MongoDB library instead of native db driver if required
		//$this->config->item('use_mongodb', 'ion_auth') ?
	//		$this->load->library('mongo_db') :
		
                $this->load->model('systema_model');
                $this->load->model('store_model','store');
                $this->load->model('cartridge_model','cartridge');
                
                
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                
                //формируем выпадающий список для групп
                $group_list='';
                $groups=$this->store->groups();
                foreach ($groups->result() as $group) {
                    $group_list.='<li><a href="/store/index/'.$group->id.'">'.$group->name.'</a></li>';
                }
                    $this->menu_groups='
                <div class="btn-group">
                    <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                        Группы<span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">'
                        .$group_list.
                        '<!--<li class="divider"></li>-->
                        <li><a href="/store/groups/">Редактор</a></li>
                    </ul>
                </div>
               ';

                
	}
        
        //функция формирует верхнее меню пользователя. впихиваю в каждый файл. не валидно. ПЕЕРЕДЕЛАТЬ!
            private function get_user_menu($usermenu="",$userhere="")
    {
         $user=$this->ion_auth->user()->row();
         if($usermenu)
            {$tomain='';
              $user_groups = $this->ion_auth->get_users_groups()->result();
                    foreach($user_groups as $group)
                    {
                        $tomain.=anchor($group->name,lang($group->description))." | ";
                        if($group->name==$this->uri->segment(1))
                        {
                            $userhere=" - ".lang($group->description);
                        }
   
                    }
                $this->data['usermenu']=$usermenu;
                $this->data['title']=$user->first_name." ".$user->last_name." - ".$userhere;
                $this->data['tomain']= $tomain;
               //$this->data['tomain']= anchor('main', lang('menu_tomain'));
                
            }
            else
            {
                $user_groups = $this->ion_auth->get_users_groups()->result();
                    foreach($user_groups as $group)
                    {
                        $usermenu.=anchor($group->name,lang($group->description))." | ";
                        if($group->name==$this->uri->segment(1))
                        {
                            $userhere=" - ".lang($group->description);
                        }
   
                    }
                    $this->data['tomain']= ""; 
                    $this->data['usermenu']=$usermenu;
                    $this->data['title']=$user->first_name." ".$user->last_name.$userhere;
                  
                    
         
            }
    }
    
    public function index($group_id=false,$search=false)

    {
        echo $search;
        if (!$this->ion_auth->logged_in())
                   {
                           //redirect them to the login page
                           redirect('main/login', 'refresh');
                   }
                   else
                   {
                     
                      $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             anchor('store/writeoff', 'Контроль списания','class="btn btn-small"').
                             $this->menu_groups,'Склад');
                

                   if($this->input->get('search')) $search=$this->input->get('search');
                
                   $store=$this->store->view($group_id,$search);
                
                    $store_table='<table class="table table-striped table-bordered table-condensed">';
$store_table.='<thead>';
$store_table.='<tr>';
        $store_table.='<th width="75px">Артикул</th>';
        $store_table.='<th width="350px">Наименование</th>';
        $store_table.='<th>Наличие</th>'; //наличие, количество товара
        
        if($this->ion_auth->is_admin()) $store_table.='<th>Закупка</th>'; //закупочную цену отбражаем только избраным
        $store_table.='<th>Цена</th>'; //цена товара
        
        $store_table.='<th width="50px">T</th>';
        $store_table.='<th width="200px">Совместимости</th>';
        
        $store_table.='</tr>';
$store_table.='</thead>';
foreach ($store->result() as $item) 
    {
    $item->article? $article=anchor('store/edit_item/'.$item->id,$item->article): 
            $article=anchor('store/edit_item/'.$item->id,'ред.');
        $store_table.='<tr>';
            $store_table.='<td>'.$article.'</td>';
            $store_table.='<td>'.$item->name.'</td>';
            $store_table.='<td>'.$item->available.nbs().$item->units.'</td>'; //наличие, количество товара  
            
            if($this->ion_auth->is_admin()) $store_table.='<td>'.$item->cost.'</td>'; //закупочную цену отбражаем только избраным
            $store_table.='<td>'.$item->price.'</td>'; //цена товара
        
            $store_table.='<td>'.$item->prefix.'</td>';
            $store_table.='<td></td>';
            
        
        $store_table.='</tr>';
    }
    $store_table.='</table>';

                    
                $this->data['store_table']=str_replace($search,'<b>'.$search.'</b>',$store_table);    
                $this->data['searchform']='<form action="/store/index" method="get">
<div class="control-group">
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on"><i class="icon-search"></i></span>
      <input class="span2" id="inputIcon" type="text" name="search">
    </div>
  </div>
</div>
</form>';    
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('store/main', $this->data);
                $this->load->view('bottom', $this->data);
                        
                }
                
        
    }
    
    
    
    public function groups()
    {
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
        else
        {
            
              $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             anchor('store/writeoff', 'Контроль списания','class="btn btn-small"').
                             $this->menu_groups,'Склад');
            
             $this->data['groups']=$this->store->groups();
             $this->load->view('header', $this->data);
             $this->load->view('user_menu', $this->data);
             $this->load->view('store/groups', $this->data);
             $this->load->view('bottom', $this->data);
        }
    }
    
    

 //переименовани группы
    public function rename_group()
    {
        foreach ($this->input->post() as $id => $value) {
            
            $data=array('name'=>$value);
            $this->store->update_group($id,$data);
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
    }
   
    
    // создание группы в складе
    public function add_group()
    {
        if($this->input->post('name')){
        $data=array('name'=>$this->input->post('name'));
        $this->store->add_group($data);
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
    }
    
     public function remove_group()
    {
       if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
        else
        {
            
              $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             anchor('store/writeoff', 'Контроль списания','class="btn btn-small"').
                             $this->menu_groups,'Склад');
            
            
             $this->load->view('header', $this->data);
             $this->load->view('user_menu', $this->data);
             $this->load->view('alert', $this->data);
             $this->load->view('bottom', $this->data);
        }
    }
    
    
    public function edit_item($item_id,$edit_cart_table=0)
    {
        $string=''; $printer->name='printer';
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
        else
        {
             $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             $this->menu_groups,'Склад');
             
             $this->data['item']=$this->store->get_item($item_id)->row();
             $this->data['cart_rashodka']=$this->store->cart_rashodka($item_id);
             $this->data['rel_printers']=$this->store->rel_printers($item_id);
             foreach ($this->data['rel_printers']->result() as $printer)
             {
                 if($printer->name!=$printer_name)
                 {
                     $string.=$printer->name.'/';
                     $printer_name=$printer->name;  
                 }
                 
             }
             $data=array('text'=>$string);
             $this->store->update_item($item_id,$data);  
             
            
             $this->data['groups_in']=$this->store->item_groups($item_id);
             
             foreach ($this->data['groups_in']->result() as $value) 
                 {
                    $not_in[]=$value->id;
                 }
             
             $this->data['groups']=$this->store->groups($not_in);
             
             $this->data['edit_cart_table']=$edit_cart_table;
               $this->data['searchform']='<form action="/store/index" method="get">
<div class="control-group">
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on"><i class="icon-search"></i></span>
      <input class="span2" id="inputIcon" type="text" name="search">
    </div>
  </div>
</div>
</form>';    
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('store/item', $this->data);
                $this->load->view('bottom', $this->data);
            
            
        }
       
    }
    
    public function update_item($item_id)
    {
        print_r($this->input->post());
        if($this->store->update_item($item_id,$this->input->post())) redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
        else 'some error';
    }
    // создание новой позиции в складе
    public function add_item()
    {
        $data=array('name'=>'new_item');
        $id=$this->store->add_item($data);
        redirect('store/edit_item/'.$id, 'refresh'); 
        
    }
    
    //добавление товарной позиции в группу
    public function add_item_to_group($item_id,$group_id)
    {
        $this->db->where('item_id',$item_id)
                ->where('group_id',$group_id);
        $result=$this->db->get('store_item_groups');
        if($result->num_rows()==0)
        {
        $data=array('item_id'=>$item_id,'group_id'=>$group_id);
        $this->store->add_item_to_group($data);
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
        
    }
    
    //удаление товарной позиции из группы
    public function remove_item_from_group($item_id,$group_id)
    {
        $this->db->where('item_id',$item_id)
                ->where('group_id',$group_id);
        $this->db->delete('store_item_groups');
        
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
        
    }
    
    //приход товара в склад. записываем сробытие в журнал и добавляем количество в склад
    public function incoming($item_id)
    {
        echo $item_id.br();
        print_r($this->input->post());
        if($item_id!=''&& $this->input->post('amount')!='')
        {
   
            //id 	date 	item_id 	amount 	order_id 	uniq_num 	user_id
            $journal_data=array("date"=>date('U'),
                                "item_id"=>$item_id,
                                "amount"=>$this->input->post('amount'),
                                "user_id"=>$this->ion_auth->user()->row()->id);
                            print_r($journal_data);
            $this->store->insert_journal($journal_data);
            
            $item=$this->store->get_item($item_id)->row();
                 
            echo $item_data['available']=$item->available+$this->input->post('amount');
            $this->store->update_item($item_id,$item_data);
            redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
        }
        else
        {
            echo 'some error';
            redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
        }
            
    }
   
    public function get_items_for_cart()
    {
        $result="";
        $search=explode('#', $this->input->post('str'));
        
        $store=$this->store->view(false,$search[0]);
     //echo '|'.$store->num_rows().'|<br>';
        if($store->num_rows()>0)
        {
            $result="";
            foreach ($store->result() as $item)
            {
                $result.='<a href="/cartridges/add_material/'.$item->id.'/'.$search[1].'/inrfl" class="btn btn-mini btn-success">ЗП</a>'.nbs();
                $result.='<a href="/cartridges/add_material/'.$item->id.'/'.$search[1].'/inrck" class="btn btn-mini btn-warning">ВС</a>';
                $result.=nbs().$item->name.br();
            }
        }
        else {echo $this->input->post('str');}
        echo $result;
    }
    
    public function get_service_for_order()
    {
        if (!$this->ion_auth->logged_in())
        {   
        }
	else
	{
            $store=$this->store->view(5,$this->input->get('term'));
            foreach ($store->result() as $item)
            {
                $elements[]='"'.$item->name.' $'.$item->price.'"';
            }
            $s = '['.implode(",", $elements).']';
            echo $s;
        }      
    }
    
    public function get_sale_for_order()
    {
        if (!$this->ion_auth->logged_in())
        {   
        }
	else
	{
            $store=$this->store->view(7,$this->input->get('term'));
            foreach ($store->result() as $item)
            {
                $elements[]='"'.$item->name.' $'.$item->price.'"';
            }
            $s = '['.implode(",", $elements).']';
            echo $s;
        }      
    }
    
    // список матриалов для заказа
    public function order()
    {
         if (!$this->ion_auth->logged_in())
                   {
                           //redirect them to the login page
                           redirect('main/login', 'refresh');
                   }
                   else
                   {
                     
                      $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             anchor('store/writeoff', 'Контроль списания','class="btn btn-small"').
                             $this->menu_groups,'Склад');
                

                     $this->data['store_order']=$this->store->view();
                
                    
                $this->data['searchform']='<form action="/store/index" method="get">
<div class="control-group">
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on"><i class="icon-search"></i></span>
      <input class="span2" id="inputIcon" type="text" name="search">
    </div>
  </div>
</div>
</form>';    
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('store/order', $this->data);
                $this->load->view('bottom', $this->data);
                        
                }
        
    }
    
    public function writeoff($user=false,$month=false,$year=false,$cart_id=false)
    {
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
        else
        {
            $this->get_user_menu(anchor('store/add_item', 'Создать','class="btn btn-small"').
                             anchor('store/order', 'Заказ материалов','class="btn btn-small"').
                             anchor('store/writeoff', 'Контроль списания','class="btn btn-small"').
                             $this->menu_groups,'Склад');
            $this->input->post('date') ? $this->data['date']= $this->input->post('date'):$this->data['date']=date('d.m.Y');
            $sel_date=  explode('.', $this->data['date']);
            $start=  mktime(0, 0, 0, $sel_date[1], $sel_date[0], $sel_date[2]);
            $end=  mktime(23, 59, 59, $sel_date[1], $sel_date[0], $sel_date[2]);
            
//mktime($hour, $minute, $second, $month, $day, $year)
            //$month=6;
            if($month&&$year)
            {
                $start = mktime(0, 0, 0, $month, 1, $year);
                $end =  mktime(23, 59, 59, $month+1, 0, $year);
            }
            elseif (!$month&&$year)
            {
                $start = mktime(0, 0, 0, 1, 1, $year);
                $end =  mktime(23, 59, 59, 12, 31, $year);
            }
            if(!$user) $whose=array('4','6','8','18','24', '34','35','36','37');
            else $whose=array($user);
            $this->data['writeoff']=$this->store->writeoff_items($start,$end,$whose,$cart_id);
            
            
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('store/writeoff', $this->data);
            $this->load->view('bottom', $this->data);
            
        }
        
    }

    
    public function add_item_to_all($item_id)
    {
        
        $this->db->select('id')
                ->from('cartridge');
        $result=$this->db->get();
        
        foreach ($result->result() as $cart) 
        {
            $data=array('id_cart'=>$cart->id,
                    'stage_code'=>'inrfl',
                    'rashodnik_id'=>$item_id,
                    'kolvo'=>1);
               // print_r($data);
               // echo '<br/>';
            $this->cartridge->add_material($data);
        }
       redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
      
}
?>