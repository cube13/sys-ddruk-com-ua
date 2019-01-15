<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Master_cartridge extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
	$this->load->library('ion_auth');
	$this->load->library('session');
	$this->load->library('form_validation');
	$this->load->helper('url');
	$this->load->database();
        $this->lang->load('ion_auth', 'russian');
        $this->lang->load('systema', 'russian');
        $this->load->helper('language');
        $this->load->helper('date');
        
        $this->load->model('cartridge_model','cartridge');
    }
    
    private function get_user_menu($usermenu="",$userhere="")
    {
        $user=$this->ion_auth->user()->row();
        if($usermenu)
        {
            $tomain='';
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
            redirect('main/login', 'refresh');
	}
	else
	{
            $this->get_user_menu(' ');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('cartridges/master', $this->data);
            $this->load->view('bottom', $this->data);
        }
        
    }
  
    
   
}
?>
