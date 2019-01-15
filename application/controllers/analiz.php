<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Analiz extends CI_Controller
{
    function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		
                $this->load->helper('file');
                $this->load->helper('email');
                $this->load->helper('html');
                
		$this->load->database();
              
                
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                
                
	}
        
        
    public function index()
    {
        
        $this->load->view('translator/main', $this->data);
    }
    
    
    
    
    
    
   
    
   
}
?>
