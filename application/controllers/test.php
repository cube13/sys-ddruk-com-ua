<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Test extends CI_Controller
{
    function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('slack');
        $this->load->library('call');
                $this->load->helper('url');
		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
			$this->load->library('mongo_db') :
		
                $this->load->database();
                $this->lang->load('ion_auth', 'russian');
                $this->lang->load('systema', 'russian');
                $this->load->helper('language');
                $this->load->helper('date');
                
                $this->load->model('systema_model','main');
                $this->load->model('cartridge_model','cartridge');
                
	}
           private function get_user_menu($usermenu="",$userhere="")
    {
         $user=$this->ion_auth->user()->row();
         if($usermenu)
            {
                $this->data['usermenu']=$usermenu;
                $this->data['title']=$user->first_name." ".$user->last_name." - ".$userhere;
               $this->data['tomain']= anchor('main', lang('menu_tomain'));
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

    public function sendsms($from=false, $to=false)
    {

        echo 'sendSms ';
        if($this->input->is_cli_request())
        {
            echo $this->input->ip_address();
        }
        else
        {
            echo $this->input->ip_address();
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
   function login()
	{
		
		//validate form input
		$this->form_validation->set_rules('login', 'Логин', 'required');
		$this->form_validation->set_rules('password', 'Пароль', 'required');

		if ($this->form_validation->run() == true)
		{ //check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('login'), $this->input->post('password'), $remember))
			{ //if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect($this->config->item('main'), 'refresh');
                                
			}
			else
			{ //if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('main/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{  //the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['login'] = array('name' => 'login',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('login'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

                        $this->data['title']=lang('global_login');
                        
                        $this->load->view('header', $this->data);
			$this->load->view('login', $this->data);
                        $this->load->view('bottom', $this->data);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Выход";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them back to the page they came from
		redirect('main', 'refresh');
	}
        
         public function autocomplete()
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                    $this->data['title']='Тесты всякие';
                    $this->data['message']='';
                        $this->load->view('header', $this->data);
			$this->load->view('test/autocomplete', $this->data);
                        $this->load->view('bottom', $this->data);
		}
               
        
    }
    public function search_street()
    {
        
     if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
                        redirect('main/login', 'refresh');
		}
		else
		{
                    $cart=$this->main->select_cartridges(false,$this->input->get('term'));
                    foreach ($cart->result() as $item)
                    {$elements[]='"'.$item->cart_name.' ('.$item->brand_name.' '.$item->printer_name.')"';}
                       
                        $s = '['.implode(",", $elements).']';
                    echo $s;
                    
		}
               
        
    }
    
    public function slack()
    {
        $Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
        
        //print_r($Slack->call('users.list'));
        
        print_r($Slack->call('chat.postMessage', 
                array(
                'channel' => '#repair-parts',
                'as_user'=>'true',
                'username'=>'U0AM6U5T5',
                'text' => 'Пришел новый принтер'
                )));
        
        
    }
    
    public function angular()
    {
        
        $this->load->view('test/angular', $this->data);
       
    }
    
    public function get_cart()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        $stages=$this->main->cartridge_stages(false,array('inrfl','inrck'));
        foreach($stages->result() as $stage)
        {
            print_r($stage);
            
        }
        $outp = "";
        /*while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
    if ($outp != "") {$outp .= ",";}
    $outp .= '{"Name":"'  . $rs["CompanyName"] . '",';
    $outp .= '"City":"'   . $rs["City"]        . '",';
    $outp .= '"Country":"'. $rs["Country"]     . '"}';
}
$outp ='{"records":['.$outp.']}';
$conn->close();

echo($outp);*/
    }
    
    public function telegramSend($message,$chat_id,$token="352825107:AAG2Gn2NaV0PTJQYPIS0HPjLCBDqRy8Zx7k")
    {
       
    $url="https://api.telegram.org/bot".$token."/".
            "sendMessage?disable_web_page_preview=true&".
            "chat_id=".$chat_id."&".
            "text=".$message;
      $ch = curl_init();
     
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      //curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
      $result = curl_exec($ch);
      curl_close($ch);
    echo $result;
    return $result ? json_decode($result, true) : false;
    }

    public function telegramGetUpdates($token="352825107:AAG2Gn2NaV0PTJQYPIS0HPjLCBDqRy8Zx7k")
    {

        $url="https://api.telegram.org/bot".$token."/getUpdates";
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      //curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
      $result = curl_exec($ch);
      curl_close($ch);
    //echo $result;

    $mess=json_decode($result);
    foreach ($mess->result as $message)
    {


       $this->data['result'].=
                $message->message->message_id." | "
                .$message->message->from->id." | "
                .$message->message->from->first_name." | "
                .$message->message->text." | "
                .date("d/m/Y H:i:s",$message->message->date)." | "
                .$message->update_id
                ."<br>";
        $last_messsagе=$message->message->text;
        $last_chat_id=$message->message->from->id;
        $last_name=$message->message->from->first_name;

    }
            $message=$last_name." ти написав мені: ".$last_messsagе;
            $url="https://api.telegram.org/bot".$token."/".
            "sendMessage?disable_web_page_preview=true&parse_mode=html&".
            "chat_id=".$last_chat_id."&".
            "text=".$message;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            //curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            $result = curl_exec($ch);
            echo $result."<br><br>";
            curl_close($ch);


        $this->load->view('header', $this->data);
        $this->load->view('test/telegramm', $this->data);
        $this->load->view('bottom', $this->data);


    //return $result ? json_decode($result, true) : false;
    }

    public function call($client, $ext)
    {
        $asterisk = new call();
        $asterisk->makeCall($client,$ext);
    }

    public function vanilla()
    {
        $this->load->view('header', $this->data);
        $this->load->view('test/vanilla', $this->data);
        $this->load->view('bottom', $this->data);


    }

    public function search()
    {
        $this->load->view('header', $this->data);
        $this->load->view('test/autocomplete', $this->data);
        $this->load->view('bottom', $this->data);


    }

}
?>
