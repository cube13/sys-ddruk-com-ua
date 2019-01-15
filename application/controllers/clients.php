<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Clients extends CI_Controller
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
			$this->load->database();
                $this->lang->load('ion_auth', 'russian');
                $this->load->helper('date');
                $this->load->model('cartridge_model','cartridge');
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
                    $user=$this->ion_auth->user()->row();
                    
                    $user_groups = $this->ion_auth->get_users_groups()->result();
                    $usermenu="";$user_here="";
                    foreach($user_groups as $group)
                    {
                        $usermenu.=anchor($group->name,$group->description)." | ";
                        if($group->name==$this->uri->segment(1))
                        {
                            $user_here=" - ".$group->description;
                        }
   
                    }
                    $this->data['usermenu']=$usermenu;
                    $this->data['title']=$user->first_name." ".$user->last_name.$user_here;
                   
			
                    //set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                        
                        
                        $this->load->view('main', $this->data);
		}
        
    }
 
    
   
}
?>
