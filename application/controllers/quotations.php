<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Quotations extends CI_Controller
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
		
                $this->load->model('messages_model','messages');
                
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                $this->load->model('cartridge_model','cartridge');
                
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
    public function index()
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                   $this->get_user_menu();
                     //set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
		}
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('main', $this->data);
                $this->load->view('bottom', $this->data);
        
    }
  
    public function add($order_id,$uniq_num,$stage_code,$value_id=false)
    {
        if(!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
	else
	{
            echo $this->input->post('text');
            //print_r($this->input->post());
            if(strip_tags($this->input->post('text'))!='')
            { 
                $data['order_id']=$order_id;
                $data['uniq_num']=$uniq_num;
                $this->input->post('code') ? $data['stage_code']=$this->input->post('code') : $data['stage_code']=$stage_code;
                $data['text']=$this->input->post('text');
                $data['user_id']=$this->ion_auth->user()->row()->id;
                $data['add_date']=date('U');
            }
            elseif($value_id)
            {
                $data['text']=$this->db->select('value')
                        ->from('orgs_contacts')
                        ->where('id',$value_id)
                        ->get()->row()->value;
                
                $data['order_id']=$order_id;
                $data['uniq_num']=$uniq_num;
                $data['stage_code']=$stage_code;
                
                $data['user_id']=$this->ion_auth->user()->row()->id;
                $data['add_date']=date('U');
            }
            else redirect($_SERVER['HTTP_REFERER'], 'refresh');
            
            if($this->messages->add_message($data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
            else echo 'some error';
            
	}   
    }
    
    public function hide_mess($mess_id)
    {
        $this->messages->update($mess_id,array('hidden'=>1));
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
    
    public function add_org_contact($org_id)
    {
        if(!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
	else
	{
            //print_r($this->input->post());
            if(strip_tags($this->input->post('text'))!='')
            {
                $data=array('timestamp'=>date('U'),
                    'user_id'=>$this->ion_auth->user()->row()->id);
                $data['org_id']=$org_id;
                $data['type']=$this->input->post('type');
                $data['value']=$this->input->post('text');
            }
            else redirect($_SERVER['HTTP_REFERER'], 'refresh');
            if($this->messages->add_org_contact($data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
            else echo 'some error';
        }   
    }
    
    public function del_org_contact($contact_id)
    {
        if(!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
	else
	{
            if($contact_id)
            {
                $data=array('visible'=>'0',
                    'timestamp'=>date('U'),
                    'user_id'=>$this->ion_auth->user()->row()->id);
                if($this->messages->update_org_contact($contact_id,$data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
                else echo 'some error';
            }
        }   
    }
    
    public function get_messages()
    {
        $mess=$this->messages->get_user_messages($this->ion_auth->user()->row()->id);
        if($mess->num_rows()>0) echo '<a href="/messages/read" target="new"><i class="icon-comment"></i> '.$mess->num_rows().'</a>';
        else echo '<i class="icon-comment-alt"></i>';
    }
    
    public function read()
    {
        if (!$this->ion_auth->logged_in())
	{
            redirect('main/login', 'refresh');
	}
	else
	{
            $this->data['mess']=$this->messages->get_user_messages($this->ion_auth->user()->row()->id);
            $this->get_user_menu(' ');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('messages/main', $this->data);
            $this->load->view('bottom', $this->data);
        }    
    }

        
    
   
}
?>
