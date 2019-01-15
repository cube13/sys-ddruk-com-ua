<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller
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
        $this->lang->load('systema', 'russian');

        $this->load->model('cartridge_model', 'cartridge');

        $this->load->helper('language');
        $this->load->helper('date');

        $this->data['userGroups'] = $this->ion_auth->get_users_groups()->result();

        $this->data['couriers'] = array(
            34 => 'Нікіта',
            39 => 'Олександр',
            41 => 'Михаил',
            42 => 'Георгий',
            31 => 'Богдан',
            35 => 'Олексій',
            36 => 'Андрій',
            24 => 'Григорій',
            37 => 'Євген',
            38 => 'Володимир',
            14 => 'Маша',
            32 => 'Дмитро',
            0 => 'ВСІ'
            //33 => 'Юля',
            //  1 => 'Швайко',
            //   2 => 'TEST',

        );

    }

    /*private function get_user_menu($usermenu = "", $userhere = "")
    {
        $user = $this->ion_auth->user()->row();
        if ($usermenu) {
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . " - " . $userhere;
            $this->data['tomain'] = anchor('main', lang('menu_tomain'));
        } else {
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach ($user_groups as $group) {
                $usermenu .= '<span class="alert-info">' . anchor($group->name, lang($group->description)) . '</span>' . nbs(2);

                if ($group->name == $this->uri->segment(1)) {
                    $userhere = " - " . lang($group->description);
                }
            }
            $this->data['tomain'] = "";
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . $userhere;
        }
    }*/

    public function index()
    {

        if (!$this->ion_auth->logged_in())
        {
            redirect('main/login', 'refresh');
        }
        else
        {
            $this->data['searchBox']='<!-- Search Box-->
            <div class="search-box">
                <button class="dismiss"><i class="icon-close"></i></button>
                <form id="searchForm" action="javascript:void(null)" role="search">
                    <input type="search" placeholder="Шукаєте щось?..." class="form-control" onchange="">
                </form>
            </div>';

            if($this->ion_auth->is_admin())
            {

                //$this->data['stage_code'] = 'tofcdl';
                $this->data['user_id'] = $this->ion_auth->user()->row()->id;


                $this->load->view('new/header', $this->data);
                $this->load->view('new/main', $this->data);
                $this->load->view('new/footer', $this->data);
            }

            else
            {
                //redirect('orders/', 'refresh');

                $this->data['user_id'] = $this->ion_auth->user()->row()->id;
                $this->load->view('new/header', $this->data);
                $this->load->view('new/main', $this->data);
                $this->load->view('new/footer', $this->data);


            }
        }
    }

    function login()
    {

        //validate form input
        $this->form_validation->set_rules('login', 'Логин', 'required');
        $this->form_validation->set_rules('password', 'Пароль', 'required');

        if ($this->form_validation->run() == true)
        { //check to see if the user is logging in
            //check for "remember me"
            $remember = (bool)$this->input->post('remember');

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

            $this->data['login'] = array(
                'name' => 'login',
                'id' => 'login',
                'type' => 'text',
                'value' => $this->form_validation->set_value('login'),
                'class'=>'input-material',
                'required'=>''

            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class'=>'input-material',
                'required'=>''
            );

            $this->data['title'] = lang('global_login');

             //   $this->load->view('header', $this->data);
                $this->load->view('new/login', $this->data);
             //   $this->load->view('bottom', $this->data);
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

    public function phpinfo()
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            phpinfo();
        }


    }


}

?>
