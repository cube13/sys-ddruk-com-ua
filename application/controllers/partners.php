<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Partners extends CI_Controller
{
    function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');
		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
			$this->load->library('mongo_db') :
		 $this->load->model('systema_model');
                $this->load->database();
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                $this->load->helper('language');
                $this->load->helper('date');
                
                $this->load->model('messages_model','messages');
                $this->load->model('cartridge_model','cartridge');
                
	}
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
    
    public function index()
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                   $this->get_user_menu(anchor('partners/create',lang('menu_partners_create')));
                     //set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
		
                $this->data['partners']=$this->systema_model->view_partners('short_name');
                $this->data['color']="#eeeeee";
                
                            
                        
                }
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('partners/main', $this->data);
                $this->load->view('bottom', $this->data);
        
    }
    
    public function select()
    {
        // экранируем одиночные и двойные кавычки, убираем пробелы
        $short_name = addslashes($this->input->post('str'));
        $short_name = trim($short_name);
        $partners=$this->systema_model->select_partners('short_name',$short_name);
        
        echo "<div class=\"input-prepend input-append\"><SELECT name='org_id'>";
        foreach ($partners->result() as $partner) {
            echo '<option value='.$partner->id.'>'.$partner->short_name.'</option>';
        }
        echo "</SELECT>";
        echo '<button class="btn" type="submit"><i class="icon-ok"></i></button></div>';
    }
    
    public function view($partner_id,$part_view=false)
    {
        if($this->ion_auth->logged_in())
        {
            $this->get_user_menu(' ','');
            $this->data['partner']=$this->systema_model->view_partners(FALSE,$partner_id)->row();
            $this->data['contacts']=$this->messages->org_contact($partner_id);
           
            
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('partners/view', $this->data);
                if($part_view)
                {
                    $this->data['orders']=$orders=$this->systema_model->view_orders_table($partner_id,1,false,500);
                            
                    switch ($part_view)
                    {
                        
                        case 'orders':
                            $this->load->view('partners/orders', $this->data);
                            break;
                        
                        case 'cartridges':
                            $date_start=mktime(0, 0, 0, date('m')-1, 1,date('Y'));
                            $date_end=mktime(0, 0, 0, date('m'), 0, date('Y'));
                            $this->data['refills_12']=$this->systema_model->done_cartridge_stage_org($date_start,$date_end,$org_id=$partner_id,$stage='inrfl')->row()->count;
                            $this->data['recikls_12']=$this->systema_model->done_cartridge_stage_org($date_start,$date_end,$org_id=$partner_id,$stage='inrck')->row()->count;
                            $date_start=mktime(0, 0, 0, date('m'), 1,date('Y'));
                            $date_end=mktime(0, 0, 0, date('m')+1, 0, date('Y'));
                            $this->data['refills']=$this->systema_model->done_cartridge_stage_org($date_start,$date_end,$org_id=$partner_id,$stage='inrfl')->row()->count;
                            $this->data['recikls']=$this->systema_model->done_cartridge_stage_org($date_start,$date_end,$org_id=$partner_id,$stage='inrck')->row()->count;
                            $this->data['cartridges']=$this->systema_model->get_cartridge(false,$partner_id);
                            $this->load->view('partners/cartridges', $this->data);
                            break;
                        
                        case 'prices':
                            $this->data['prices']=$this->systema_model->get_price_cart($partner_id);
                            $this->load->view('partners/prices', $this->data);
                            break;
                    }
                    
                        
                }
                    
                $this->load->view('bottom', $this->data);
            
            }
         else
            {
                //redirect them to the login page
                 redirect('main/login', 'refresh');
            }
            
    }
    
    public function create()
    {
        if($this->ion_auth->logged_in())
            {
            $this->get_user_menu(anchor('partners', lang('menu_partners'))
              ,'');
                   
            //проверим ввод данных
            $this->form_validation->set_rules('full_name', lang('partners_full_name'), 'xss_clean');
            $this->form_validation->set_rules('short_name', lang('partners_short_name'), 'required|xss_clean');
            $this->form_validation->set_rules('adres', lang('partners_fiz_adres'), 'required|xss_clean');
            $this->form_validation->set_rules('tel', lang('partners_tel'), 'required|xss_clean');
            $this->form_validation->set_rules('edrpou', lang('partners_edrpou'), 'xss_clean');
            $this->form_validation->set_rules('ipn', lang('partners_ipn'), 'xss_clean');
            $this->form_validation->set_rules('svid_pdv', lang('partners_pdv'), 'xss_clean');
            $this->form_validation->set_rules('mfo', lang('partners_mfo'), 'xss_clean');
            $this->form_validation->set_rules('bank_accnt', lang('partners_bank_accnt'), 'xss_clean');
            $this->form_validation->set_rules('direktor', lang('partners_direktor'), 'xss_clean');
            
             if ($this->form_validation->run() == true)
            {
               $partner_data = array('full_name' => $this->input->post('full_name'),
        		'short_name' => $this->input->post('short_name'),
                   'adres' => $this->input->post('adres'),
                   'tel' => $this->input->post('tel'),
                   'edrpou' => $this->input->post('edrpou'),
                   'ipn' => $this->input->post('ipn'),
                   'svid_pdv' => $this->input->post('svid_pdv'),
                   'mfo' => $this->input->post('mfo'),
                   'bank_accnt' => $this->input->post('bank_accnt'),
                   'direktor' => $this->input->post('direktor')
		);
            }
            
            if ($this->form_validation->run() == true && $this->systema_model->partner_create($partner_data))
            {
                $this->session->set_flashdata('message', "Організацію додано");
		redirect("partners", 'refresh');
            }
            
            else
            { 
            //отображаем форму создания организации
            //отправляем ссобщения об ошибках если они есть
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $this->data['full_name'] = array('name' => 'full_name',
			'id' => 'full_name',
			'type' => 'text',
                        'size'=>'61',
			'value' => $this->form_validation->set_value('full_name')
                	);
        $this->data['short_name'] = array('name' => 'short_name',
			'id' => 'short_name',
			'type' => 'text',
	'size'=>'61',		
            'value' => $this->form_validation->set_value('short_name')
                	);
        $this->data['adres'] = array('name' => 'adres',
			'id' => 'adres',
			'type' => 'text',
            'size'=>'61',
			'value' => $this->form_validation->set_value('adres')
                	);
         $this->data['tel'] = array('name' => 'tel',
			'id' => 'tel',
			'type' => 'text',
			'value' => $this->form_validation->set_value('tel')
                	);
        $this->data['edrpou'] = array('name' => 'edrpou',
			'id' => 'edrpou',
			'type' => 'text',
			'value' => $this->form_validation->set_value('edrpou')
                	);
        $this->data['ipn'] = array('name' => 'ipn',
			'id' => 'ipn',
			'type' => 'text',
			'value' => $this->form_validation->set_value('ipn')
                	);
        $this->data['svid_pdv'] = array('name' => 'svid_pdv',
			'id' => 'svid_pdv',
			'type' => 'text',
			'value' => $this->form_validation->set_value('svid_pdv')
                	);
        $this->data['mfo'] = array('name' => 'mfo',
			'id' => 'mfo',
			'type' => 'text',
			'value' => $this->form_validation->set_value('mfo')
                	);
        $this->data['bank_accnt'] = array('name' => 'bank_accnt',
			'id' => 'bank_accnt',
			'type' => 'text',
			'value' => $this->form_validation->set_value('bank_accnt')
                	);
        $this->data['direktor'] = array('name' => 'direktor',
			'id' => 'direktor',
			'type' => 'text',
            'size'=>'61',
			'value' => $this->form_validation->set_value('direktor')
                	);
         $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
		$this->load->view('partners/create', $this->data);
                $this->load->view('bottom', $this->data);
        }
            }
            else
            {
                //redirect them to the login page
                 redirect('main/login', 'refresh');
            }
        
        
    }
    
  public function edit($partner_id,$change=false)
    {
        if($this->ion_auth->logged_in())
            {
            $this->get_user_menu(anchor('partners', lang('menu_partners'))
              ,'');
            
             $partner_data = array('full_name' => $this->input->post('full_name'),
        		'short_name' => $this->input->post('short_name'),
                   'adres' => $this->input->post('adres'),
                   'tel' => $this->input->post('tel'),
                   'edrpou' => $this->input->post('edrpou'),
                   'ipn' => $this->input->post('ipn'),
                   'svid_pdv' => $this->input->post('svid_pdv'),
                   'mfo' => $this->input->post('mfo'),
                   'bank_accnt' => $this->input->post('bank_accnt'),
                   'direktor' => $this->input->post('direktor'),
                   'paymethod'=>$this->input->post('paymethod'),
                   'discount'=>$this->input->post('discount'),
                    'contract'=>$this->input->post('contract')
                );
             
            if ($change==1 && $this->systema_model->partner_update($partner_id,$partner_data)) 
            {
                //redirect($_SERVER['HTTP_REFERER'], 'refresh');
                redirect('/partners/view/'.$partner_id, 'refresh');
            }
            else
            {
        $partner=$this->systema_model->view_partners(FALSE,$partner_id)->row();    
        
        $nal=FALSE;$bnlfop=FALSE;$bnltov=FALSE;
        switch ($partner->paymethod)
                        {
                         case 'nal':
                             $nal=TRUE;
                             break;
                         case 'bnlfop':
                             $bnlfop=TRUE;
                             break;
                         case 'bnltov':
                             $bnltov=TRUE;
                             break;
                         case 'bnltovitfs':
                            $bnltovitfs=TRUE;
                            break;
            case 'bnltovfsu':
                $bnltovfsu=TRUE;
                break;
                        }
        $this->data ['nal']=array('name'        => 'paymethod',
    'id'          => 'paymethod',
    'value'       => 'nal',
    'checked'     => $nal
    );
         $this->data ['bnltov']=array('name'        => 'paymethod',
    'id'          => 'paymethod',
    'value'       => 'bnltov',
    'checked'     => $bnltov
    );
                $this->data ['bnltovitfs']=array('name'        => 'paymethod',
                    'id'          => 'paymethod',
                    'value'       => 'bnltovitfs',
                    'checked'     => $bnltovitfs
                );
                $this->data ['bnltovfsu']=array('name'        => 'paymethod',
                    'id'          => 'paymethod',
                    'value'       => 'bnltovfsu',
                    'checked'     => $bnltovfsu
                );
         $this->data ['bnlfop']=array('name'        => 'paymethod',
    'id'          => 'paymethod',
    'value'       => 'bnlfop',
    'checked'     => $bnlfop
    );

                        
        
        $this->data['full_name'] = array('name' => 'full_name',
			'id' => 'full_name',
			'type' => 'text',
                        'size'=>'61',
			'value' => $partner->full_name,
                        'class'=>'span12' 
                	);
        $this->data['short_name'] = array('name' => 'short_name',
			'id' => 'short_name',
			'type' => 'text',
            'size'=>'61',
			'value' => $partner->short_name,
                        'class'=>'span12' 
                	);
        $this->data['adres'] = array('name' => 'adres',
			'id' => 'adres',
			'type' => 'text',
            'size'=>'61',
			'value' => $partner->adres,
                        'class'=>'span12' 
                	);
        
         $this->data['tel'] = array('name' => 'tel',
			'id' => 'tel',
			'type' => 'text',
			'value' => $partner->tel,
                        'class'=>'span12' 
                	);
       
        $this->data['edrpou'] = array('name' => 'edrpou',
			'id' => 'edrpou',
			'type' => 'text',
			'value' => $partner->edrpou,
                        'class'=>'span12' 
                	);
        $this->data['ipn'] = array('name' => 'ipn',
			'id' => 'ipn',
			'type' => 'text',
			'value' => $partner->ipn
                	);
        $this->data['svid_pdv'] = array('name' => 'svid_pdv',
			'id' => 'svid_pdv',
			'type' => 'text',
			'value' => $partner->svid_pdv,
                        'class'=>'span12' 
                	);
        $this->data['mfo'] = array('name' => 'mfo',
			'id' => 'mfo',
			'type' => 'text',
			'value' => $partner->mfo,
                        'class'=>'span12' 
                	);
        $this->data['bank_accnt'] = array('name' => 'bank_accnt',
			'id' => 'bank_accnt',
			'type' => 'text',
			'value' => $partner->bank_accnt,
                        'class'=>'span12' 
                	);
        $this->data['direktor'] = array('name' => 'direktor',
			'id' => 'direktor',
			'type' => 'text',
            'size'=>'61',
			'value' => $partner->direktor,
                        'class'=>'span12' 
                	);
        $this->data['contract'] = array('name' => 'contract',
			'id' => 'contract',
			'type' => 'text',
            'size'=>'61',
			'value' => $partner->contract,
                        'class'=>'span12' 
                	);
        
        
         $this->data['discount'] = array('name' => 'discount',
			'id' => 'discount',
			'type' => 'text',
            'size'=>'6',
			'value' => $partner->discount,
                        'class'=>'span12' 
                	);
                        
         
       
         $this->data['partner_id']=$partner_id;
         $this->data['contacts']=$this->messages->org_contact($partner_id);
        $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
		$this->load->view('partners/change', $this->data);
                $this->load->view('bottom', $this->data);
            }
            }
            else
            {
                //redirect them to the login page
                 redirect('main/login', 'refresh');
            }
        
        
    }
    
    public function create_price_cart($partner_id,$cartridge_id=false)
    {
        $this->systema_model->create_price_cart($partner_id,$cartridge_id);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
                
    }
   
    public function change_price()
    {
        foreach ($this->input->post() as $key => $value) 
            {
                if($key!='submit') $this->systema_model->change_price_item($key,'price',$value);//echo $key.' -> '.$value.'<br/>';
            }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
            
    }
    
    public function lock_price_item($item_id)
    {
        $this->systema_model->change_price_item($item_id,'locked',1);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
    
    public function unlock_price_item($item_id)
    {
        $this->systema_model->change_price_item($item_id,'locked',0);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
}
?>
