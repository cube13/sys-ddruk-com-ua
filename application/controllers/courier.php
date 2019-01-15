<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Courier extends CI_Controller
{
       function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
                $this->load->library('session');
		$this->load->library('form_validation');
                $this->load->library('table');
                
		$this->load->helper('url');
                $this->load->helper('html');
                $this->load->helper('language');
                $this->load->helper('date');
                

// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
			$this->load->library('mongo_db') :
			$this->load->database();
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                
                $this->load->model('systema_model');
                $this->load->model('smsclient_model');
                $this->load->model('systema_fin_model','fin');
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
    
    public function index($user_id)
    {
        
    if($this->ion_auth->logged_in())
            {
            $this->get_user_menu('','Керування замовленнями');
      
                $this->data['stages_menu']='';
                $this->data['user_id']=$user_id;
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('orders/courier', $this->data);
                $this->load->view('bottom', $this->data);
            }
               
            else
            {
                //redirect them to the login page
                 redirect('main/login', 'refresh');
            }
    }
}