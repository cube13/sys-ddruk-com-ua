<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Techs extends CI_Controller
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
                $this->load->model('tech_model','tech');
                $this->load->model('messages_model','messages');
                $this->load->model('cartridge_model','cartridge');
                
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                
                
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
    
    public function index($stage_code=false)
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                     //redirect('techs/filter/torpr', 'redirect');
                     
                    $this->get_user_menu(' ');
                    
                
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('techs/main', $this->data);
                $this->load->view('bottom', $this->data);
                        
                }
    }
    
     //Вывод ГЛАВНОЙ ТАБЛИЦЫ устройств
    public function main_table()
    {   
        //Дигностика
        $stages=$this->tech->tech_stages(false,array('inofc','todgs'));
        $count_in_diag=$stages->num_rows();
        $tech_in_diag='<table class="table">';
        $popup_settings = array(
              'width'      => '750',
              'height'     => '570',
              'scrollbars' => 'no',
              'status'     => 'no',
              'resizable'  => 'no',
              'screenx'    => '150',
              'screeny'    => '150');
        foreach($stages->result() as $stage)
        {
             $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            
            if($stage->stage_code=='inofc') {$info='<i class="icon-edit"></i>'; $initial='initial'; $set=$popup_settings;}
            if($stage->stage_code=='todgs') {$info='<i class="icon-user-md"></i>'; $initial='diagnos'; $set=false;}
            $tech_in_diag.='<tr><td>';
            
            $tech_in_diag.=nbs(4).'№ '.anchor_popup('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).nbs(3).date('d/m H:i',$local_date).br();
            $tech_in_diag.=anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $info,$set).nbs(1);
            $tech_in_diag.=anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $stage->tech_name,$set).
                    br().timespan($timestamp);
            $tech_in_diag.='</td></tr>';
            $initial='';
        }
        $tech_in_diag.='</table>';
        
               
        //Согласование
        $stages=$this->tech->tech_stages(false,array('apprv'));
        $count_in_apprv=$stages->num_rows();
        $tech_in_apprv='<table class="table">';
        foreach($stages->result() as $stage)
        { 
            $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $global_time=date('d/m H:i',$local_date).br().timespan($timestamp);
            $tech_in_apprv.='<tr><td>';
            $tech_in_apprv.=nbs(4).'№ '.anchor_popup('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).nbs(3).date('d/m H:i',$local_date).br()
            .anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $stage->tech_name).
                    br().timespan($timestamp).'</td></tr>';
        }
        $tech_in_apprv.='</table>';
        
        //Запчасти
        $stages=$this->tech->tech_stages(false,array('toprt'));
        $count_in_parts=$stages->num_rows();
        $tech_in_toprt='<table class="table">';
        foreach($stages->result() as $stage)
        {
            $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $tech_in_toprt.='<tr><td>';
            $tech_in_toprt.=nbs(4).'№ '.anchor_popup('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).nbs(3).date('d/m H:i',$local_date).br()
                    .anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $stage->tech_name,$set).
                    br().timespan($timestamp).'</td></tr>';
        }
        $tech_in_toprt.='</table>';
        
        //Работа и тестирование
        $stages=$this->tech->tech_stages(false,array('torpr','totst'));
        $count_in_work=$stages->num_rows();
        $tech_in_work='<table class="table">';
        foreach($stages->result() as $stage)
        {
            $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $tech_in_work.='<tr><td>';
            $tech_in_work.=nbs(4).'№ '.anchor_popup('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).nbs(3).date('d/m H:i',$local_date).br()
            .anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $stage->tech_name,$set).
                    br().timespan($timestamp).'</td></tr>';
        }
        $tech_in_work.='</table>';

        //Выдача
        $stages=$this->tech->tech_stages(false,array('todsp'));
        $count_in_todsp=$stages->num_rows();
        $tech_in_todsp.='<table class="table">';
        foreach($stages->result() as $stage)
        {
            $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $tech_in_todsp.='<tr><td>';
            $tech_in_todsp.=nbs(4).'№ '.anchor_popup('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).nbs(3).date('d/m H:i',$local_date).br()
            .anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num.'/'.$initial, $stage->tech_name,$set).
                    br().timespan($timestamp).'</td></tr>';
        }
        $tech_in_todsp.='</table>';
        
        $techs="";

$techs.='<table class="table table-condensed">';
$techs.='<tr ><th width=20% class="alert-info" style="text-align:center;"><h4>'.anchor_popup('techs/table/inofc-todgs', 'Диагностика').' ('.$count_in_diag.')</h4></th>
                <th width=20% class="alert-error" style="text-align:center;"><h4>'.anchor_popup('techs/table/apprv', 'Согласование').' ('.$count_in_apprv.')</h4></th>
                <th width=20% class="alert-success" style="text-align:center;"><h4>'.anchor_popup('techs/table/toprt', 'Запчасти').' ('. $count_in_parts.')</h4></th>
                <th width=20% class="alert-danger" style="text-align:center;"><h4>'.anchor_popup('techs/table/torpr-totst', 'Работа').' ('. $count_in_work.')</h4></th>
                <th width=25% class="alert-info" style="text-align:center;"><h4>'.anchor_popup('techs/table/todsp', 'Выдать').' ('.$count_in_todsp.')</h4></th></tr>';
$techs.='<tr><td  class="alert-info">'.$tech_in_diag.'</td>
                <td  class="alert-error">'.$tech_in_apprv.'</td>
                <td  class="alert-success">'.$tech_in_toprt.'</td>
                <td  class="alert-danger">'.$tech_in_work.'</td>
                <td  class="alert-info">'.$tech_in_todsp.'</td></tr>';
        
        
        $techs.='</table>';
       echo $techs;
        
    }
    
    public function table($stage_code=false)
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                    $this->get_user_menu(' ','Техника');
                    
                
                $this->data['stage_filter']=$stage_code;
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('techs/table', $this->data);
                $this->load->view('bottom', $this->data);
                        
                }
                
        
    }
   
    //Добавление техники в заказ и вобще в систему.
    public function add_techs2($order_id,$hash)
    {
           
        if($this->ion_auth->logged_in())
        {
              //проверим ввод данных
            $this->form_validation->set_rules('serial_num', 'серийный номер', 'trim|required|xss_clean|alpha_numeric');
            $this->form_validation->set_rules('tech_name', 'название', 'trim|required|xss_clean');
            
            if ($this->form_validation->run() == true)
            {   
                 $thisorder=$this->systema_model->view_order($hash)->row();
                 $serial_num=mb_strtoupper($this->input->post('serial_num'),'utf-8');
                 $serial_num= str_replace(array(' ','.','/','\\',':',';','№','#','$','&','%'), '', $serial_num);
                 
                 $tech_data = array('org_id'=>$thisorder->org_id,
                     'uniq_num'=>$serial_num,
                     'serial_num'=>$serial_num,
                     'type_id'=>$this->input->post('tech_type'),
                     'name'=>$this->input->post('tech_name'));
           // добавляем технику в реестр
                   $result=$this->tech->add_tech($serial_num,$tech_data);
           //делаем запись что техника у нас и создаем все этапы под него
                   if(!$result=$this->systema_model->create_stage_tech($order_id,'tech','inofc',date('U'), $this->ion_auth->user()->row()->id,'done',$serial_num,'','',''))
                   {
                       
                       $this->systema_model->update_stage_orders('prewrk', $order_id,1,false,false,'Идет обработка заказа');
                        $stages=$this->systema_model->get_stages('stages_tech')->result();
           //тут создаем этапы
                        foreach ($stages as $stage)
                        {
                            if($stage->code!='inofc')
                            {
                                $this->systema_model->create_stage_tech($order_id,'tech',$stage->code,0,$this->ion_auth->user()->row()->id,false,$serial_num,'','','');
                            }
                                
                        }
                        redirect('techs/working/'.$order_id.'/'.$serial_num.'/initial', 'refresh'); 
                   //redirect('techs/add_techs2/'.$order_id.'/'.$hash, 'refresh');
                   }
                   else
                   {
                       $this->load->view('header', $this->data);
                       echo $result;
                        $this->load->view('bottom', $this->data);
                   }
                   
              }
            else
            { 
            //отображаем форму для внесения техники
            //отправляем ссобщения об ошибках если они есть
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        
                $tech_types=$this->systema_model->tech_types()->result();
                foreach ($tech_types as $tech_type) 
                    {
                        $type[$tech_type->id]=$tech_type->name;
                    }
                
                $this->data['serial_num'] = array('name' => 'serial_num',
			'type' => 'text',
                    'class'=>'span4',
                    'placeholder'=>'серийный номер...',
                    'autocomplete'=>'off',
                   'onkeyup'=>'get_tech(this.value);',
			'value' => $this->form_validation->set_value('serial_num')
                	);
                $this->data['tech_name'] = array('name' => 'tech_name',
			'type' => 'text',
                    'class'=>'span4',
                    'placeholder'=>'наименование...',
                    'autocomplete'=>'off',
                        'value' => $this->form_validation->set_value('tech_name')
                	);
                
                $this->data['techtypes'] = $type;
                $this->data['title']='Внесение техники в заказ';
                $this->data['hash']=$hash;
                $this->data['order_id']=$order_id;
                $this->load->view('header', $this->data);
                $this->load->view('techs/add', $this->data);
                $this->load->view('bottom', $this->data);
        
            }
            
        }
            else
            {
                redirect('main/login', 'refresh');
            }
   
    }
    
    
    public function get_tech()
    {
        $uniq_num = addslashes($this->input->post('str'));
        $result=$uniq_num = trim($uniq_num);
        $tech=$this->systema_model->get_tech($uniq_num);
        if($tech->num_rows()>0)
        {
            $result=$tech->row()->name.'<input type="hidden" name="tech_name" value="'.$tech->row()->name.'">';
            $result.='<input type="hidden" name="tech_id" value="'.$tech->row()->name_id.'">';
        }
        else
        {
        $tech_name = array('name' => 'tech_name',
			'id' => 'tech_name',
			'type' => 'text',
                        'placeholder'=>'наименование устройства...',
                        'class'=>'span12',
                    'value' => $this->form_validation->set_value('tech_name'));
        $result=form_input($tech_name);
        
        }
        echo $result;
       
    }
    
    //Вывод в заказ
     public function view_tech_stages($order_id=false)
    {
        $print_flag=false;
        $stage_code=array('toprt','torpr','apprv','inofc','todgs','totst','todsp');
        $stages=$this->tech->tech_stages($order_id,$stage_code);
        
        $print_flag=false;
        if($stages->num_rows>0)
        {
            $techs='<table class="table  table-condensed table-hover">';
            $techs.='<tr ><th width=220>Устройство (S/N)</th>
                    <th width=200>Статус</th>
                    <th width=200>этап</th>
                    <th width=*>Информация</th>
                    </tr>';
            $cn='';$sid=''; $prev_id=-1;
 
        foreach($stages->result() as $stage)
        {
            $message=''; 
            $mess=$this->messages->get_tech($stage->order_id,$stage->serial_num,FALSE,'add_date','asc');
            if($mess->num_rows()>0)
            {
                $rows=0; 
                $message='';
                foreach ($mess->result() as $item) 
                {
                    if($item->text!='[closed]')
                    {
                        $message.='<b>'.$item->last_name.': </b>';
                        $item->name ? $message.=$item->name : $message.=lang($item->stage_code);
                        $message.=' -> '.strip_tags($item->text).br();
                    }
                    $rows++;
                }     
            }
            else $message=$stage->message;
            $result=$this->tech->timeline($stage->order_id,$stage->serial_num)->result();
            $timestamp = $result[0]->date_start;
            
            $techs.='<tr><td>'.$stage->tech_name.br().anchor_popup('techs/working/'.$stage->order_id.'/'.$stage->serial_num,$stage->serial_num)
            .'</td>';
            $local_date=gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $techs.='<td>'.date('d/m H:i',$local_date).br().timespan($timestamp).'</td>
            <td>'.lang($stage->stage_code).'</td>
            <td>'.$message.'</td></tr>';
            if(!$stage->info || !$stage->adres) $print_flag=true;
        }
        $techs.='<table>';
        }
        echo $techs;     
        
    }
    
    //отображение очереди техники в работу. 
    public function to_work_list($stage_code=false)
    {
        //Здесь готовыим меню
        //Дигностика
        $stages=$this->tech->tech_stages(false,array('inofc','todgs'));
        $count_in_diag=$stages->num_rows();
        //Согласование
        $stages=$this->tech->tech_stages(false,array('apprv'));
        $count_in_apprv=$stages->num_rows();
        //Запчасти
        $stages=$this->tech->tech_stages(false,array('toprt'));
        $count_in_parts=$stages->num_rows();
        //Работа и тестирование
        $stages=$this->tech->tech_stages(false,array('torpr','totst'));
        $count_in_work=$stages->num_rows();
        //Выдача
        $stages=$this->tech->tech_stages(false,array('todsp'));
        $count_in_todsp=$stages->num_rows();
          $MAIN_MENU="";
        $HS='<h4>'; $HE='</h4>';
 switch ($stage_code)
 {
     case 'inofc-todgs': $style1='border: 2px solid blue';$style2='';$style3='';$style4='';$style5='';break;
     case 'apprv': $style1='';$style2='border: 2px solid red';$style3='';$style4='';$style5='';break;
     case 'toprt': $style1='';$style2='';$style3='border: 2px solid green';$style4='';$style5='';break;
     case 'torpr-totst': $style1='';$style2='';$style3='';$style4='border: 2px solid red';$style5='';break;
     case 'todsp': $style1='';$style2='';$style3='';$style4='';$style5='border: 2px solid blue';break;
 }
        

$MAIN_MENU.='<table class="table table-condensed">';
$MAIN_MENU.='<tr ><th width=20% class="alert-info" style="text-align:center;'.$style1.'">'.$HS.anchor('techs/table/inofc-todgs', 'Диагностика').' ('.$count_in_diag.')'.$HE.'</th>
                <th width=20% class="alert-error" style="text-align:center;'.$style2.'">'.$HS.anchor('techs/table/apprv', 'Согласование').' ('.$count_in_apprv.')'.$HE.'</th>
                <th width=20% class="alert-success" style="text-align:center;'.$style3.'">'.$HS.anchor('techs/table/toprt', 'Запчасти').' ('. $count_in_parts.')'.$HE.'</th>
                <th width=20% class="alert-danger" style="text-align:center;'.$style4.'">'.$HS.anchor('techs/table/torpr-totst', 'Работа').' ('. $count_in_work.')'.$HE.'</th>
                <th width=25% class="alert-info" style="text-align:center;'.$style5.'">'.$HS.anchor('techs/table/todsp', 'Выдать').' ('.$count_in_todsp.')'.$HE.'</th></tr>';

        
        
        $MAIN_MENU.='</table>';
      //конец готовки меню

        $stage_code ? $stage_code=explode('-',$stage_code) : $stage_code=array('toprt','torpr','apprv','inofc','todgs','totst','todsp');
        $stages=$this->tech->tech_stages(false,$stage_code);
        
        $print_flag=false;
        if($stages->num_rows>0)
        {
            $techs='<table class="table  table-condensed table-hover">';
            $techs.='<tr ><th width=250>Заказ № / Клиент</th>
                    <th width=220>Устройство (S/N)</th>
                    <th width=200>Статус</th>
                    <th width=*>Информация</th>
                    </tr>';
            $cn='';$sid=''; $prev_id=-1;
 
        foreach($stages->result() as $stage)
        {
            $message=''; 
            $contacter=str_replace('&nbsp;',' ',$stage->contacter);
            $contacter=trim($contacter);
            if($contacter!='') $contacter.='. ';
            $user_anchor=anchor('techs/working/'.$stage->order_id.'/'.$stage->serial_num,$stage->serial_num);
            $mess=$this->messages->get_tech($stage->order_id,$stage->serial_num,FALSE,'add_date','asc');
            if($mess->num_rows()>0)
            {
                $rows=0; 
                $message='';
                foreach ($mess->result() as $item) 
                {
                    if($item->text!='[closed]')
                    {
                        $message.='<b>'.$item->last_name.': </b>';
                        $item->name ? $message.=$item->name : $message.=lang($item->stage_code);
                        $message.=' -> '.strip_tags($item->text).br();
                    }
                    $rows++;
                }
                    
            }
            else $message=$stage->message;
            $techs.='<tr sortid="'.$stage->serial_num.'">';
            if($stage->order_id!=$prev_id)
            {
                $techs.='<td>'.anchor('/orders/view_order/'.$stage->hash.'/techs',$stage->order_id).br().$contacter.' '.$stage->org_name.'</td>
                <td>'.$stage->tech_name.br().$user_anchor.'</td>';
            }
            else
            {
                $techs.='<td></td><td>'.$stage->tech_name.br().$user_anchor.'</td>';
            }
            $prev_id=$stage->order_id;
            
            $local_date=gmt_to_local($stage->date_start, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $techs.='<td>'.date('d/m H:i',$local_date).br().timespan($local_date).'</td>
            <td>'.$message.'</td></tr>';
            if(!$stage->info || !$stage->adres) $print_flag=true;
        }
        $techs.='<table>';
        echo $MAIN_MENU.$techs;
        }
    }
    
    //отображения карточки принтера для работы мастеру, либо просто обзор для менеджера/администратора
    public function working($order_id,$serial_num,$init=false)
    {
        if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                     $this->get_user_menu();
                     //берем данные про технику (название, серийник, номер заказа,клиент)
                     $this->data['tech_info']=$this->tech->tech_info($serial_num)->row();
                    // и берем этапы данной единицы техники
                     $this->data['stages']=$this->tech->stages_of_tech($order_id,$serial_num)->result();
                     
                    //берем сообщения к этапам работ по технике
                     $this->data['messages']=$this->messages->get_tech($order_id,$serial_num,false,'add_date','asc');
                
                    $this->data['order_id']=$order_id;
                    $this->data['serial_num']=$serial_num;
                    
                    $this->load->view('header', $this->data);
                    
                    if($init=='initial') $this->load->view('techs/init', $this->data);
                    
                    else
                    {
                        $this->load->view('user_menu', $this->data);
                        $this->load->view('techs/working', $this->data);
                    }
                        
                    $this->load->view('bottom', $this->data);
                }
    }
    
   public function close_stage($stage_code,$serial_num,$order_id)
    {
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
             redirect('main/login', 'refresh');
        }
        else
        {
          if($this->input->post('close_please'))
           {
            //закрываем этап. функция возвращает код следующего этапа. если этап последний то возвращает код последнего этапа
            $next_stage=$this->systema_model->update_tech_stage($stage_code, $order_id, $serial_num, false, date('U'),'done',false,$this->ion_auth->user()->row()->id,'');
            //и пишем скрытое ссобщение к данному этапу о его закрытии
            $data['order_id']=$order_id;
            $data['uniq_num']=$serial_num;
            $data['stage_code']=$stage_code;
            $data['text']='[closed]';
            $data['user_id']=$this->ion_auth->user()->row()->id;
            $data['add_date']=date('U');
            $data['hidden']=1;
            $this->messages->add_message($data);
            
            if($stage_code!=$next_stage)
            {
                //открываем следующий этап
                $this->systema_model->update_tech_stage($next_stage, $order_id, $serial_num, date('U'), false,'done',false,$this->ion_auth->user()->row()->id,'');
            }
            if($stage_code=='inofc')
            {
                $result=$this->systema_model->update_stage_orders('prewrk', $order_id,1,FALSE,date('U'),'ok')->row();
                $this->systema_model->update_stage_orders('inwrk', $order_id,1,date('U'),FALSE);
                $this->systema_model->update_stage_orders('pprdoc', $order_id,1,date('U'),FALSE);
                $this->systema_model->update_stage_orders('toclnt', $order_id,1,date('U'),FALSE);
                
                redirect('techs/diag_list/'.$serial_num.'/'.$order_id, 'refresh');
            }
            
           }
           
          redirect($_SERVER['HTTP_REFERER'], 'refresh');                
           
        }
        
    }
    
    public function diag_list($serial_num,$order_id)
    {
        $this->data['tech_info']=$this->tech->tech_info($serial_num)->row();
        // и берем этапы данной единицы техники
        $this->data['stages']=$this->tech->stages_of_tech($order_id,$serial_num)->result();
        //берем сообщения к этапам работ по технике
        $this->data['messages']=$this->messages->get_tech($order_id,$serial_num,false,'add_date','asc');
        $this->data['order_id']=$order_id;
        $this->data['serial_num']=$serial_num;
        $this->load->view('header', $this->data);
        $this->load->view('techs/diag_list', $this->data);
    }


    
    
//установление диагноза техники и запись в базу
    public function set_diag($serial_num,$order_id)
    {
        $info='Диагноз: '.$this->input->post('diagnos').'<br/>Надо сделать: '.$this->input->post('needthis')
                .'<br/>Необходимые запчасти: '.$this->input->post('parts');
        //записываем и закрываем диагноз
 $this->systema_model->update_tech_stage('todgs', $order_id, $serial_num, false, 
        date('U'),'done',false,$this->ion_auth->user()->row()->id,$info);

        //открываем апрув
$this->systema_model->update_tech_stage('apprv', $order_id, $serial_num, date('U'),
        false,'needapprv' ,false,$this->ion_auth->user()->row()->id,$info);
//пишем в этап запроса запчастей необходимые запчасти
$this->systema_model->update_tech_stage('toprt', $order_id, $serial_num, false,
        false,'needparts' ,false,$this->ion_auth->user()->row()->id,$this->input->post('parts'));
//пишем в этап ремонта что надо сделать
$this->systema_model->update_tech_stage('torpr', $order_id, $serial_num, false,
        false,'needrpr' ,false,$this->ion_auth->user()->row()->id,$this->input->post('needthis'));
//сразу пишем в этап посттеста что б потом проверить все ли исправленно
$this->systema_model->update_tech_stage('totst', $order_id, $serial_num, false,
        false,'needrpr' ,false,$this->ion_auth->user()->row()->id,$this->input->post('needthis'));


//$this->systema_model->update_tech_sort($serial_num,1111111);
        
       
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
 
    //завершение работы мастером
    public function done($serial_num,$order_id)
    {
        //записываем и закрываем ремонт
 $this->systema_model->update_tech_stage('torpr', $order_id, $serial_num, false, 
        date('U'),'done',false,$this->ion_auth->user()->row()->id,$this->input->post('donethat'));

        //открываем посттест
         //пока что отдельного тестировщика  унас нету потому закрываем посттест
//автоматом. посттест делает мастер

$this->systema_model->update_tech_stage('totst', $order_id, $serial_num, date('U'),
        date('U'),'done' ,false,$this->ion_auth->user()->row()->id,'проверено мастером');


//открываем выдачу
$this->systema_model->update_tech_stage('todsp', $order_id, $serial_num, date('U'),
        false,'needdsp' ,false,$this->ion_auth->user()->row()->id,$this->input->post('donethat'));


$this->systema_model->update_tech_sort($serial_num,1111111);
        
       
        redirect('/master_tech', 'refresh');
    }
    

    //техника выдана
    public function wait_client($serial_num,$order_id)
    {
        //закрыть выдачу
        $this->systema_model->update_tech_stage('todsp', $order_id, $serial_num, false, date('U'),
                      'done',false);
        
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

       
    //функция сортировки списка техники у которой обращается жэкуери плагин
    public function sort()
    {
        foreach (explode(',',$this->input->post('ids')) as $n => $id)
        {
            $this->systema_model->update_tech_sort($id,$n);
        }
    }
    
    
    //вывод информации о готовой технике (что делалось) в заказ
    public function get_done_techs($order_id=false,$org_id=false,$paymethod=false,$discount=0)
    {
        $techs=$this->systema_model->tech_stages_done($order_id,array('torpr'));
                        
        $response='<b>Выполненные работы по технике</b>
            <table class="table table-condensed table-bordered" width="100%">
            <thead><tr>
            <th width="20"></th>
            <th width="200">Устройтво</th>
            <th width="*">Сделано</th>
            <th width="50">Цена</th></tr>
            </thead>';
        if($techs->num_rows()>0)
        {
            $tech="";$num=1;
            //$discount?$discountD=$discount-1:$discountD=0;
            //if($paymethod=='bnltov'&&$org_id==11) $nacenka=10;
            foreach($techs->result() as $tech)
            {
                //print_r($tech);
                $message=''; 
                $mess=$this->messages->get_tech($order_id,$tech->serial_num,'torpr','add_date','asc');
                if($mess->num_rows()>0)
                {
                    $rows=0; 
                    $message='';
                    foreach ($mess->result() as $item) 
                    {
                        if($item->text!='[closed]')
                        {
                            $message.=strip_tags($item->text).br();
                        }
                        $rows++;
                    }        
                }
                $response.='<tr><td>'.$num.'</td>
                <td>'.$tech->name.br().anchor_popup('techs/working/'.$order_id.'/'.$tech->serial_num,$tech->serial_num).'</td>
                <td>'.$message.'</td>
                <td> - </td></tr>';
                $num++;
            }
        $response.='</table>';
        
        
       echo $response;
       }
    }
    
    
    
    public function client_answer($serial_num,$order_id)
    {
        //проверяем какой был ответ пользователя и ставим технику на необходимые этапы
        switch ($this->input->post('apprv'))
        {
            // ремонт
            case 'torpr':
                //закрываем этап согласования
                $this->systema_model->update_tech_stage('apprv', $order_id, $serial_num, false,date('U'),
                      'answered',false);
                //установить старт дату ремонта и инфо апрув
                $this->systema_model->update_tech_stage('torpr', $order_id, $serial_num, date('U'),'setnull',
                      'approved',false);
                break;
            
            //ремонт(без согласования)
            case 'natorpr':
                //закрываем этап согласования
                $this->systema_model->update_tech_stage('apprv', $order_id, 
                        $serial_num, false,date('U'), 'noanswer',false);
                //установить старт дату ремонта и инфо нотапрув
                $this->systema_model->update_tech_stage('torpr', $order_id, 
                        $serial_num, date('U'),'setnull','notapproved',false);
                break;
            
            case 'stop':
                 $this->systema_model->update_tech_stage('apprv', $order_id, $serial_num, false,date('U'),
                      'answered',false);
              //установить старт дату и стоп дату ремонта в 0 и инфо stop 
              $this->systema_model->update_tech_stage('torpr', $order_id, $serial_num, 'setnull','setnull',
                      'stop',false);
              
              //установить старт дату на упаковку и инфо stop
              $this->systema_model->update_tech_stage('todsp', $order_id, $serial_num, date('U'),'setnull',
                      'stop',false);
              break;
        }
         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
    
    
    
    // печать доставочного листа
    public function print_delivery_list($order_hash)
    {
        if($this->ion_auth->logged_in())
        {
            if($order_hash)
            {
                $this->data['order']=$this->systema_model->view_order($order_hash)->row();
                $this->data['techs']=$this->tech->tech_stages($this->data['order']->id,array('todsp'));
                foreach ($this->data['techs']->result() as $tech)
                {
                    $attach[$tech->serial_num]=$this->messages->get_tech($this->data['order']->id,$tech->serial_num,'attach','stage_code','asc')->result();
                }
               $this->data['attach']=$attach;
                $this->load->view('techs/delivery_list', $this->data);
                
            }
            else
            {
                return FALSE;
            }
        }        
        else        
        {            
            return false;        
        }
    }
    
    
   
    
   
}
?>
