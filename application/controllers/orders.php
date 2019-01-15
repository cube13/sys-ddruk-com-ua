<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller
{
    function __construct()
    {
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
        $this->load->model('systema_fin_model', 'fin');
        $this->load->model('cartridge_model', 'cartridge');
        $this->load->model('messages_model', 'messages');
        $this->load->model('orders_model', 'orders');

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
            32 => 'Дмитро'
            //33 => 'Юля',
            //  1 => 'Швайко',
            //   2 => 'TEST',

        );

        $this->data['managers'] = array(
            32 => 'Дмитро',
            33 => 'Юля',
            14 => 'Маша',
            1 => 'Швайко',
            2 => 'TEST',
        );



    }

    private function get_user_menu($usermenu = "", $userhere = "")
    {
        $user = $this->ion_auth->user()->row();
        if ($usermenu) {
            $tomain = '';
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach ($user_groups as $group) {
                $tomain .= anchor($group->name, lang($group->description)) . nbs(2);
                if ($group->name == $this->uri->segment(1)) {
                    $userhere = " - " . lang($group->description);
                }
            }
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . " - " . $userhere;
            $this->data['tomain'] = $tomain;
            //$this->data['tomain']= anchor('main', lang('menu_tomain'));    
        } else {
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach ($user_groups as $group) {
                $usermenu .= anchor($group->name, lang($group->description)) . " | ";
                if ($group->name == $this->uri->segment(1)) {
                    $userhere = " - " . lang($group->description);
                }
            }
            $this->data['tomain'] = "";
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . $userhere;


        }
    }

    public function index($isNew)
    {

        //$this->ion_auth->is_admin() ? $isNew=0 : $isNew=1;
$isNew=1;

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->data['orders'] = $this->systema_model->view_orders_table();
            $this->data['stages_menu'] = "";
            $stages = $this->systema_model->get_stages('stages_order')->result();
            $this->data['searchBox']='<!-- Search Box-->
            <div class="search-box">
                <button class="dismiss"><i class="icon-close"></i></button>
                <form id="searchForm" action="javascript:void(null)" role="search">
                    <input type="search" placeholder="Введіть номер замовлення..." class="form-control" onchange="SearchOrder(this.value)">
                </form>
            </div>';
            foreach ($stages as $stage) {
                $this->data['stages_menu'] .= '<' . anchor('orders/stage/' . $stage->code, $stage->name) . '> ';
            }
            $this->get_user_menu(anchor_popup('orders/quick_create', lang('menu_orders_create')) .
                ' | ' . anchor('/orders/search/', 'Поиск заказов'), 'Керування замовленнями');

            if (($this->ion_auth->users()->row()->id == 33 || $this->ion_auth->users()->row()->id == 1) && $isNew == 1)
            {


                // $this->data['orders1'] = $this->orders->GetOrders();
                $this->load->view('new/header', $this->data);
                $this->load->view('new/orders/main', $this->data);
                $this->load->view('new/footer', $this->data);

            } else {
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('orders/main_d', $this->data);
                $this->load->view('bottom', $this->data);
            }


        }
    }

    public function GetOrders($userId = false, $orderId=false, $isClosed=false)
    {
        $toScreen ="";
        $orders = $this->orders->GetOrders($userId, $orderId, $isClosed);
        foreach ($orders->result() as $order) {
            $stageFilter="";
            $stageToOffice = $this->orders->GetStage($order->id, 'tofcdl')->row();
            $stageIsPayd = $this->orders->GetStage($order->id, 'ispayd')->row();
            $stageDocs = $this->orders->GetStage($order->id, 'pprdoc')->row();
            $stageToClient = $this->orders->GetStage($order->id, 'toclnt')->row();
            $startDiv='<div class="project ">';


            $cartAll = $this->cartridge->get_stage_simple('inofc', 1, $order->id)->row()->count;
            $cartRefiled = $this->cartridge->get_stage_simple('inrfl', 1, $order->id)->row()->count;
            $cartPacked = $this->cartridge->get_stage_simple('topck', 1, $order->id)->row()->count;

            $cartToApprove = $this->cartridge->get_stage_simple('apprv', 0, $order->id)->row()->count;

            $cartAll ? $cartProgressR = ($cartRefiled / $cartAll * 100) : $cartProgressR = 0;
            $cartAll ? $cartProgressP = ($cartPacked / $cartAll * 100) : $cartProgressP = 0;


            if($stageToOffice->date_end)
            {
                if($stageToClient->date_end)
                {

                }
                else
                {
                    $stageFilter=" toclnt";
                }

            }
            else
            {
                $stageFilter=" tofcdl";
            }



            if($stageDocs->date_end)
            {
                $docsInfo = '<span class="text-success">Документи підготовлено</span>';
                //  $startDiv='<div class="project" id="docsDone">';
            }
            else
            {
                    $docsInfo = '<span class="text-danger">Підготувати документи</span>';
                    if($stageToOffice->date_end) $stageFilter=" pprdoc";
                    else ;

            }

            if($stageIsPayd->date_end)
            {
                $isPaydInfo = '<span class="text-success">Є оплата</span>';
                // $startDiv='<div class="project" id="payDone">';
            }
            else
            {
                $isPaydInfo = '<span class="text-danger">Очікує оплати</span>';
                // $startDiv='<div class="project" id="payNeed">';
            }
            //echo $stageFilter;

            $toScreen.='<div class="project '.$stageFilter.'">';
            $toScreen.='<div class="row bg-white has-shadow">
                    <div class="left-col col-lg-2 d-flex align-items-center justify-content-between">
                        <div class="project-title d-flex align-items-center">
                            <div class="text">
                                <h3 class="h3">' . anchor_popup('orders/view_order/' . $order->hash, $order->id) . '</h3>
                                <small>' . $order->org_name . ' </small>
                            </div>
                        </div>
                    </div>
                    <div class="left-col col-lg-2 d-flex align-items-center justify-content-between">
                        <div class="time">
                            <small>
                                ' . date('d.m.y о H:i', $order->date_create) . '
                                <br>
                                ' . $order->last_name . '
                            </small>
                        </div>
                    </div>';

            if ($stageToOffice->date_end) {
                if($cartRefiled && ($cartPacked/$cartRefiled)==1 && $stageDocs->date_end && !$stageToClient->date_end)
                {
                    $toScreen .= '
                    <div class="right-col col-lg-4 d-flex align-items-center">
                        <div class="comments"><small><i class="fa fa-car"></i>Доставка <b>' . $stageToClient->first_name . '</b> 
                         <br><i class="fa fa-clock-o"></i> ' .
                        date('d.m на H:i', $stageToClient->date_start) . ' </small></div>
                        
                    </div>'
                    ;

                }
                elseif($cartRefiled && ($cartPacked/$cartRefiled)==1 && $stageDocs->date_end && $stageToClient->date_end)
                {
                    $toScreen .= '
                    <div class="right-col col-lg-4 d-flex align-items-center">
                        <div class="comments"><small><i class="fa fa-car"></i>Доставленно <b>' . $stageToClient->first_name . '</b> 
                         <br><i class="fa fa-clock-o"></i> ' .
                        date('d.m о H:i', $stageToClient->date_end) . ' </small></div>
                        
                    </div>'
                    ;

                }
                else
                {
                   if($cartToApprove) $toScreen .= '<div class="right-col col-lg-2 d-flex align-items-center bg-warning">';
                   else $toScreen .= '<div class="right-col col-lg-2 d-flex align-items-center">';
                    $toScreen .= '<div class="comments no-margin"><small><i class="fa fa-recycle"></i></small></div>
                        <div class="comments "><small>' . $cartRefiled . '/' . $cartAll . '</small></div>
                        <div class="project-progress no-margin">
                            <div class="progress">
                                <div role="progressbar" style="width:' . $cartProgressR . '%; height: 6px;" aria-valuenow="' . $cartProgressR . '" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-red"></div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="right-col col-lg-2 d-flex align-items-center">
                        <div class="comments no-margin"><small>';

                    if($cartRefiled && ($cartPacked/$cartRefiled)!=1) $toScreen .= anchor_popup('cartridges/to_pack/' . $order->id, '<i class="fa fa-boxes"></i></a>');
                    else $toScreen .= '<i class="fa fa-boxes"></i></a>';
                    $toScreen .= '</small></div>
                        <div class="comments"><small>' . $cartPacked . '/' . $cartRefiled . '</small></div>
                        <div class="project-progress no-margin">
                            <div class="progress">
                                <div role="progressbar" style="width:' . $cartProgressP . '%; height: 6px;" aria-valuenow="' . $cartProgressP . '" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-red"></div>
                            </div>
                        </div>
                    </div>';

                }

            } elseif (!$stageToOffice->date_end && !$stageToOffice->action_flag) {
                switch ($stageToOffice->info) {
                    case 'himself':
                        $toScreen .= '
                    <div class="right-col col-lg-2 d-flex align-items-center">
                        <div class="comments"><small><i class="fa fa-car"></i>Самовивіз </small></div>
                    </div>';
                        break;

                    case 'courier':
                        $toScreen .= '
                    <div class="right-col col-lg-2 d-flex align-items-center">
                        <div class="comments"><small><i class="fa fa-car"></i>Виїзд <b>' . $stageToOffice->first_name . '</b> 
                         <br><i class="fa fa-clock-o"></i> ' .
                            date('d.m на H:i', $stageToOffice->date_start) . ' </small></div>
                        
                    </div>';
                        break;

                    default:

                }

            } elseif (!$stageToOffice->date_end && $stageToOffice->action_flag) {
                $toScreen .= '
                    <div class="right-col col-lg-2 d-flex align-items-center">
                        <div class="comments"><small><i class="fa fa-car"></i>Кур\'єр (<b>' . $stageToOffice->first_name . '</b>) виїхав <br><i class="fa fa-clock-o"></i> ' .
                    date('d.m о H:i', $stageToOffice->date_start) . '</small></div>
                        
                    </div>';
            }

            if ($stageToOffice->date_end) {

                $toScreen .= '
                <div class="right-col col-lg-2 d-flex align-items-center comments">' . $docsInfo . '</div>
                <div class="right-col col-lg-1 d-flex align-items-center comments">' . $isPaydInfo . '</div>';
            }





            $toScreen .='</div></div>';
        }
        echo $toScreen;

    }

    public function quick_create()
    {
        if ($this->ion_auth->logged_in()) {

            $hash = md5(rand(1000000, 999999999) . date('U'));
            $order_data = array(
                'org_name' => 'Приватна особа',
                'manager_id' => $this->ion_auth->user()->row()->id,
                'org_id' => 572,
                'date_create' => date('U'),
                'hash' => $hash,
                'paymethod' => 'nal'
            );
            $delivery_date = date('U');

            $this->systema_model->order_create($order_data);
            $this->systema_model->create_stage_order($hash, 'order', 'tofcdl', $delivery_date, $this->ion_auth->user()->row()->id, 'himself');

            $stages = $this->systema_model->get_stages('stages_order')->result();
            foreach ($stages as $stage) {
                if ($stage->code != 'tofcdl') {
                    $this->systema_model->create_stage_order($hash, 'order', $stage->code, 0,
                        $this->ion_auth->user()->row()->id, 'himself');

                }
            }
            echo $hash;
            $this_order = $this->systema_model->view_order($hash);
            $this_order_id = $this_order->row()->id;
            $this_order_org_name = $this_order->row()->short_name;
            /*$Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
            print_r($Slack->call('chat.postMessage', array(
                        'channel' => '#orders',
                        //'as_user'=>'true',
                        //'username'=>'U0AM6U5T5',
                        'text' => 'Создан новый заказ №'.$this_order_id
                        )));*/
            redirect("orders/view_order/" . $hash, 'refresh');


        } else {
            redirect('main/login', 'refresh');
        }

    }


    //отказ от заказа
    public function refusing($order_id)
    {
        $stages = $this->systema_model->view_stages('order');
        //закрытие всех этапов заказа с признаком refusing
        foreach ($stages->result() as $stage) {
            $this->systema_model->update_stage_orders($stage->code, $order_id, 1, date('U'), date('U'), 'refusing');
        }
        redirect('orders', 'refresh');
    }

    // просмотр заказа
    public function view_order($hash = FALSE)
    {
        if ($this->ion_auth->logged_in()) {
            $this->get_user_menu(' ');

            $this->data['stages_menu'] = "";
            $this->data['is_payd'] = $is_payd = FALSE;
            $stages = $this->systema_model->get_stages('stages_order')->result();
            foreach ($stages as $stage) {
                $this->data['stages_menu'] .= '<' . anchor('orders/stage/' . $stage->code, $stage->name) . '> ';
            }

            $thisorder = $this->data['order'] = $this->systema_model->view_order($hash)->row();
            // print_r($thisorder);
            $adres = array('name' => 'adres',
                'id' => 'adres',
                'type' => 'text',
                'size' => '37'
            );
            $contacter = array('name' => 'contacter',
                'id' => 'contacter',
                'type' => 'text',
                'size' => '37'
            );
            $phone = array('name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'size' => '37'
            );
            $phonemob = array('name' => 'phonemob',
                'id' => 'phonemob',
                'type' => 'text',
                'size' => '37'
            );
            $other_info = array('name' => 'other_info',
                'id' => 'other_info',
                'type' => 'text',
                'size' => '37'
            );

            $this->data['org_name'] = array('name' => 'org_name',
                'id' => 'class_activity',
                'type' => 'text',
                'onkeyup' => 'autosuggest(this.value);',
                'value' => $this->form_validation->set_value('org_name'),
                'autocomplete' => 'off',
                'placeholder' => 'переместить (сменить организацию)',
                'class' => 'span3'
            );

            $delivery = $this->systema_model->get_stage($thisorder->id, 'tofcdl')->row();
            $whos = '';
            $who = '';
            $act_flag = 0;
            $act = '';
            $do = '';
            $delivery_adres = '';

            switch ($delivery->info) {
                case 'courier':
                    $who = "<b><u>" . $delivery->first_name . "</u></b>";
                    $whos = "<b><u>" . $delivery->first_name . "</u></b>";
                    $act_flag = TRUE;
                    break;
                case 'master':
                    $who = "<b><u>" . $delivery->manager_id . "</u></b>";
                    $whos = "<b><u>" . $delivery->manager_id . "</u></b>";
                    $act_flag = TRUE;
                    break;
                case 'himself':
                    $who = 'Самовывоз';
                    $act_flag = FALSE;
                    break;
                default :
                    $who = '';
            }

            if ($delivery->action_flag && $act_flag) {
                if (!$delivery->date_end) {
                    $act = $who . ' выехал ' . date('d.m.Y в H:i', $delivery->date_start);
                    $do = ' ' . anchor('orders/close_stage/tofcdl/' . $thisorder->id, $who . ' вернулся') . ' ';
                }
                if ($delivery->date_end) {
                    $act = '<br> Приехал ' . date('d.m.Y в H:i', $delivery->date_end);
                    $do = '';
                }
            }
            $this->data['order_recive'] = false;

            if (!$delivery->action_flag && $act_flag) {
                $act = 'Выезд ' . $whos . ' на <br/>' . date('d.m.Y H:i', $delivery->date_start);
                $do = br() . anchor('orders/update_stage_orders/tofcdl/' . $thisorder->id . '/1/' . date('U'), $who . ' выехал');
                //$do .= ' ' . anchor('/orders/refusing/' . $thisorder->id, 'Отказ') . ' ';
                $delivery_adres = br() . '<span style="background-color: #eeeeee;">' . $thisorder->adres . '</span>';
            }
            if (!$delivery->action_flag && !$act_flag) {
                $act = $who;
                $do = br() . anchor('orders/close_stage/tofcdl/' . $thisorder->id, ' Заказ пришел') . '';
            }
            if ($delivery->action_flag && !$act_flag) {
                if (!$this->smsclient_model->sms_status($thisorder->id)) {
                    $atts_sms = array(
                        'width' => '600',
                        'height' => '270',
                        'scrollbars' => 'no',
                        'status' => 'no',
                        'resizable' => 'no',
                        'screenx' => '250',
                        'screeny' => '150');
                    $this->data['send_sms'] = anchor_popup('sendsms/send/' . $hash, ' Отправить СМС', $atts_sms) . '';
                } else {
                    $this->data['send_sms'] = 'СМС ' . lang('smsstatus_' . $this->smsclient_model->sms_status($thisorder->id));
                }
                $this->data['order_recive'] = true;
            }

            $nal = FALSE;
            $bnlfop = FALSE;
            $bnltov = FALSE;

            switch ($thisorder->paymethod) {
                case 'nal':
                    $nal = TRUE;
                    break;
                case 'bnlfop':
                    $bnlfop = TRUE;
                    break;
                case 'bnltov':
                    $bnltov = TRUE;
                    break;
                case 'bnlitfs':
                    $bnlitfs = TRUE;
                    break;
            }
            $this->data ['nal'] = array('name' => 'paymethod',
                'id' => 'paymethod',
                'value' => 'nal',
                'checked' => $nal);

            $this->data ['bnltov'] = array('name' => 'paymethod',
                'id' => 'paymethod',
                'value' => 'bnltov',
                'checked' => $bnltov);

            $this->data ['bnlfop'] = array('name' => 'paymethod',
                'id' => 'paymethod',
                'value' => 'bnlfop',
                'checked' => $bnlfop);
            $this->data ['bnlitfs'] = array('name' => 'paymethod',
                'id' => 'paymethod',
                'value' => 'bnlitfs',
                'checked' => $bnlitfs);

            $this->data['discount'] = array('name' => 'discount',
                'id' => 'discount',
                'type' => 'text',
                'class' => 'span1',
                'value' => $thisorder->discount);

            $delivery_price = $this->systema_model->select_invoice_item('delivery', $thisorder->id);
            if ($delivery_price->num_rows() > 0) $price = $delivery_price->row()->price;
            else $price = '';
            $this->data['delivery_price'] = array('name' => 'delivery_price',
                'id' => 'delivery_price',
                'type' => 'text',
                'class' => 'span1',
                'value' => $price);

            $this->data['toclnt_close'] = '';


            $toclnt = $this->systema_model->get_stage($thisorder->id, 'toclnt')->row();

            //if($this->ion_auth->is_admin()) print_r($toclnt);

            if ($toclnt->date_start > 0 && $toclnt->date_end == 0) {
                $toclnt_close = anchor('/orders/close_stage/toclnt/' . $thisorder->id, 'Виданий', 'class="btn btn-success"');
                $toclnt_close = anchor('/orders/close_stage/toclnt/' . $thisorder->id, 'Виданий', 'class="btn btn-success" onclick=""');
            } elseif ($toclnt->date_end) {
                $toclnt_close = 'Виданий ' . date('H:i d/m/y', $toclnt->date_end);
            }

            if ($this->ion_auth->user()->row()->id == $thisorder->manager_id || $this->ion_auth->user()->row()->id == 28 || $this->ion_auth->is_admin()) {
                //     echo 'id is '.$thisorder->first_name.nbs().$thisorder->manager_id.nbs().$this->ion_auth->user()->row()->id;
                $this->data['toclnt_close'] = $toclnt_close;
            } else {
                $this->data['toclnt_close'] = '';
            }


            $this->data['camein'] = '';
            $wtclnt = $this->systema_model->get_stage($thisorder->id, 'toclnt')->row();
            if ($wtclnt->date_start > 0 && $wtclnt->date_end == 0 && $wtclnt->info == 'himself') {
                $this->data['camein'] = anchor('/orders/update_stage_orders/toclnt/' . $thisorder->id . '/1/0/0/camein', 'Клиент пришел');
                //update_stage_orders($code,$order_id,$action_flag,$date_start=FALSE,$date_end=FALSE)
            } elseif ($wtclnt->date_start > 0 && $wtclnt->date_end == 0 && $wtclnt->info == 'camein') {
                $this->data['camein'] = '<div style="font-weight:bold;">Клиент ждет на проходной!</div>';
            }

            // Добавление товаров
            $this->data['sale'] = '';

            $sale = $this->fin->select_invoice($thisorder->id, 'sale');

            if ($sale->num_rows() > 0) {
                $this->data['sale'] = '<b>Дополнительные товары</b><br>
                   <table class="table table-condensed table-hover table-bordered span6">';
                foreach ($sale->result() as $row) {


                    $this->data['sale'] .= '<tr><td width="400">' . $row->text . '</td><td>' . $row->price . '</td>';
                    $is_payd ? $this->data['sale'] .= '<td></td></tr>' :
                        $this->data['sale'] .= '<td width="50">' . anchor('fin/del_invoice_item/' . $row->id, '<i class="icon-minus"></i>', 'class="btn"') . '</td></tr>';
                }
                $this->data['sale'] .= '</table>';
            }

            $text = array('name' => 'text',
                'type' => 'text',
                'class' => 'span5',
                'placeholder' => 'наименование товара',
                'id' => 'sale',
                'autocomplete' => 'off'
            );
            $price = array('name' => 'price',
                'type' => 'text',
                'class' => 'span1',
                'placeholder' => 'цена'
            );
            $is_payd ? false : $this->data['sale'] .= '<div class="input-prepend input-append">' .
                form_open('fin/add_extra_works/' . $thisorder->id . '/sale') .
                form_input($text) .
                form_input($price) .
                '<button class="btn" type="submit"><i class="icon-plus"></i></button></div>' .
                form_close();

            // Добавление услуг
            $this->data['extra_works'] = '';

            $extra = $this->fin->select_invoice($thisorder->id, 'extra');
            //print_r($extra);
            if ($extra->num_rows() > 0) {
                $this->data['extra_works'] = '<b>Дополнительные услуги</b><br>
                   <table class="table table-condensed table-hover table-bordered span6">';
                foreach ($extra->result() as $row) {
                    $this->data['extra_works'] .= '<tr><td width="400">' . $row->text . '</td><td>' . $row->price . '</td>';
                    $is_payd ? $this->data['extra_works'] .= '<td></td></tr>' :
                        $this->data['extra_works'] .= '<td width="50">' . anchor('fin/del_invoice_item/' . $row->id, '<i class="icon-minus"></i>', 'class="btn"') . '</td></tr>';
                }
                $this->data['extra_works'] .= '</table>';
            }

            $text = array('name' => 'text',
                'type' => 'text',
                'class' => 'span5',
                'placeholder' => 'наименование услуги',
                'id' => 'extrawork',
                'autocomplete' => 'off'
            );
            $price = array('name' => 'price',
                'type' => 'text',
                'class' => 'span1',
                'placeholder' => 'цена'
            );
            $is_payd ? false : $this->data['extra_works'] .= '<div class="input-prepend input-append">' .
                form_open('fin/add_extra_works/' . $thisorder->id . '/extra') .
                form_input($text) .
                form_input($price) .
                '<button class="btn" type="submit"><i class="icon-plus"></i></button></div>' .
                form_close();

            $this->data['delivery'] = $act . $delivery_adres . $do;
            $this->data['hash'] = $hash;
            $this->data['order_id'] = $thisorder->id;

            // все что выше пересмотреть и  оптимизировать почистить
            //все что ниже новый код для нового вывода заказа
            $this->data['tofcdl'] = $this->systema_model->get_stage($thisorder->id, 'tofcdl');
            $this->data['toclnt'] = $this->systema_model->get_stage($thisorder->id, 'toclnt');
            $this->data['ispayd'] = $this->systema_model->get_stage($thisorder->id, 'ispayd');

            $this->data['extrainfo'] = $this->messages->to_order($thisorder->id, $thisorder->id, 'info');
            $this->data['extracontact'] = $this->messages->to_order($thisorder->id, $thisorder->id, 'contact');
            $this->data['contacts'] = $this->messages->org_contact($thisorder->org_id);


            if ($this->ion_auth->is_admin()) {
                $this->data['orders1'] = $this->orders->GetOrders($this->ion_auth->users()->row()->id);

                //$this->load->view('new/header', $this->data);
                //$this->load->view('new/orders/view_order1', $this->data);
                //$this->load->view('new/footer', $this->data);

                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('orders/view_order1', $this->data);
                $this->load->view('bottom', $this->data);

            } else {
                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('orders/view_order1', $this->data);
                $this->load->view('bottom', $this->data);
            }


        } else {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
    }


    //обновление информаци в заказе: имя, телефон
    public function set_paymethod($order_id, $paymethod)
    {
        if ($paymethod == 'nal') $edrpou = '3047313536';
        if ($paymethod == 'bnltov') $edrpou = '36832980';
        if ($paymethod == 'bnltovitfs') $edrpou = '36832980';
        if ($paymethod == 'bnlfop') $edrpou = '3047313536';
        if ($paymethod == 'subscr') $edrpou = '3047313536';

        $org_id = $this->systema_model->view_order($order_id, true)->row()->org_id;
        $partner_edrpou = $this->systema_model->view_partners(FALSE, $org_id)->row()->edrpou;
        $is_accounting = $this->systema_model->select_accounting($order_id);

        $data['paymethod'] = $paymethod;
        if (count($data) > 0) {
            $this->systema_model->clarify_order($data, $order_id);
        }

        if ($is_accounting->num_rows() > 0) {
            $this->systema_model->update_accounting($order_id, array('for_edrpou' => $edrpou, 'edrpou' => $partner_edrpou));
        } else {
            $accounting_data = array('order_id' => $order_id, 'sum' => '',
                'date' => date('U'), 'debet_kredit' => 0, 'for_edrpou' => $edrpou, 'edrpou' => $partner_edrpou);
            $this->systema_model->insert_accounting($accounting_data);
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function clarify($order_id)
    {
        foreach ($this->input->post() as $param => $value) {
            if ($param != 'submit' && $value != '' && $param != 'delivery_price') $data[$param] = $value;

            if ($param == 'delivery_price') {
                $delivery = $this->systema_model->select_invoice_item('delivery', $order_id);
                if ($delivery->num_rows() > 0) $this->systema_model->update_invoice_item($delivery->row()->id, array('price' => $value));
                else $this->systema_model->insert_invoice_item('delivery', $order_id, 'Доставка', $value);

            }
        }

        if (count($data) > 0) {
            $this->systema_model->clarify_order($data, $order_id);
        }

        if ($this->input->post('paymethod')) {
            if ($this->input->post('paymethod') == 'nal') $edrpou = '3047313536';
            if ($this->input->post('paymethod') == 'bnltov') $edrpou = '36832980';
            if ($this->input->post('paymethod') == 'bnltovitfs') $edrpou = '36832980';
            if ($this->input->post('paymethod') == 'bnlfop') $edrpou = '3047313536';

            $org_id = $this->systema_model->view_order($order_id, true)->row()->org_id;
            $partner_edrpou = $this->systema_model->view_partners(FALSE, $org_id)->row()->edrpou;

            $is_accounting = $this->systema_model->select_accounting($order_id);

            if ($is_accounting->num_rows() > 0) {
                $this->systema_model->update_accounting($order_id, array('for_edrpou' => $edrpou, 'edrpou' => $partner_edrpou));
            } else {
                $accounting_data = array('order_id' => $order_id, 'sum' => '',
                    'date' => date('U'), 'debet_kredit' => 0, 'for_edrpou' => $edrpou, 'edrpou' => $partner_edrpou);
                $this->systema_model->insert_accounting($accounting_data);
            }
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //перемещение заказа в другую организацию
    public function move($order_id)
    {
        if ($this->input->post('org_id') != 0) {
            $data['org_name'] = $this->systema_model->view_partners(FALSE, $this->input->post('org_id'))->row()->short_name;
            $data['org_id'] = $this->input->post('org_id');

            $this->systema_model->clarify_order($data, $order_id);

            $is_accounting = $this->systema_model->select_accounting($order_id);
            $org_id = $this->systema_model->view_order($order_id, true)->row()->org_id;

            $partner = $this->systema_model->view_partners(FALSE, $org_id);
            $data['paymethod'] = $partner->row()->paymethod;
            $partner_edrpou = $partner->row()->edrpou;

            $this->systema_model->clarify_order($data, $order_id);


            if ($is_accounting->num_rows() > 0) {
                $this->systema_model->update_accounting($order_id, array('edrpou' => $partner_edrpou));
            } else {
                $accounting_data = array('order_id' => $order_id, 'sum' => '',
                    'date' => date('U'), 'debet_kredit' => 0, 'edrpou' => $partner_edrpou);
                $this->systema_model->insert_accounting($accounting_data);
            }
        }
        /*$Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
            print_r($Slack->call('chat.postMessage', array(
                        'channel' => '#orders',
                        //'as_user'=>'true',
                        //'username'=>'U0AM6U5T5',
                        'text' => 'Заказ _*№'.$order_id.'*_ создан для организации *'.$data['org_name'].'*'
                        )));
    */
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function print_order1($order_hash)
    {
        $thisorder = $this->data['order'] = $this->systema_model->view_order($order_hash)->row();
        $this->data['extracontact'] = $this->messages->to_order($thisorder->id, $thisorder->id, '');
        $this->data['cartridges'] = $this->systema_model->cartridge_stages_done($this->data['order']->id, array('inofc'));

        $this->load->view('orders/print_order1', $this->data);
    }

    public function print_order_tech($order_hash)
    {

        $thisorder = $this->data['order'] = $this->systema_model->view_order($order_hash)->row();
        $this->data['extracontact'] = $this->messages->to_order($thisorder->id, $thisorder->id, '');

        $this->load->view('orders/print_order_tech', $this->data);
    }

    public function stage($stage_code, $user_id)
    {
        if ($this->ion_auth->logged_in()) {
            if ($stage_code == 'tofcdl' || $stage_code == 'toclnt') {
                redirect('main', 'refresh');
            }
            $this->get_user_menu(anchor_popup('orders/quick_create', lang('menu_orders_create')), 'Керування замовленнями');
            $this->data['stages_menu'] = "";
            $stages = $this->systema_model->get_stages('stages_order')->result();
            foreach ($stages as $stage) {
                $this->data['stages_menu'] .= '<' . anchor('orders/stage/' . $stage->code, $stage->name) . '> ';
            }
            $this->data['stage_code'] = $stage_code;
            $this->data['user_id'] = $user_id;
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('orders/stage', $this->data);
            $this->load->view('bottom', $this->data);
        } else {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
    }

    public function stage_dynamic($stage_code, $user_id = false)
    {
        if ($this->ion_auth->logged_in()) {

            $orders = $this->systema_model->get_orders_stage($stage_code, $user_id)->result();
            $contacter = '';

            switch ($stage_code) {
                case 'tofcdl':

                    //   if ($this->ion_auth->is_admin()) {
                    $atts = array('class' => 'btn btn-sm btn-info', 'width' => '600', 'height' => '670', 'scrollbars' => 'no',
                        'status' => 'no', 'resizable' => 'no', 'screenx' => '250', 'screeny' => '5');

                    $response = '';

                    foreach ($orders as $order) {
                        // print_r($order);
                        $orderData=$this->orders->ViewSimple(false,false,$order->id);
                     //   print_r($orderData->row());
                        $mess = '';
                        $do = '';
                        if ($order->info == 'master' || $order->info == 'courier') {
                            if (!$order->action_flag) {
                                $mess = date('d.m.Y о H:i', $order->date_start) .
                                    '<br/><b>' . $order->first_name . '</b>' . nbs(2);
                                if ($order->manager_id == $this->ion_auth->user()->row()->id) {
                                    $mess .= anchor('orders/update_stage_orders/' . $stage_code . '/' . $order->order_id . '/1/' . date('U'), 'выехал',
                                        array('class' => 'btn btn-sm btn-success'));
                                }
                            }

                            if ($order->action_flag) {
                                $mess = '<div><b>' . $order->first_name . '</b><br/>
                выехал ' . date('d.m.Y в H:i', $order->date_start) . '</div>';
                            }
                        }
                        if ($order->info == 'himself') {
                            $mess = '<div>' . lang($order->info) . '</div>';
                        }

                        $extrainfo = $this->messages->to_order($order->order_id, $order->order_id, '');
                        $other_info = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'info') $other_info .= $info->text . '; ';
                        }
                        $adres = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-adres') $adres .= $info->text . '; ';
                        }
                        $phone = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-tel' || $info->stage_code == 'contact-mob') $phone .= $info->text . '; ';
                        }
                        $name = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-name') $name .= $info->text . '; ';
                        }
                        $org_name = '';
                        if ($order->org_id == 11) {
                            foreach ($extrainfo->result() as $info) {
                                if ($info->stage_code == 'contact-name') $org_name = $info->text;
                            }
                        } else $org_name = $order->short_name;

                        $response .= '<!-- Project-->
            <div class="project">
                <div class="row bg-white has-shadow">
                    <div class="left-col col-lg-4 d-flex align-items-center justify-content-between">
                        <div class="project-title d-flex align-items-center">
                            <!--<div class="image has-shadow">
                            <img src="/assets2/img/project-1.jpg" alt="..."
                                                               class="img-fluid">
                            </div>-->
                            <div class="text">
                                <h3 class="h4">' . anchor_popup("orders/view_order/" . $order->hash, $order->order_id) . '</h3>
                                <small>' . $org_name . '</small>
                            </div>
                        </div>
                        <div class="project-date"><span class="hidden-sm-down"><i class="fa fa-clock-o"></i> ' . $mess . '</span></div>
                    </div>
                    <div class="right-col col-lg-8 d-flex align-items-center">
                        <div class="comments"><i class="fa fa-address-card"></i>' . $adres . ' ' . $phone . ' ' . $name . '</div>
                        
                    </div>
                    <div class="right-col col-lg-4 d-flex align-items-center">';

                        if ($order->manager_id == $this->ion_auth->user()->row()->id) {
                            $response .= '<div class="">' . anchor_popup('/orders/print_order1/' . $order->hash, 'Квитанция', $atts) . '</div>';
                        }

                        $response .= '   </div>
                    <div class="right-col col-lg-8 d-flex align-items-center">
                        <div class="comments"><i class="fa fa-info"></i>' . $other_info . '</div>
                        
                    </div>
                </div>
            </div>
            ';
                    }
//                    }

                    break;

                case 'prewrk':
                    $response = '   <table id="order-list" width="100%"><tr>
        <th class="prework-table-th" width="70">Заказ №</th>
        <th class="prework-table-th" width="150">Клиент</th>
        <th class="prework-table-th" width="150">Поступил</th>
        <th class="prework-table-th" width="*">Устройства</th>
        <th class="prework-table-th" width="250">Дополнительно</th>
        <th class="prework-table-th" width="100"></th>
        </tr>';

                    foreach ($orders as $order) {

                        $contacter = str_replace('&nbsp;', ' ', $order->contacter);
                        $contacter = trim($contacter);
                        if ($contacter != '') $contacter .= '. ';
                        $cartridges = $this->systema_model->cartridges_in_order($order->order_id);
                        $cartridges_in_order = '';
                        if ($cartridges->num_rows() > 0) {
                            foreach ($cartridges->result() as $cartridge) {
                                $cartridges_in_order .= 'картридж ' . $cartridge->name . ' (' . $cartridge->count . ')<br>';
                            }
                        }

                        $techs = $this->systema_model->techs_in_order($order->order_id);
                        $techs_in_order = ' ';
                        if ($techs->num_rows() > 0) {
                            if ($cartridges->num_rows() > 0) $techs_in_order .= '------------------------<br/>';
                            foreach ($techs->result() as $tech) {
                                $techs_in_order .= $tech->name . ' (' . $tech->count . ')<br>';
                            }
                        }

                        $response .= '<tr><td>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
        <td>' . $contacter . $order->short_name . '</td>
        <td>' . date('d M Y H:i', $order->date_create) . '</td>
        <td>' . $cartridges_in_order . $techs_in_order . '</td>
        <td>' . $order->info . '</td>
        <td>' . anchor('orders/close_stage/' . $stage_code . '/' . $order->order_id, ' в работу') . '</td></tr>';
                    }
                    $response .= '</table>';
                    $response .= "<script type=\"text/javascript\">
        $(\"table tr:nth-child(even)\").addClass(\"prework-table-striped\");
$(\"#order-list tr:nth-child(odd)\").addClass(\"prework-table-tr\");
</script>";

                    break;

                case 'inwrk':
                    $response = '<table><tr><td class="grey_border" width="50">№ Заказа</td>
        <td class="grey_border" width="150">организация</td>
        <td class="grey_border" width="300">Доп. инфо</td>
        <td class="grey_border"> </td>
        </tr>';

                    foreach ($orders as $order) {
                        $style1 = $style = ' class="grey_border"';
                        $result = $this->systema_model->check_in_order($order->order_id);
                        $order_status = '';
                        $t = '';
                        $todo_flag = true;
                        $ready = '';
                        $todo = anchor('orders/close_stage/' . $stage_code . '/' . $order->order_id, '->');
                        foreach ($result->result() as $res) {
                            if ($res->stage_code == 'apprv' || $res->stage_code == 'inrfl' ||
                                $res->stage_code == 'todgs' || $res->stage_code == 'inrck' || $res->stage_code == 'totst') {
                                $todo = false;
                                $todo_flag = false;
                            }

                            if ($res->stage_name != $t) $order_status .= $res->stage_name . '; ';
                            $t = $res->stage_name;
                            if ($res->stage_code == 'apprv') {
                                $order_status = 'needapprove';
                                break;
                            }
                        }

                        if ($todo_flag) {
                            $ready = 'go_to_next_stage';
                            $this->systema_model->update_stage_orders($stage_code, $order->order_id, 1, FALSE, date('U'), 'ok');
                            $this->systema_model->update_stage_orders('pprdoc', $order->order_id, 0, date('U'), FALSE);
                            $this->systema_model->update_stage_orders('toclnt', $order->order_id, 0, date('U'), FALSE);

                        }

                        if ($order_status == 'needapprove') {
                            $style1 = ' class="red_border"';
                            $order_status = lang($order_status);
                        }
                        $contacter = str_replace('&nbsp;', ' ', $order->contacter);
                        $contacter = trim($contacter);
                        if ($contacter != '') $contacter .= '. ';
                        $response .= '<tr><td' . $style . '>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
        <td' . $style . '>' . $contacter . $order->short_name . '</td>
        <td' . $style1 . '>' . $order_status . '</td>
        <td' . $style . '>' . $todo . $ready . '</td></tr>';
                    }
                    $response .= '</table>';
                    break;

                case 'pprdoc':

                    $response = '<table class="table table-bordered table-condensed table-hover">
                <tr><td class="grey_border" width="50">№ Заказа</td>
        <td class="grey_border" width="150">организация</td>
        <td class="grey_border" width="300">Доп. инфо</td>
        <td class="grey_border"> </td>
        </tr>';

                    foreach ($orders as $order) {
                        if ($order->org_id == 572) continue;
                        $result = $this->systema_model->check_in_order($order->order_id);
                        $order_status = '';
                        $t = '';
                        $todo_flag = true;
                        $ready = '';
                        $todo = anchor('orders/close_stage/' . $stage_code . '/' . $order->order_id, 'Документы сделаны');
                        foreach ($result->result() as $res) {
                            if ($res->stage_code == 'apprv' || $res->stage_code == 'inrfl' || $res->stage_code == 'todgs' || $res->stage_code == 'inrck' || $res->stage_code == 'totst') {
                                $todo = false;
                                $todo_flag = false;
                            }
                            if ($res->stage_name != $t) $order_status .= $res->stage_name . '; ';
                            $t = $res->stage_name;
                            if ($res->stage_code == 'apprv') {
                                $order_status = 'needapprove';
                                break;
                            }
                        }
                        if ($todo_flag) $ready = '';

                        if ($order_status == 'needapprove') {
                            $style1 = ' class="red_border"';
                            $order_status = lang($order_status);
                        }
                        $contacter = str_replace('&nbsp;', ' ', $order->contacter);
                        $contacter = trim($contacter);
                        if ($contacter != '') $contacter .= '. ';
                        $response .= '<tr><td>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
                            <td width="300">' . $contacter . $order->short_name . '</td>
                            <td>' . $order_status . '</td>
                            <td>' . $todo . $ready . '</td></tr>';
                    }
                    $response .= '</table>';
                    break;

                case 'toclnt':

                    $response = "";
                    $atts_order = array(
                        'class' => 'btn btn-sm btn-info',
                        'width' => '600',
                        'height' => '650',
                        'scrollbars' => 'no',
                        'status' => 'no',
                        'resizable' => 'no',
                        'screenx' => '250',
                        'screeny' => '10');

                    foreach ($orders as $order) {

                        $result = $this->systema_model->check_in_order($order->order_id);
                        $order_status = '';
                        $t = '';
                        $todo_flag = true;
                        //$todo=anchor('orders/close_stage/'.$stage_code.'/'.$order->order_id, 'Доставлен');
                        foreach ($result->result() as $res) {
                            if ($res->stage_code == 'topck') {
                                $todo = false;
                            }
                            if ($res->stage_name != $t) $order_status .= $res->stage_name . '; ';
                            $t = $res->stage_name;
                            if ($res->stage_code == 'apprv') {
                                $order_status = 'needapprove';
                                break;
                            }
                        }

                        $extrainfo = $this->messages->to_order($order->order_id, $order->order_id, '');
                        $other_info = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'info') $other_info .= $info->text . '; ';
                        }
                        $adres = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-adres') $adres .= $info->text . '; ';
                        }
                        $phone = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-tel' || $info->stage_code == 'contact-mob') $phone .= $info->text . '; ';
                        }
                        $name = '';
                        foreach ($extrainfo->result() as $info) {
                            if ($info->stage_code == 'contact-name') $name .= $info->text . '; ';
                        }
                        $org_name = '';
                        if ($order->org_id == 11) {
                            foreach ($extrainfo->result() as $info) {
                                if ($info->stage_code == 'contact-name') $org_name = $info->text;
                            }
                        } else $org_name = $order->short_name;

                        if ($order_status) $order_status = '<i class="fa fa-question"></i> ' . $order_status;

                        $response .= '<!-- Project-->
            <div class="project">
                <div class="row bg-white has-shadow">
                    <div class="left-col col-lg-4 d-flex align-items-center justify-content-between">
                        <div class="project-title d-flex align-items-center">
                            <!--<div class="image has-shadow">
                            <img src="/assets2/img/project-1.jpg" alt="..."
                                                               class="img-fluid">
                            </div>-->
                            <div class="text">
                                <h3 class="h4">' . anchor_popup("orders/view_order/" . $order->hash, $order->order_id) . '</h3>
                                <small>' . $org_name . '</small>
                            </div>
                        </div>
                        <div class="project-date"><span class="hidden-sm-down"><i class="fa fa-clock-o"></i> ' .
                            date('d.m.y о H:i', $order->date_start) . '<br>' . $order->first_name . '</span></div>
                    </div>
                    <div class="right-col col-lg-8 d-flex align-items-center">
                        <div class="comments"><i class="fa fa-address-card"></i>' . $adres . ' ' . $phone . ' ' . $name . '</div>
                        
                    </div>
                    
                    <div class="right-col col-lg-4 d-flex align-items-center">';

                        if ($order->manager_id == $this->ion_auth->user()->row()->id) {
                            $response .= '<div class="">' . anchor_popup('/cartridges/print_delivery_list/' . $order->hash, 'Акт доставки/выдачи картриджей', $atts_order) . '</div>';
                        }

                        $response .= '   
                    </div>
                    <div class="right-col col-lg-8 d-flex align-items-center">
                        <div class="comments"><i class="fa fa-info"></i>' . $other_info . '</div>
                    </div>
                    
                    <div class="right-col col-lg-4 d-flex align-items-center">
                        <div class="badge badge-important">' . $order_status . nbs() . $todo . '</div>
                    </div>
                    <div class="right-col col-lg-8 d-flex align-items-center">
                        
                    </div>
                    
                   
                   
                </div>
            </div>';


                        /*                           $response .= '<tr><td>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
                           <td>' . $org_name . '</td>
                           <td>' . $adres . ' ' . $phone . ' ' . $name . '</td>
                           <td>' . $other_info . '</td>
                           <td>' . $order_status . nbs() . $todo . '</td>
                           <td>' . $order->first_name . '</td></tr>';

                                               $response .= '<tr><td>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
                           <td>' . $org_name . '</td>
                           <td>' . $adres . ' ' . $phone . ' ' . $name . '</td>
                           <td>' . $other_info . '</td>
                           <td>' . $order_status . nbs() . $todo . '</td>
                           <td>' . $order->first_name . '</td></tr>';*/


                        $response .= '</table>';
                    }


                    break;

                case 'ispayd':
                    $response = '
        <table class="table table-condensed table-striped">
        <tr><td width="50">№</td>
        <td width="150">Організація</td>
        <td width="300">Доп. інфо</td>
        <td > </td>
        </tr>';

                    foreach ($orders as $order) {

                        $result = $this->systema_model->check_in_order($order->order_id);
                        $order_status = '';
                        $t = '';
                        $todo_flag = true;
                        $ready = '';
                        $todo = 'Не оплачен';
                        foreach ($result->result() as $res) {
                            if ($res->stage_code == 'apprv' || $res->stage_code == 'inrfl' || $res->stage_code == 'todgs' || $res->stage_code == 'inrck' || $res->stage_code == 'totst') {
                                $todo = false;
                                $todo_flag = false;
                            }
                            if ($res->stage_name != $t) $order_status .= $res->stage_name . '; ';
                            $t = $res->stage_name;
                            if ($res->stage_code == 'apprv') {
                                $order_status = 'needapprove';
                                break;
                            }
                        }
                        if ($todo_flag) $ready = '';

                        if ($order_status == 'needapprove') {
                            $order_status = lang($order_status);
                        }
                        $contacter = str_replace('&nbsp;', ' ', $order->contacter);
                        $contacter = trim($contacter);
                        if ($contacter != '') $contacter .= '. ';
                        $response .= '<tr><td>' . anchor("orders/view_order/" . $order->hash, $order->order_id) . '</td>
        <td>' . $contacter . $order->short_name . '</td>
        <td>' . $order_status . '</td>
        <td>' . $todo . $ready . '</td></tr>';
                    }
                    $response .= '</table>';
                    break;

                default:
                    break;
            }

            echo $response;

        } else {
            false;
        }
    }

    //закрытие этапа заказа
    public function close_stage($from_stage, $order_id)
    {
        $this->systema_model->update_stage_orders($from_stage, $order_id, 1, FALSE, date('U'), 'ok');

        if ($from_stage == 'tofcdl') {
            $this->systema_model->update_stage_orders('prewrk', $order_id, 1, date('U'), FALSE, 'ok');

            /*$Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
                print_r($Slack->call('chat.postMessage', array(
                            'channel' => '#logistic',
                            //'as_user'=>'true',
                            //'username'=>'U0AM6U5T5',
                            'text' => 'Прибыл заказ №'.$order_id
                            )));*/
        }

        if ($from_stage == 'pprdoc') {
            $this->systema_model->update_stage_orders('prewrk', $order_id, 1, date('U'), FALSE, 'ok');
            $this->systema_model->update_stage_orders('ispayd', $order_id, 1, date('U'), FALSE, 'ok');

        }

        if ($from_stage == 'toclnt') {
            $this->systema_model->update_stage_orders('prewrk', $order_id, 1, date('U'), FALSE, 'ok');


        }

        if ($_SERVER['HTTP_REFERER'] != 'http://systema/orders') redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function set_executant($stage_code, $order_id, $executant_id)
    {
        $this->systema_model->update_stage_orders($stage_code, $order_id, 0, $date_start = false, $date_end = false, $info = false, $executant_id);
        // $this->systema_model->update_stage_orders('toclnt', $order_id, 0, $date_start = false, $date_end = false, $info = false, $executant_id);

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function update_stage_orders($code, $order_id, $action_flag, $date_start = FALSE, $date_end = FALSE, $info = false)
    {
        $this->systema_model->update_stage_orders($code, $order_id, $action_flag, $date_start, $date_end, $info);
        echo date('d.m.y H:i', $date_start);

        //http://sys.ddruk.com.ua/orders/update_stage_orders/tofcdl/15905/1/1444462720
        if ($code == 'tofcdl') {
            /* $Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
     print_r($Slack->call('chat.postMessage', array(
                             'channel' => '#logistic',
                             //'as_user'=>'true',
                             //'username'=>'U0AM6U5T5',
                             'text' => $this->ion_auth->user()->row()->id.' Курьер выехал за заказом №'.$order_id
                             )));*/
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //закрытие всех этапов заказа -> закрытие заказа.
    public function close_order($order_id)
    {
        if ($this->ion_auth->is_admin()) {
            $stages = $this->systema_model->get_stages('stages_order')->result();
            foreach ($stages as $stage) {
                echo $stage->code . br();
                $this->systema_model->update_stage_orders($stage->code, $order_id,
                    1, date('U'), date('U'), 'close by root');
            }


            //redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
    }

    public function set_delivery_date($code, $order_id, $action_flag)
    {

        $date = explode('.', $this->input->post('date'));

        $date_start = mktime($this->input->post('hour'), $this->input->post('min'), 0, $date[1], $date[0], $date[2]);
        $this->systema_model->update_stage_orders($code, $order_id, $action_flag, $date_start, $date_end = false, 'courier');
        //$this->systema_model->update_stage_orders("toclnt", $order_id, $action_flag, $date_start + 3600 * 48, $date_end = false, 'courier');
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function setOwner($orderId, $userId)
    {
        $this->orders->Update($orderId, Array('manager_id' => $userId));
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //мега таблица всех заказов
    public function mega_table($org_id = false)
    {
        $orders = $this->systema_model->view_orders_table($org_id);
        //if($this->ion_auth->is_admin()) print_r($orders->result());
        if ($orders->num_rows() > 0) {
            $prev_id = -1;
            $res = '<table width="100%" class="table  table-condensed table-hover"><tr align="left">
            <th>№ заказа</th><th width="200">Клиент</th><th>Доставка в сервис</th>
            <th>Подготовка</th><th>Работа</th>
            <th>Упаковка</th><th>Доставка клиенту</th>
            <th>Оплата</th>
            </tr>';

//строим таблицу всех заказов которые в работе либо не закрыты полностью
            foreach ($orders->result() as $order) {
                $mess = '';
                if ($prev_id == $order->id) {
                    //проверяем этапы заказа
                    switch ($order->stage_code) {
                        case 'prewrk':
                            $cart_all = $this->cartridge->get_stage_simple('apprv', 0, $order->id)->row()->count;
                            if ($cart_all) {
                                $mess .= '<div class="progress progress-striped active"><div class="bar" style="width:100%; background-color: #ff9900;">
                                            <a href="orders/view_order/' . $order->hash . '" style="color:black;">СОГЛАСОВАТЬ ' .
                                    $cart_all . '</a></div></div>';
                            } elseif ($order->date_end) {
                                $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' .
                                    date('d M Y  H:i', $order->date_end) . '</div></div>';
                            } else {
                                $DOC_FLAG++;
                                $mess = '<div class="progress"><div class="bar" style="width:0%;"></div></div>';
                            }

                            $res .= '<td>' . $mess . '</td>';
                            break;

                        case 'inwrk':
                            if ($order->date_end) {
                                $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' .
                                    date('d M Y  H:i', $order->date_end) . '</div></div>';
                            } else {
                                $DOC_FLAG++;
                                $cart_done = 0;
                                $cart_all = 1;

                                $cart_all = $this->cartridge->get_stage_simple('inofc', 1, $order->id)->row()->count;
                                $cart_done = $this->cartridge->get_stage_simple('inrfl', 1, $order->id)->row()->count;
                                $cart_all ? $cart_progress = ($cart_done / $cart_all * 100) * 0.95 : $cart_progress = 0;
                                $all_done = true;
                                $result = $this->systema_model->check_in_order($order->id);
                                foreach ($result->result() as $rs) {
                                    if ($rs->stage_code == 'apprv' || $rs->stage_code == 'inrfl' ||
                                        $rs->stage_code == 'todgs' || $rs->stage_code == 'inrck' || $rs->stage_code == 'totst') {
                                        $all_done = false;
                                    }
                                }
                                if ($all_done && $cart_all != 0) {

                                    $this->systema_model->update_stage_orders($order->stage_code, $order->id, 1, FALSE, date('U'), 'ok');
                                    $this->systema_model->update_stage_orders('pprdoc', $order->id, 0, date('U'), FALSE);
                                    $this->systema_model->update_stage_orders('toclnt', $order->id, 0, date('U'), FALSE);


                                    if ($order->org_id != 572) {
                                        $data['subject'] = 'Зробити рахунок ' . $order->id;
                                        $data['text'] = 'Зробити рахунок у замовленні ' . anchor_popup('/orders/view_order/' . $order->hash, $order->id) .
                                            '<br>' . $order->short_name;
                                        $data['order_id'] = $order->id;
                                        $data['uniq_num'] = '';
                                        $data['stage_code'] = 'task';
                                        $data['user_id'] = $this->ion_auth->user()->row()->id;
                                        $data['add_date'] = date('U');
                                        $data['remind_date'] = '';
                                        $data['hidden'] = '';
                                        $data['to_user'] = 32;
                                        $data['isread'] = '';
                                        //  $this->messages->add_message($data);
                                    }
                                }


                                $mess = '<div class="progress">';
                                if ($cart_done != 0) $mess .= '<div class="bar" style="width:' . $cart_progress . '%;">' . $cart_done . '/' . $cart_all . '</div>';
                                if ($cart_done == 0 && $cart_all != 0) $mess .= '<div class="bar bar-danger" style="width:100%;">в работу ' . $cart_all . '</div>';

                                $mess .= '<div class="bar bar-warning" style="width: ' . $tech_progress . '%;">.: ' . $tech_done . ' из ' . $tech_all . '</div>';

                                $mess .= '</div>';

                            }
                            $res .= '<td width="175">' . $mess . '</td>';
                            break;

                        case 'pprdoc':
                            $cart_done = 0;
                            $cart_all = 1;
                            $cart_all = $this->cartridge->get_stage_simple('topck', 0, $order->id)->row()->count;
                            $all_done = true;
                            $result = $this->systema_model->check_in_order($order->id);
                            foreach ($result->result() as $rs) {
                                if ($rs->stage_code == 'apprv' || $rs->stage_code == 'inrfl' ||
                                    $rs->stage_code == 'todgs' || $rs->stage_code == 'inrck' || $rs->stage_code == 'totst') {
                                    $all_done = false;
                                }
                            }
                            $mess = '<div class="progress">';

                            if ($cart_all != 0 && $all_done) {
                                $attrib = array(
                                    'style' => 'color:white',
                                    'width' => '600',
                                    'height' => '650',
                                    'scrollbars' => 'no',
                                    'status' => 'no',
                                    'resizable' => 'no',
                                    'screenx' => '250',
                                    'screeny' => '10');
                                $mess .= '<div class="bar bar-danger" style="width:100%;">'
                                    . anchor_popup('cartridges/to_pack/' . $order->id, 'УПАКОВАТЬ ' . $cart_all, $attrib) . '</div>';
                            }
                            if ($cart_all != 0 && !$all_done) {
                                $attrib = array(
                                    'style' => 'color:white',
                                    'width' => '600',
                                    'height' => '650',
                                    'scrollbars' => 'no',
                                    'status' => 'no',
                                    'resizable' => 'no',
                                    'screenx' => '250',
                                    'screeny' => '10');
                                $mess .= '<div class="bar" style="width:100%;">'
                                    . anchor_popup('cartridges/to_pack/' . $order->id, 'упаковать ' . $cart_all, $attrib) . '</div>';
                            }

                            if (!$DOC_FLAG) $mess .= '<div class="progress"><div class="bar" style="width:100%;">'
                                . anchor_popup('fin/print_check/' . $order->id, 'Сделать Чек ', $attrib) . '</div></div>';
                            if ($order->date_end) $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' .
                                date('d M Y  H:i', $order->date_end) . '</div></div>';
                            $mess .= '</div>';

                            $res .= '<td width="175">' . $mess . '</td>';
                            break;

                        case 'toclnt':

                            $cart_done = 0;
                            $cart_all = 1;
                            $bar_class = "bar-warning";
                            if ($order->info == 'camein') {
                                $rapid_order .= anchor('orders/view_order/' . $order->hash, $order->id) . ' | ';
                                $bar_class = "bar-danger";
                                $rapid_order_flag = true;
                            }
                            //$tech_done=0;$tech_all=1;
                            $cart_all = $this->cartridge->get_stage_simple('todsp', 0, $order->id)->row()->count;
                            if ($cart_all) {
                                $mess .= '<div class="progress"><div class="bar ' . $bar_class . '" style="width:100%;">к выдаче ' . $cart_all . '</div></div>';
                            } elseif ($order->date_end) {
                                $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' . date('d M Y  H:i', $order->date_end) . '</div></div>';
                            } else {
                                //  $mess='<div class="progress progress-striped active"><div class="bar bar-success" style="width:100%;">выполняется</div></div>';
                                $mess = '<div class="progress"><div class="bar" style="width:0%;"></div></div>';
                            }

                            $res .= '<td>' . $mess . '</td>';

                            break;

                        case 'ispayd':

                            if (!$order->date_start) {
                                $mess = '<div class="progress width:50%"><div class="bar bar-warning" style="width:100%;">-</div></div>';
                            } else {
                                if ($order->date_end) {
                                    $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' . date('d M Y', $order->date_end) . '</div></div>';
                                } else {
                                    $mess = '<div class="progress"><div class="bar bar-info" style="width:100%;">+</div></div>';
                                }
                            }
                            //if($this->ion_auth->is_admin()) print_r($order);
                            $res .= '<td>' . $mess . '</td>';
                            break;


                        default:
                            $res .= '<td>' . $mess . '</td>';
                            break;
                    }
                }
                if ($prev_id != $order->id) {
                    $DOC_FLAG = 0;
                    $cart_all = $this->cartridge->get_stage_simple('inofc', 0, $order->id)->row()->count;
                    if ($cart_all) {
                        $mess .= '<div class="progress"><div class="bar bar-success" style="width:100%;">в офисе: ' . $cart_all . '</div></div>';
                    } elseif ($order->date_end) {
                        $mess = '<div class="progress"><div class="bar bar-success" style="width:100%;">' . date('d M Y  H:i', $order->date_end) . '</div></div>';
                    } else {
                        continue;
                        $mess = '<div class="progress progress-striped"><div class="bar bar-success" style="width:100%;background-color:navy;">выполняется</div></div>';
                    }
                    /*$order->date_end ?
                        $mess='<div class="progress"><div class="bar bar-success" style="width:100%;">'.date('d M Y  H:i',$order->date_end).'</div></div>':
                     $mess='<div class="progress progress-striped"><div class="bar bar-success" style="width:100%;background-color:navy;">выполняется</div></div>';
                    */
                    if ($this->ion_auth->user()->row()->id != $order->manager_id) {
                        $res .= '<tr>';
                    } else {
                        $res .= '<tr class="info">';
                    }
                    $res .= '<td>' . anchor('orders/view_order/' . $order->hash, $order->id);

                    //if($this->ion_auth->is_admin()) $res.=nbs().anchor_popup('orders/close_order/'.$order->id,'З').nbs().$order->manager_id;
                    $res .= '</td>';
                    $org_name = '';
                    $name = '';
                    $contacts = $this->messages->to_order($order->id, $order->id, 'contact');
                    foreach ($contacts->result() as $contact) {
                        if ($contact->stage_code == 'contact-name') $name = $contact->text;
                    }
                    if ($name == '') $name = $order->contacter;
                    if ($order->org_id != 11) $org_name = $order->short_name . br();
                    if ($order->org_id == 572) $org_name = '<i class="icon-shopping-cart"></i>' . nbs();


                    $res .= '<td>' . $org_name . $name . '</td>';
                    $res .= '<td>' . $mess . '</td>';
                }
                $prev_id = $order->id;
            }
            $res .= '</table>';
        }
        $occupied_cells = $this->systema_model->cartridge_stages(false, array('inofc', 'todgs', 'apprv', 'inrck', 'inrfl', 'topck'))->num_rows();
        $free_cells = 64 - $occupied_cells;
        if(is_cli())
        {

        }
        else {

            echo 'Свободно: ' . $free_cells . "; ";
            echo 'Занято: ' . $occupied_cells . "; ";
            echo 'Загрузка: ' . round(($occupied_cells / 64) * 100) . "%";
            echo nbs(13);
            //if ($rapid_order_flag) echo '<span class="alert alert-error">НА ПРОХОДНОЙ ЖДУТ:   ' . $rapid_order . '</span>';
            echo str_replace(array('Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'),
                array('Ноя', 'Дек', 'Янв', 'Фев', 'Мар', 'Апр', 'Мая', 'Июн', 'Июл', 'Авг', 'Сен'), $res);
        }
    }


    //функция внесения картриджа в заказ (надо перенести в cartridges.php)
    public function add_cartridge($order_id, $hash)
    {
        if ($this->ion_auth->logged_in()) {
            //проверим ввод данных
            $this->form_validation->set_rules('cart_num', 'номер картриджа', 'trim|required|xss_clean|alpha_numeric');
            $this->form_validation->set_rules('cart_name', 'наименование картриджа', 'trim|required|xss_clean');
            $this->form_validation->set_rules('cart_id', 'cart_id', 'required|xss_clean');
            if ($this->form_validation->run() == true) {
                $thisorder = $this->systema_model->view_order($hash)->row();
                $cart_num = mb_strtoupper($this->input->post('cart_num'), 'utf-8');
                $cart_num = trim($cart_num);
                $cart_data = array('name_id' => $this->input->post('cart_id'),
                    'org_id' => $thisorder->org_id,
                    'uniq_num' => $cart_num,
                    'name' => $this->input->post('cart_name'));
                // добавляем картридж в реестр
                $result = $this->systema_model->add_cartridge($cart_num, $cart_data);

                //делаем запись что картридж у нас и создаем все этапы под него
                if ($this->systema_model->create_stage($order_id, 'cartridge', 'inofc', date('U'), $this->ion_auth->user()->row()->id, '', $cart_num)) {
                    $this->systema_model->update_stage_orders('prewrk', $order_id, 1, false, false, 'Идет обработка заказа');
                    $stages = $this->systema_model->get_stages('stages_cartridge')->result();
                    foreach ($stages as $stage) {
                        if ($stage->code != 'inofc') {
                            $this->systema_model->create_stage($order_id, 'cartridge', $stage->code, 0,
                                $this->ion_auth->user()->row()->id, '', $cart_num);
                        }
                    }
                }
                redirect('orders/add_cartridge/' . $order_id . '/' . $hash, 'refresh');
            } else {
                $free_cels = 64 - $this->systema_model->cartridge_stages(false, array('inofc', 'todgs', 'apprv', 'inrck', 'inrfl', 'topck'))->num_rows();
                if ($free_cels > 1) {
                    //echo $free_cels;
                    //отображаем форму для внесения картриджа
                    //отправляем ссобщения об ошибках если они есть
                    $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    $this->data['cart_num'] = array('name' => 'cart_num',
                        'id' => 'cart_num',
                        'type' => 'text',
                        'size' => '30',
                        'onkeyup' => 'get_cartridge(this.value);',
                        'value' => $this->form_validation->set_value('cart_num'),
                        'autocomplete' => 'off'
                    );
                    $this->data['cart_name'] = array('name' => 'cart_name',
                        'id' => 'cart_name',
                        'type' => 'text',
                        'size' => '10',
                        'onkeyup' => 'autosuggest_cart(this.value);',
                        'value' => $this->form_validation->set_value('cart_name'),
                        'autocomplete' => 'off'
                    );
                    $this->data['title'] = 'Внесение картриджа в заказ';
                    $this->data['hash'] = $hash;
                    $this->data['order_id'] = $order_id;
                    $this->load->view('header', $this->data);
                    $this->load->view('orders/add_cartridge', $this->data);
                    $this->load->view('bottom', $this->data);
                } else {
                    echo "Нет свободных ячеек! Дядя Вася сидит на печке!";
                }
            }
        } else {
            // redirect('main/login', 'refresh');
        }
    }

    public function stat()
    {
        echo '<table border=1><tr><th>month</th><th>all</th><th>privat</th><th>org</th></tr>';
        for ($i = 10; $i <= 12; $i++) {
            $start_date = mktime(0, 0, 0, $i, 1, 2012);
            $end_date = mktime(23, 59, 59, $i + 1, 0, 2012);
            $all = $this->systema_model->orders_count($start_date, $end_date);
            $privat = $this->systema_model->orders_count($start_date, $end_date, 11);
            $org = $all - $privat;
            echo '<tr><td>' . $i . '/2012</td><td>' . $all . '</td>';
            echo '<td>' . $privat . '</td>';
            echo '<td>' . $org . '</td></tr>';
        }

        for ($i = 1; $i <= date('m'); $i++) {
            $start_date = mktime(0, 0, 0, $i, 1, 2013);
            $end_date = mktime(23, 59, 59, $i + 1, 0, 2013);
            $all = $this->systema_model->orders_count($start_date, $end_date);
            $privat = $this->systema_model->orders_count($start_date, $end_date, 11);
            $org = $all - $privat;
            echo '<tr><td>' . $i . '/2013</td><td>' . $all . '</td>';
            echo '<td>' . $privat . '</td>';
            echo '<td>' . $org . '</td></tr>';
        }
        echo '</table>';


    }

    public function without_cart($order_id)
    {
        $this->systema_model->update_stage_orders('inwrk', $order_id, 1, false, date('U'), 'without cartridges');
        $this->systema_model->update_stage_orders('pprdoc', $order_id, 1, date('U'), false, 'without cartridges');
        $this->systema_model->update_stage_orders('toclnt', $order_id, 1, date('U'), false, 'without cartridges');
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }


    public function search($order_id)
    {


        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {

            $this->data['stages_menu'] = "";
            $stages = $this->systema_model->get_stages('stages_order')->result();
            foreach ($stages as $stage) {
                $this->data['stages_menu'] .= '<' . anchor('orders/stage/' . $stage->code, $stage->name) . '> ';
            }
            $this->get_user_menu(anchor_popup('orders/quick_create', lang('menu_orders_create')), 'Керування замовленнями');


            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('orders/search', $this->data);
            $this->load->view('bottom', $this->data);


            /*
                      $orders=$this->systema_model->view_orders_table(0,1,$order_id);
                      foreach($orders->result() as $order)
                      {
                          echo $order->id.' - '.$order->short_name.'<br/>';
                      }*/
        }
    }

    public function search_action($order_id)
    {

       /* $orders = $this->orders->ViewSimple(0, $order_id);
        $result = "<table class='table table-bordered table-condensed table-striped'>";
        foreach ($orders->result() as $order) {
            $result .= "<tr>";
            $result .= "<td>" . anchor_popup('/orders/view_order/' . $order->hash, $order->id) . "</td>";
            $result .= "<td>" . $order->short_name . "</td>";
            $result .= "<tr>";

        }
        $result .= "</table>";*/
        //echo $result;

       echo $this->GetOrders(false,$order_id,1);
    }


}