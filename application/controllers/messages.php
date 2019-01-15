<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->library('form_validation');

        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('language');
        $this->load->helper('date');

        // Load MongoDB library instead of native db driver if required
        //$this->config->item('use_mongodb', 'ion_auth') ?
        //		$this->load->library('mongo_db') :

        $this->load->model('systema_model');
        $this->load->model('tech_model', 'tech');
        $this->load->model('cartridge_model', 'cartridge');
        $this->load->model('messages_model', 'messages');

        $this->lang->load('ion_auth', 'russian');
        $this->lang->load('systema', 'russian');

        $this->data['userGroups'] = $this->ion_auth->get_users_groups()->result();


    }


    public function index()
    {

        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            $this->data['mess'] = $this->messages->getTasks($this->ion_auth->user()->row()->id);
            $this->load->view('new/header', $this->data);
            $this->load->view('new/messages/main', $this->data);
            $this->load->view('new/footer', $this->data);
        }

    }

    public function viewTasks($userId = false)
    {
        if (!$this->ion_auth->logged_in()) {
            return 'access error';
        } else {
            $userId ? true : $userId = $this->ion_auth->user()->row()->id;
            $tasks = $this->messages->getTasks($userId);

            foreach ($tasks->result() as $task) {
                echo '<div class="col-12">

                        <div class="card bg-light mb-3" id="'.$task->id.'">
                            <div class="card-header" >
                                <div class="row">
                                    <div class="col-6 text-primary">'.$task->subject.'</div>
                                    <div class="col-6 text-small text-info text-right"><span class="badge badge-pill badge-light">'.date('d.m.Y',$task->add_date).'</span>
                                    '. $task->first_name.' '.$task->last_name.'
                                    <button onclick="taskDone('.$task->id.')" title="Виконано" class="btn btn-link btn-sm"><i class="fa fa-check"></i></button>
                                    <button onclick="taskRefresh('.$task->id.')" title="Перенести в кінець" class="btn btn-link btn-sm"><i class="fa fa-refresh"></i></button></div> 
                                </div>
                            </div>
                            <div class="card-body" >
                                <p class="card-text text-small" >'.$task->text.'</p >
                            </div>
                        </div>
                      </div>';
            }

        }

    }

    public function addTask()
    {
        if (!$this->ion_auth->logged_in()) {
            return 'access error';
        } else {
            $data = $this->input->post();
            $data['order_id'] = '';
            $data['uniq_num'] = '';
            $data['stage_code'] = 'task';
            $data['user_id'] = $this->ion_auth->user()->row()->id;
            $data['add_date'] = date('U');
            $data['remind_date'] = '';
            $data['hidden'] = '';
            $data['to_user'] = $this->ion_auth->user()->row()->id;
            $data['isread'] = '';


            $this->messages->add_message($data);
            print_r($data);
        }

    }

    public function taskRefresh($taskId)
    {
        if (!$this->ion_auth->logged_in()) {
            return 'access error';
        } else {
            $this->messages->update($taskId,array('add_date' => date('U')+43000));
        }

    }

    public function taskDone($taskId)
    {
        if (!$this->ion_auth->logged_in()) {
            return 'access error';
        } else {
            $this->messages->update($taskId, array('isread' => 1, 'done_date' => date('U')));

        }

    }

    public function add($order_id, $uniq_num, $stage_code, $value_id = false)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            echo $this->input->post('text');
            //print_r($this->input->post());
            if (strip_tags($this->input->post('text')) != '') {
                $data['order_id'] = $order_id;
                $data['uniq_num'] = $uniq_num;
                $this->input->post('code') ? $data['stage_code'] = $this->input->post('code') : $data['stage_code'] = $stage_code;
                $data['subject'] = $this->input->post('subject');
                $data['text'] = $this->input->post('text');
                $data['user_id'] = $this->ion_auth->user()->row()->id;
                $data['to_user'] = $this->input->post('to_user_id');
                $data['add_date'] = date('U');

                $date = explode('.', $this->input->post('date'));
                $data['remind_date'] = mktime(6, 0, 0, $date[1], $date[0], $date[2]);
            } elseif ($value_id) {
                $data['text'] = $this->db->select('value')
                    ->from('orgs_contacts')
                    ->where('id', $value_id)
                    ->get()->row()->value;

                $data['order_id'] = $order_id;
                $data['uniq_num'] = $uniq_num;
                $data['stage_code'] = $stage_code;

                $data['user_id'] = $this->ion_auth->user()->row()->id;
                $data['add_date'] = date('U');
            } else redirect($_SERVER['HTTP_REFERER'], 'refresh');

            if ($this->messages->add_message($data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
            else echo 'some error';

        }
    }

    public function mess_readed($mess_id)
    {
        $this->messages->update($mess_id, array('isread' => 1));
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function hide_mess($mess_id)
    {
        $this->messages->update($mess_id, array('hidden' => 1));
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function add_org_contact($org_id)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            //print_r($this->input->post());
            if (strip_tags($this->input->post('text')) != '') {
                $data = array('timestamp' => date('U'),
                    'user_id' => $this->ion_auth->user()->row()->id);
                $data['org_id'] = $org_id;
                $data['type'] = $this->input->post('type');
                $data['value'] = $this->input->post('text');
            } else redirect($_SERVER['HTTP_REFERER'], 'refresh');
            if ($this->messages->add_org_contact($data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
            else echo 'some error';
        }
    }

    public function del_org_contact($contact_id)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            if ($contact_id) {
                $data = array('visible' => '0',
                    'timestamp' => date('U'),
                    'user_id' => $this->ion_auth->user()->row()->id);
                if ($this->messages->update_org_contact($contact_id, $data)) redirect($_SERVER['HTTP_REFERER'], 'refresh');
                else echo 'some error';
            }
        }
    }

    public function get_messages()
    {
        $messeg_num = $this->messages->get_user_messages($this->ion_auth->user()->row()->id)->num_rows();
        $mess = $this->messages->get_user_messages($this->ion_auth->user()->row()->id);
        if ($messeg_num == 1) echo '<a href="/messages/read" target="new"><i class="icon-comment"></i> У вас ' . $messeg_num . ' новое сообщение</a>';
        elseif ($messeg_num > 1) echo '<a href="/messages/read" target="new"><i class="icon-comment"></i> У вас ' . $messeg_num . ' новых сообщений</a>';

        else echo '<i class="icon-comment-alt"></i>';
        //echo ' '.$mess->row()->subject;
    }

    public function read()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            $this->data['mess'] = $this->messages->get_user_messages($this->ion_auth->user()->row()->id);
            $this->get_user_menu(anchor('messages/create', lang('menu_messages_create')), 'Сообшения');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('messages/main', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    public function create()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            $this->data['mess'] = $this->messages->get_user_messages($this->ion_auth->user()->row()->id);
            $this->data['users'] = $this->messages->get_active_user();

            $this->get_user_menu(anchor('messages/create', lang('menu_messages_create')), 'Сообшения');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('messages/create', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

}

?>
