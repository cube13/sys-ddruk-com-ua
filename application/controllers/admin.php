<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('systema_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('html');
        // Load MongoDB library instead of native db driver if required
        $this->config->item('use_mongodb', 'ion_auth') ?
            $this->load->library('mongo_db') :
            $this->load->database();
        $this->lang->load('ion_auth', 'russian');
        $this->lang->load('systema', 'russian');
        $this->load->helper('language');
        $this->load->helper('date');

        $this->load->model('systema_model');
        $this->load->model('cartridge_model', 'cartridge');


    }


    private function get_user_menu($usermenu = "", $userhere = "")
    {
        $user = $this->ion_auth->user()->row();
        if ($usermenu)
        {
            $tomain = '';
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach ($user_groups as $group)
            {
                $tomain .= anchor($group->name, lang($group->description)) . " | ";
                if ($group->name == $this->uri->segment(1))
                {
                    $userhere = " - " . lang($group->description);
                }

            }
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . " - " . $userhere;
            $this->data['tomain'] = $tomain;
            //$this->data['tomain']= anchor('main', lang('menu_tomain'));

        }
        else
        {
            $user_groups = $this->ion_auth->get_users_groups()->result();
            $usermenu='<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">ДД</a>
    <ul class="nav">
      <li class="active"><a href="#">Home</a></li>
      ';
            foreach ($user_groups as $group)
            {

                $usermenu .= '<li>'.anchor($group->name, lang($group->description)) . "</li>";
                if ($group->name == $this->uri->segment(1))
                {
                    $userhere = " - " . lang($group->description);
                }

            }
            $usermenu.='
    </ul>
  </div>
</div>';
            $this->data['tomain'] = "";
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . $userhere;


        }
    }

    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->get_user_menu(anchor('admin/create_user', lang('menu_create_user'))
                . ' | ' . anchor('admin/groups', lang('menu_groups'))
                . ' | ' . anchor('admin/stages', lang('menu_stages'))
                . ' | ' . anchor('admin/tech_types', lang('menu_techtypes'))
                , lang('menu_users'));

            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }
            $this->data['groups'] = $this->ion_auth->groups()->result();
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('users', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }



    public function set_settings()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            foreach ($this->input->post() as $param => $value) {
                $data = array('value' => $value);
                $this->systema_model->set_settings($data, $param);
            }
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

    }

    public function update_site_prices($brand_id)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            $cartridges = $this->cartridge->view($brand_id);
            $kurs_usd_nal = $this->systema_model->settings('kurs_usd_nal')->row()->value;
            foreach ($cartridges->result() as $cart) {
                if (!$cart->refill_usd) {
                    $cena_zapravki = $cart->refill_usd;
                } elseif (($cart->refill_usd * $kurs_usd_nal) % 10 == 9) {
                    $cena_zapravki = (int)$cart->refill_usd * $kurs_usd_nal;
                } elseif (($cart->refill_usd * $kurs_usd_nal) % 10 < 5) {
                    $cena_zapravki = (int)$cart->refill_usd * $kurs_usd_nal - ((int)$cart->refill_usd * $kurs_usd_nal) % 10 - 1;
                } elseif (($cart->refill_usd * $kurs_usd_nal) % 10 >= 5) {
                    $cena_zapravki = (int)$cart->refill_usd * $kurs_usd_nal - ((int)$cart->refill_usd * $kurs_usd_nal) % 10 + 9;
                }

                if (!$cart->recikl_usd) {
                    $cena_vostanovlenia = $cart->recikl_usd;
                } elseif (($cart->recikl_usd * $kurs_usd_nal) % 10 == 9) {
                    $cena_vostanovlenia = (int)$cart->recikl_usd * $kurs_usd_nal;
                } elseif (($cart->recikl_usd * $kurs_usd_nal) % 10 < 5) {
                    $cena_vostanovlenia = (int)$cart->recikl_usd * $kurs_usd_nal - ((int)$cart->recikl_usd * $kurs_usd_nal) % 10 - 1;
                } elseif (($cart->recikl_usd * $kurs_usd_nal) % 10 >= 5) {
                    $cena_vostanovlenia = (int)$cart->recikl_usd * $kurs_usd_nal - ((int)$cart->recikl_usd * $kurs_usd_nal) % 10 + 9;
                }
                $data = array('cena_zapravki' => $cena_zapravki, 'cena_vostanovlenia' => $cena_vostanovlenia);
                $this->cartridge->update_cartridge($data, $cart->id);
            }
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

    }


    public function groups()
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } elseif ($this->ion_auth->is_admin()) {
            $this->get_user_menu(anchor('admin', lang('menu_admin'))
                , 'Групи');

            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));


            $this->data['groups'] = $this->ion_auth->groups()->result();

            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['groupname'] = array('name' => 'groupname',
                'id' => 'groupname',
                'type' => 'text',
                'value' => $this->form_validation->set_value('groupname'),
            );
            $this->data['description'] = array('name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'value' => $this->form_validation->set_value('description'),
            );

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('groups', $this->data);
            $this->load->view('create_group', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    public function create_group()
    {
        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Створення групи');

        //проверим ввод данных
        $this->form_validation->set_rules('groupname', 'Група', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Опис', 'required|xss_clean');


        if ($this->form_validation->run() == true) {
            $groupname = strtolower($this->input->post('groupname'));
            $description = $this->input->post('description');

        }
        if ($this->form_validation->run() == true && $this->systema_model->create_group($groupname, $description)) {
            $this->session->set_flashdata('message', "Група створена");
            redirect("admin/groups", 'refresh');
        } else {
            //отображаем форму для создания группы
            //отправляем ссобщения об ошибках если они есть

            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['groupname'] = array('name' => 'groupname',
                'id' => 'groupname',
                'type' => 'text',
                'value' => $this->form_validation->set_value('groupname'),
            );
            $this->data['description'] = array('name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'value' => $this->form_validation->set_value('description'),
            );


            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('create_group', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    //Змінити дані групи
    public function update_group($groupid, $act_flag = 0)
    {

        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Групи - параметри групи');
        $this->data['message'] = '';
        if (!$act_flag) {
            $user = $this->ion_auth->group($groupid)->row();

            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'value' => $user->name,
            );
            $this->data['description'] = array('name' => 'description',
                'id' => 'description',
                'type' => 'text',
                'value' => $user->description,
            );

            $this->data['groupid'] = $groupid;
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('update_group', $this->data);
            $this->load->view('bottom', $this->data);
        }

        if ($act_flag) {

            //перевіряемо введені данні
            $this->form_validation->set_rules('name', 'Имя', 'required|xss_clean');
            $this->form_validation->set_rules('description', 'Описание', 'required|xss_clean');


            if ($this->form_validation->run() == true) {
                $groupdata = array(
                    'name' => strtolower($this->input->post('name')),
                    'description' => strtolower($this->input->post('description'))
                );
            }
            if ($this->form_validation->run() == true && $this->systema_model->group_update($groupid, $groupdata)) {
                $this->session->set_flashdata('message', "Дані про групу змінено");
                redirect("admin/groups", 'refresh');
            } else {
                //отображаем форму для изменения группы
                //отправляем ссобщения об ошибках если они есть
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->data['name'] = array('name' => 'name',
                    'id' => 'name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('name'),
                );
                $this->data['description'] = array('name' => 'description',
                    'id' => 'description',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('description'),
                );

                $this->data['groupid'] = $groupid;


                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('update_group', $this->data);
                $this->load->view('bottom', $this->data);
            }
        }

    }

    public function up_group($id)
    {
        $this->systema_model->up_group($id);
        redirect('admin/groups', 'refresh');
    }

    public function down_group($id)
    {
        $this->systema_model->down_group($id);
        redirect('admin/groups', 'refresh');
    }

    //додати користувача в групу
    public function add_to_group($user_id, $group_id)
    {

        if ($this->ion_auth->add_to_group($group_id, $user_id)) redirect('admin', 'refresh');
        else echo "fail!";
    }

    //видалити користувача з группи
    public function remove_from_group($user_id, $group_id)
    {

        if ($this->ion_auth->remove_from_group($group_id, $user_id)) redirect('admin', 'refresh');
        else echo "fail!";
    }

    public function stages()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } elseif ($this->ion_auth->is_admin()) {
            $this->get_user_menu(anchor('admin', lang('menu_admin'))
                , 'Етапи робіт');

            $this->data['stages_cartridge'] = $this->systema_model->view_stages('cartridge')->result();
            $this->data['stages_cartridge_table'] = 'cartridge';

            $this->data['stages_tech'] = $this->systema_model->view_stages('tech')->result();
            $this->data['stages_tech_table'] = 'tech';

            $this->data['stages_order'] = $this->systema_model->view_stages('order')->result();
            $this->data['stages_order_table'] = 'order';

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('stages', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    public function stage_up($table, $id)
    {
        $this->systema_model->up_stage($table, $id);
        redirect('admin/stages', 'refresh');
    }

    public function stage_down($table, $id)
    {
        $this->systema_model->down_stage($table, $id);
        redirect('admin/stages', 'refresh');
    }

    //отображение типов техники (принтеры, мфу, компьютеы, мониторы, другое)
    public function tech_types()
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } elseif ($this->ion_auth->is_admin()) {
            $this->get_user_menu(anchor('admin', lang('menu_admin'))
                , 'Групи');


            $this->data['techtypes'] = $this->systema_model->techtypes()->result();

            $this->data['name'] = array('name' => 'name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('name'),
            );

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('admin/techtypes', $this->data);
            $this->load->view('admin/create_techtypes', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    public function create_techtype()
    {
        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Добаление типа');

        //проверим ввод данных
        $this->form_validation->set_rules('name', 'Тип', 'required|xss_clean');


        if ($this->form_validation->run() == true && $this->systema_model->create_techtype($this->input->post('name'))) {
            $this->session->set_flashdata('message', "Тип добавлен");
            redirect("admin/tech_types", 'refresh');
        } else {
            //отображаем форму для создания группы
            //отправляем ссобщения об ошибках если они есть
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $this->data['name'] = array('name' => 'name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('name'),
            );

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('admin/create_techtype', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    //Переименование типа
    public function update_techtype($typeid, $act_flag = 0)
    {

        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Групи - параметри групи');
        $this->data['message'] = '';
        if (!$act_flag) {
            $techtypes = $this->systema_model->techtypes($typeid)->row();

            $this->data['name'] = array('name' => 'name',
                'id' => 'name',
                'type' => 'text',
                'value' => $techtypes->name,
            );
            $this->data['typeid'] = $typeid;
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('admin/update_techtype', $this->data);
            $this->load->view('bottom', $this->data);
        }

        if ($act_flag) {
            //перевіряемо введені данні
            $this->form_validation->set_rules('name', 'Имя', 'required|xss_clean');

            if ($this->form_validation->run() == true) {
                $typedata = array('name' => $this->input->post('name'));
            }
            if ($this->form_validation->run() == true && $this->systema_model->techtype_update($typeid, $typedata)) {
                $this->session->set_flashdata('message', "Наименование типа ");
                redirect("admin/tech_types", 'refresh');
            } else {
                //отображаем форму для изменения группы
                //отправляем ссобщения об ошибках если они есть
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->data['name'] = array('name' => 'name',
                    'id' => 'name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('name'),
                );
                $this->data['description'] = array('name' => 'description',
                    'id' => 'description',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('description'),
                );

                $this->data['groupid'] = $groupid;


                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('update_group', $this->data);
                $this->load->view('bottom', $this->data);
            }
        }

    }


    //Створити нового користувача
    public function create_user()
    {
        if ($this->ion_auth->is_admin() && $this->ion_auth->logged_in()) {
            $this->get_user_menu(anchor('admin', lang('menu_admin'))
                , 'Пользователи и доступ - Создание пользователя');

            //проверим ввод данных
            $this->form_validation->set_rules('first_name', 'Имя', 'required|xss_clean');
            $this->form_validation->set_rules('last_name', 'Фамилия', 'required|xss_clean');
            $this->form_validation->set_rules('login', 'Логин', 'required|xss_clean');
            $this->form_validation->set_rules('email', 'Е-мейл', 'required|valid_email');
            $this->form_validation->set_rules('phone', 'Телефон', 'xss_clean');
            $this->form_validation->set_rules('company', 'Имя компании', 'required|xss_clean');
            $this->form_validation->set_rules('password', 'Пароль', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', 'Подтверждение пароля', 'required');

            if ($this->form_validation->run() == true) {
                $username = strtolower($this->input->post('login'));
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                $additional_data = array('first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone')
                );
            }
            if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {
                $this->session->set_flashdata('message', "Пользователь добавлен");
                redirect("admin", 'refresh');
            } else {
                //отображаем форму для создания пользователя
                //отправляем ссобщения об ошибках если они есть

                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->data['first_name'] = array('name' => 'first_name',
                    'id' => 'first_name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('first_name')
                );
                $this->data['last_name'] = array('name' => 'last_name',
                    'id' => 'last_name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('last_name')
                );
                $this->data['email'] = array('name' => 'email',
                    'id' => 'email',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('email')
                );
                $this->data['company'] = array('name' => 'company',
                    'id' => 'company',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('company')
                );
                $this->data['phone'] = array('name' => 'phone',
                    'id' => 'phone',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('phone')
                );
                $this->data['login'] = array('name' => 'login',
                    'id' => 'login',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('login')
                );
                $this->data['password'] = array('name' => 'password',
                    'id' => 'password',
                    'type' => 'password',
                    'value' => $this->form_validation->set_value('password')
                );
                $this->data['password_confirm'] = array('name' => 'password_confirm',
                    'id' => 'password_confirm',
                    'type' => 'password',
                    'value' => $this->form_validation->set_value('password_confirm')
                );

                $date_start = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $date_end = mktime(23, 59, 59, date('m') + 1, 0, date('Y'));


                $this->data['count_cart'] = $this->cartridge->get_count_cart_stage('todsp', $date_start, $date_end, $userid, 0, 0)->row();
                $this->data['inrfl_cart'] = $this->cartridge->get_count_cart_stage('inrfl', $date_start, $date_end, $userid, 0, 0)->row();

                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('create_user', $this->data);
                $this->load->view('bottom', $this->data);
            }
        } else {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
    }

    //Змінити дані про користувача
    public function update_user($userid, $act_flag = 0)
    {
        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Користувачі та доступ - Зміна даних користувача');
        $this->data['message'] = '';
        if (!$act_flag) {
            $user = $this->ion_auth->user($userid)->row();

            $this->data['first_name'] = array('name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'value' => $user->first_name,
            );
            $this->data['last_name'] = array('name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'value' => $user->last_name,
            );
            $this->data['email'] = array('name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $user->email,
            );
            $this->data['company'] = array('name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $user->company,
            );
            $this->data['phone'] = array('name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'value' => $user->phone,
            );
            $this->data['login'] = array('name' => 'login',
                'id' => 'login',
                'type' => 'text',
                'value' => $user->username,
            );
            $this->data['userid'] = $user->id;

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('update_user', $this->data);
            $this->load->view('bottom', $this->data);
        }

        if ($act_flag) {

            //проверим ввод данных
            $this->form_validation->set_rules('first_name', 'Имя', 'required|xss_clean');
            $this->form_validation->set_rules('last_name', 'Фамилия', 'required|xss_clean');
            $this->form_validation->set_rules('login', 'Логин', 'required|xss_clean');
            $this->form_validation->set_rules('email', 'Е-мейл', 'required|valid_email');
            $this->form_validation->set_rules('phone', 'Телефон', 'xss_clean');
            $this->form_validation->set_rules('company', 'Имя компании', 'required|xss_clean');

            if ($this->form_validation->run() == true) {
                $userdata = array(
                    'username' => strtolower($this->input->post('login')),
                    'email' => $this->input->post('email'),
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone')
                );
            }
            if ($this->form_validation->run() == true && $this->ion_auth->update($userid, $userdata)) {
                $this->session->set_flashdata('message', "Дані про користувача змінено");
                redirect("admin", 'refresh');
            } else {
                //отображаем форму для изменения пользователя
                //отправляем ссобщения об ошибках если они есть
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $this->data['first_name'] = array('name' => 'first_name',
                    'id' => 'first_name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('first_name'),
                );
                $this->data['last_name'] = array('name' => 'last_name',
                    'id' => 'last_name',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('last_name'),
                );
                $this->data['email'] = array('name' => 'email',
                    'id' => 'email',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('email'),
                );
                $this->data['company'] = array('name' => 'company',
                    'id' => 'company',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('company'),
                );
                $this->data['phone'] = array('name' => 'phone',
                    'id' => 'phone',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('phone'),
                );
                $this->data['login'] = array('name' => 'login',
                    'id' => 'login',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('login'),
                );


                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('update_user', $this->data);
                $this->load->view('bottom', $this->data);
            }
        }

    }

    public function update_user_passwd($userid)
    {
        $this->get_user_menu(anchor('admin', lang('menu_admin'))
            , 'Користувачі та доступ - Зміна пароля користувача');
        $this->data['message'] = '';
        //проверим ввод данных
        $this->form_validation->set_rules('password', 'Пароль', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', 'Подтверждение пароля', 'required');

        if ($this->form_validation->run() == true) {
            $userdata = array(
                'password' => $this->input->post('password')
            );
        }
        if ($this->form_validation->run() == true && $this->ion_auth->update($userid, $userdata)) {
            $this->session->set_flashdata('message', "Пароль користувача змінено");
            redirect("admin", 'refresh');
        } else {
            //отображаем форму для изменения пользователя
            //отправляем ссобщения об ошибках если они есть
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array('name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->data['userid'] = $userid;


            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('update_user_passwd', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }


    //Активации пользователя
    function activate_user($id, $code = false)
    {
        if ($code !== false)
            $activation = $this->ion_auth->activate($id, $code);
        else if ($this->ion_auth->is_admin())
            $activation = $this->ion_auth->activate($id);

        if ($activation) {
            //redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("admin", 'refresh');
        } else {
            //redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("admin", 'refresh');
        }
    }

    //Деактивация пользователя
    public function deactivate_user($id = NULL)
    {
        $this->get_user_menu(anchor('admin', lang('menu_admin')), 'Пользователи и доступ - Деактивация пользователя');

        $id = $this->config->item('use_mongodb', 'ion_auth') ? (string)$id : (int)$id;

        $this->form_validation->set_rules('confirm', 'confirmation', 'required');
        $this->form_validation->set_rules('id', 'user ID', 'required|alpha_numeric');

        if ($this->form_validation->run() == FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('deactivate_user', $this->data);
            $this->load->view('bottom', $this->data);
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    show_404();
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('admin', 'refresh');
        }
    }


    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


}

?>