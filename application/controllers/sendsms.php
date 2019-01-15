<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sendsms extends CI_Controller
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
        $this->load->model('messages_model', 'messages');
        $this->load->model('smsclient_model');


    }

    public function index()
    {
        echo 'index';

    }

    public function getbalance()
    {
        echo $this->smsclient_model->get_balance();
        //echo $this->smsclient_model->hasErrors();
    }

    public function haserrors()
    {
        echo $this->smsclient_model->hasErrors();
    }


    public function send($order_hash = false)
    {
        $sms_id = 0;

        if ($order_hash) {
            $order = $this->systema_model->view_order($order_hash)->row();

            $contacts = $this->messages->to_order($order->id, $order->id, 'contact');
            foreach ($contacts->result() as $contact) {
                if ($contact->stage_code == 'contact-mob') $mob = $contact->text;
            }

            $this->data['phonemob'] = array('name' => 'phonemob',
                'id' => 'phonemob',
                'type' => 'text',
                'value' => $mob,
                'size' => '17'
            );
            $this->data['message'] = array('name' => 'message',
                'id' => 'message',
                'type' => 'text',
                'value' => 'Ваш заказ №' . $order->id . ' готов. Добрий Друк',
                'size' => '60'
            );
            $this->data['orderid'] = array('name' => 'orderid',
                'value' => $order->id,
                'type' => 'hidden'
            );
            $this->data['title'] = 'Отправка СМС';
            $this->load->view('header', $this->data);
            $this->load->view('sms/sendform', $this->data);
            $this->load->view('bottom', $this->data);
            echo $order->id;
        }

        if ($this->input->post('message') && $this->input->post('phonemob')) {
            echo $this->input->post('phonemob');
            $sms_id = $this->smsclient_model->sendSMS('DobriyDruk', $this->input->post('phonemob'), $this->input->post('message'));
            echo $sms_id;
        }
        if ($sms_id) {
            $this->smsclient_model->smslog($sms_id, $this->input->post('orderid'), $this->ion_auth->user()->row()->id);
            redirect("sendsms/getstatus/" . $sms_id, 'refresh');
        }

    }

    public function getstatus($sms_id)
    {

        $this->data['title'] = 'Проверка статуса СМС';
        $this->data['sms_status'] = $this->smsclient_model->receiveSMS($sms_id);
        $this->data['balance'] = $this->smsclient_model->get_balance();

        $this->load->view('header', $this->data);
        $this->load->view('sms/status', $this->data);
        $this->load->view('bottom', $this->data);
        if ($this->data['sms_status'] == 'Доставлено') {
            $this->smsclient_model->smslog_update($sms_id, 3);
        } else {
            sleep(10);
            redirect("sendsms/getstatus/" . $sms_id, 'refresh');

        }

    }
}

?>