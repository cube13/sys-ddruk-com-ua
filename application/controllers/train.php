<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Train extends CI_Controller
{
    function __construct(){
	
         parent::__construct();
	$this->load->library('ion_auth');
        $this->load->library('session');
	$this->load->library('form_validation');
        $this->load->library('table');
        $this->load->library('slack');
                
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
    }
          
    public function index(){
        $this->load->view('traincontrol/train', $this->data);
        
      
    }
}
?>
