<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Subscription extends CI_Controller
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
                
        $this->load->database();
        $this->lang->load('ion_auth', 'russian');
        $this->lang->load('systema', 'russian');
                
        $this->load->model('systema_model');
        $this->load->model('smsclient_model');
        $this->load->model('systema_fin_model','fin');
        $this->load->model('cartridge_model','cartridge');
        $this->load->model('messages_model','messages');
        $this->load->model('cartridge_model','cartridge');
        
        $this->load->model('subscription_model','subscription');
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
            $this->get_user_menu(anchor_popup('subscription/charges',' Тираж'),'Керування замовленнями');
                   
            $this->data['subscription']=$this->subscription->view();
            
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('subscription/main', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }
    
    public function charges()
    {
        if (!$this->ion_auth->logged_in())
	{
            redirect('main/login', 'refresh');
	}
	else
	{
            $this->get_user_menu(anchor_popup('subscription/charges',' Тираж'),'Керування замовленнями');
            
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('subscription/create_form', $this->data);
            $this->load->view('bottom', $this->data);
        }
        
    }
    
    public function create_charges()
    {
        if (!$this->ion_auth->logged_in())
	{
            redirect('main/login', 'refresh');
	}
	else
	{
            echo 'test';
            $this->subscription->create_charges($this->input->post('start_num'),$this->input->post('end_num'),$this->input->post('capacity'));
        }
        
    }
    
    
    
    
    
}
?>
