<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Stat extends CI_Controller
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
                $this->load->model('stat_model','stat');
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
            redirect('main/login', 'refresh');
	}
	else
	{
            $this->get_user_menu(anchor('stat/techs', lang('menu_techs')),'');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('main', $this->data);
            $this->load->view('bottom', $this->data);
        }
            
    }
    
    public function techs()
    {
        if (!$this->ion_auth->logged_in())
	{
            redirect('main/login', 'refresh');
	}
	else
	{
            $this->get_user_menu(anchor('stat/techs', lang('menu_techs')),'');
        
            $this->data['date_start']=$this->input->post('date_start');
            $this->data['date_end']=$this->input->post('date_end');        
            $date_s=explode('-', $this->input->post('date_start'));
            $date_e=explode('-', $this->input->post('date_end'));
            echo 'y:'.$date_s[0].'m:'.$date_s[1].'d:'.$date_s[2];
            $this->data['tech_stat']=$this->stat->tech(mktime(0, 0, 0, $date_s[1], $date_s[2], $date_s[0]),mktime(23, 59, 59, $date_e[1], $date_e[2], $date_e[0]));
            
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('stat/techs', $this->data);
            $this->load->view('bottom', $this->data);
        }
            
    }
   
 
        
    
   
}
?>
