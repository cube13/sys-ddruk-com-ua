<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cartridges extends CI_Controller
{
    /**
     * Cartridges constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('slack');

        $this->load->helper('url');
        $this->load->helper('html');


        $this->load->database();

        $this->load->model('systema_model');
        $this->load->model('cartridge_model', 'cartridge');

        $this->load->model('store_model', 'store');
        $this->load->model('messages_model', 'messages');

        $this->lang->load('ion_auth', 'russian');

        $this->lang->load('systema', 'russian');

        $this->load->helper('language');
        $this->load->helper('date');
        $this->load->helper('text');
    }


    private function get_user_menu($usermenu = "", $userhere = "")
    {
        $user = $this->ion_auth->user()->row();
        if ($usermenu) {
            $tomain = '';
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach ($user_groups as $group) {
                $tomain .= anchor($group->name, lang($group->description)) . " | ";
                if ($group->name == $this->uri->segment(1)) {
                    $userhere = " - " . lang($group->description);
                }

            }
            $this->data['usermenu'] = $usermenu;
            $this->data['title'] = $user->first_name . " " . $user->last_name . " - " . $userhere;
            $this->data['tomain'] = $tomain;
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

    public function index()
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            if ($this->ion_auth->is_admin() || $this->ion_auth->user()->row()->id == 16) {
                $this->get_user_menu(anchor('cartridges/index', 'Процессы') .
                    ' | ' . anchor('cartridges/catalog', 'Каталог'), 'Картриджы');
            } else {
                $this->get_user_menu(' ');
            }


            $this->data['searchform'] = '';
            $this->data['stage_code'] = false;
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('cartridges/main', $this->data);
            $this->load->view('bottom', $this->data);

        }
    }

    /**
     * @param bool $brand_id
     * @param bool $type
     */
    public function catalog($brand_id = false, $type = false)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } elseif ($this->ion_auth->is_admin() || $this->ion_auth->user()->row()->id == 16) {
            /*select cartridge.id, brands.name, cartridge.name, cartridge.cena_zapravki, cartridge.cena_vostanovlenia,
from `cartridge`
join brands on cartridge.brand=brands.id
where cartridge.brand=4
   */
            $this->data['brand_id'] = $brand_id;

            $this->data['brands'] = $this->cartridge->get_brands();

            $this->data['cartridges'] = $this->cartridge->view($brand_id, $type);
            $this->data['kurs_usd_nal'] = $this->systema_model->settings('kurs_usd_nal')->row()->value;

            $this->get_user_menu(anchor('cartridges/index', 'Процессы') .
                ' | ' . anchor('cartridges/catalog', 'Каталог'), 'Картриджы');
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('cartridges/catalog', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }


    public function set_usd_price()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('main/login', 'refresh');
        } else {
            foreach ($this->input->post() as $key => $value) {
                $keys = explode('_', $key);
                $id = $keys[0];
                $type = $keys[1];
                $price[$id][$type] = $value;
            }
            foreach ($price as $key => $value) {
                $data = array('zapravka_usd' => $value['refill'], vostanovlenie_usd => $value['recikl']);
                $this->cartridge->set_usd_price($data, $key);
            }

        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function filter($stage_code)
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->get_user_menu(' ');

            $this->data['stage_code'] = $stage_code;
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('cartridges/table', $this->data);
            $this->load->view('bottom', $this->data);
        }
    }

    //Вывод ГЛАВНОЙ ТАБЛИЦЫ картриджей
    public function main_table()
    {
        //На диагностике до работ
        $stages = $this->systema_model->cartridge_stages(false, array('inofc', 'todgs'));
        $count_in_diag = $stages->num_rows();
        $cart_in_diag = '<table class="table table-border">';
        foreach ($stages->result() as $stage) {
            if ($stage->org_id == 11) $privet = ' <i class="icon-user"></i>';
            else $privet = '';
            if ($stage->sort == -2) $privet .= ' <i class="icon-fire"></i>';
            $stage_time = '';

            //На входе отображаем время ввдения в базу
            $result = $this->cartridge->timeline($stage->order_id, $stage->cart_num)->result();
            $timestamp = $result[0]->date_start;
            $stage_time = '<b>' . timespan($timestamp) . '</b>';
            $cart_in_diag .= '<tr><td style="border:1px solid #cccccc">' . $stage->adres . '</td>'
                . '<td width=90>' . anchor_popup('orders/view_order/' . $stage->hash, $stage->cart_num . $privet) . '</td><td>' . $stage_time . '</td></tr>';
        }
        $cart_in_diag .= '</table>';

        //На согласовании
        $stages = $this->systema_model->cartridge_stages(false, array('apprv'));
        $count_in_apprv = $stages->num_rows();
        $cart_in_apprv = '<table class="table table-border">';
        foreach ($stages->result() as $stage) {
            if ($stage->org_id == 11) $privet = ' <i class="icon-user"></i>';
            else $privet = '';
            if ($stage->sort == -2) $privet .= ' <i class="icon-fire"></i>';
            $stage_time = '';
            $timestamp = $stage->date_start;
            $local_date = gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $stage_time = '<b>' . timespan($stage->date_start) . '</b>';
            $cart_in_apprv .= '<tr><td style="border:1px solid #cccccc">' . $stage->adres . '</td>
                <td width=90>' . anchor_popup('orders/view_order/' . $stage->hash, $stage->cart_num . $privet) . '</td><td>' . $stage_time . '</td></tr>';
        }
        $cart_in_apprv .= '</table>';

        //Работа
        $count_in_work = $stages = $this->systema_model->cartridge_stages(false, array('inrfl'))->num_rows();
        $stages = $this->systema_model->cartridge_stages(false, array('inrck', 'inrfl'));
        $cart_in_work = '<table class="table table-border">';
        foreach ($stages->result() as $stage) {
            if ($stage->cart_num != $prev_num) {
                if ($stage->org_id == 11) $privet = ' <i class="icon-user"></i>';
                else $privet = '';
                if ($stage->sort == -2) $privet .= ' <i class="icon-fire"></i>';
                $stage_time = '';
                $timestamp = $stage->date_start;
                $local_date = gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
                $stage_time = '<b>' . timespan($stage->date_start) . '</b>';
                $cart_in_work .= '<tr><td style="border:1px solid #cccccc">' . $stage->adres . '</td>
                <td width=90>' . anchor_popup('orders/view_order/' . $stage->hash, $stage->cart_num . $privet) . '</td><td>' . $stage_time . '</td></tr>';
            }
            $prev_num = $stage->cart_num;
        }
        $cart_in_work .= '</table>';

        //Упаковать
        $stages = $this->systema_model->cartridge_stages(false, array('topck'));
        $count_in_pack = $stages->num_rows();
        $cart_in_pack = '<table class="table table-border">';
        foreach ($stages->result() as $stage) {
            if ($stage->org_id == 11) $privet = ' <i class="icon-user"></i>';
            else $privet = '';
            if ($stage->sort == -2) $privet .= ' <i class="icon-fire"></i>';
            $stage_time = '';
            $timestamp = $stage->date_start;
            $local_date = gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $stage_time = '<b>' . timespan($stage->date_start) . '</b>';

            $cart_in_pack .= '<tr><td style="border:1px solid #cccccc">' . $stage->adres . '</td>
                <td width=90>' . anchor_popup('orders/view_order/' . $stage->hash, $stage->cart_num . $privet) . '</td><td>' . $stage_time . '</td></tr>';
        }
        $cart_in_pack .= '</table>';

        //Выдать
        $stages = $this->systema_model->cartridge_stages(false, array('todsp'));
        $count_in_todsp = $stages->num_rows();
        $cart_in_todsp = '<table class="table table-border">';
        foreach ($stages->result() as $stage) {
            if ($stage->org_id == 11) $privet = ' <i class="icon-user"></i>';
            else $privet = '';
            if ($stage->sort == -2) $privet .= ' <i class="icon-fire"></i>';
            $stage_time = '';

            //на Выдаче отбражаем время со входа в офис
            $result = $this->cartridge->timeline($stage->order_id, $stage->cart_num)->result();
            $timestamp = $result[0]->date_start;
            $local_date = gmt_to_local($timestamp, $this->config->item('timezone'), $this->config->item('$daylight_saving'));
            $global_time = '<b>' . timespan($timestamp) . '</b>' . br() . date('d/M H:i', $local_date);

            $cart_in_todsp .= '<tr><td style="border:1px solid #cccccc">' . $stage->adres . '</td>
                <td width=90>' . anchor_popup('orders/view_order/' . $stage->hash, $stage->cart_num . $privet) . '</td><td>' . $global_time . '</td></tr>';
        }
        $cart_in_todsp .= '</table>';
        $cartridges = "";

        $cartridges .= '<table class="table table-condensed">';
        $cartridges .= '<tr ><th width=25% class="alert-info" style="text-align:center;"><h4>' . anchor('cartridges/filter/inofc-todgs', 'Диагностика') . ' (' . $count_in_diag . ')</h4></th>
                <th width=25% class="alert-error" style="text-align:center;"><h4>' . anchor('cartridges/filter/apprv', 'Согласование') . ' (' . $count_in_apprv . ')</h4></th>
                <th width=25% class="alert-success" style="text-align:center;"><h4>' . anchor('cartridges/filter/inrck-inrfl', 'Работа') . ' (' . $count_in_work . ')</h4></th>
                <th width=25% class="alert-danger" style="text-align:center;"><h4>' . anchor('cartridges/filter/topck', 'Упаковать') . ' (' . $count_in_pack . ')</h4></th></tr>';
        $cartridges .= '<tr><td  class="alert-info">' . $cart_in_diag . '</td>
                <td  class="alert-error">' . $cart_in_apprv . '</td>
                <td  class="alert-success">' . $cart_in_work . '</td>
                <td  class="alert-danger">' . $cart_in_pack . '<span style="text-align:center"><h4>' . anchor('cartridges/filter/todsp', 'Выдать') . ' (' . $count_in_todsp . ')</h4></span>' . $cart_in_todsp . '</td></tr>';


        $cartridges .= '</table>';
        echo $cartridges;

    }

    public function cellOcupation($stage=false)
    {
        if($stage)
        {
            $stages = $this->systema_model->cartridge_stages(false, $stage);
            $cart_in_diag = array();
            foreach ($stages->result() as $stage) {
                $cart_in_diag[$stage->adres] = 1;
            }
        }
        else
        {
            //На диагностике до работ
            $stages = $this->systema_model->cartridge_stages(false, array('inofc', 'todgs', 'apprv', 'inrck', 'inrfl', 'topck'));
            $cart_in_diag = array();
            foreach ($stages->result() as $stage) {
                $cart_in_diag[$stage->adres] = 1;
            }
        }
        if(count($cart_in_diag)) echo json_encode($cart_in_diag);
        else echo '';
    }

    public function cartCount()
    {
        $start_date=mktime(0, 0, 0, date('m'), 1, date('Y'));
        $end_date=mktime(23, 59, 59, date('m')+1, 0, date('Y'));
        $cart_done=$this->cartridge->get_count($start_date,$end_date,$org_id=false);

        echo $cart_done;
    }

    //отображение очереди картриджей в работу(вывод администратору)
    public function to_work_list_new($stage_code = false)
    {
        //MAIN_MANU
        //На диагностике до работ
        $stages = $this->systema_model->cartridge_stages(false, array('inofc', 'todgs'));
        $count_in_diag = $stages->num_rows();

        //На согласовании
        $stages = $this->systema_model->cartridge_stages(false, array('apprv'));
        $count_in_apprv = $stages->num_rows();

        //Работа
        $stages = $this->systema_model->cartridge_stages(false, array('inrfl'));
        $count_in_work = $stages->num_rows();

        //Упаковать
        $stages = $this->systema_model->cartridge_stages(false, array('topck'));
        $count_in_pack = $stages->num_rows();

        //Выдать
        $stages = $this->systema_model->cartridge_stages(false, array('todsp'));
        $count_in_todsp = $stages->num_rows();
        $MAIN_MENU = "";
        $HS = '<h4>';
        $HE = '</h4>';
        switch ($stage_code) {
            case 'inofc-todgs':
                $style1 = 'border: 2px solid blue';
                $style2 = '';
                $style3 = '';
                $style4 = '';
                $style5 = '';
                break;
            case 'apprv':
                $style1 = '';
                $style2 = 'border: 2px solid red';
                $style3 = '';
                $style4 = '';
                $style5 = '';
                break;
            case 'inrck-inrfl':
                $style1 = '';
                $style2 = '';
                $style3 = 'border: 2px solid green';
                $style4 = '';
                $style5 = '';
                break;
            case 'topck':
                $style1 = '';
                $style2 = '';
                $style3 = '';
                $style4 = 'border: 2px solid red';
                $style5 = '';
                break;
            case 'todsp':
                $style1 = '';
                $style2 = '';
                $style3 = '';
                $style4 = '';
                $style5 = 'border: 2px solid blue';
                break;
        }


        $MAIN_MENU .= '<table class="table table-condensed">';
        $MAIN_MENU .= '<tr ><th width=20% class="alert-info" style="text-align:center;' . $style1 . '">' . $HS . anchor('cartridges/filter/inofc-todgs', 'Диагностика') . ' (' . $count_in_diag . ')' . $HE . '</th>
                <th onclick="cart_list(apprv);" width=20% class="alert-error" style="text-align:center;' . $style2 . '">' . $HS . anchor('cartridges/filter/apprv', 'Согласование') . ' (' . $count_in_apprv . ')' . $HE . '</th>
                <th onclick="cart_list(inrck);" width=20% class="alert-success" style="text-align:center;' . $style3 . '">' . $HS . anchor('cartridges/filter/inrck-inrfl', 'Работа') . ' (' . $count_in_work . ')' . $HE . '</th>
                <th onclick="cart_list(topck);" width=20% class="alert-danger" style="text-align:center;' . $style4 . '">' . $HS . anchor('cartridges/filter/topck', 'Упаковать') . ' (' . $count_in_pack . ')' . $HE . '</th>
                <th onclick="cart_list(todsp);" width=25% class="alert-info" style="text-align:center;' . $style5 . '">' . $HS . anchor('cartridges/filter/todsp', 'Выдать') . ' (' . $count_in_todsp . ')' . $HE . '</th></tr>';


        $MAIN_MENU .= '</table>';
        //END OF MAIN_MENU


        $stage_code ? $stage_code = explode('-', $stage_code) : $stage_code = array('inrfl', 'inrck', 'apprv', 'inofc', 'todgs', 'topck');
        $stages = $this->systema_model->cartridge_stages(false, $stage_code);

        $print_flag = false;
        if ($stages->num_rows > 0) {


            $cartridges = '<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">';

            $cartridges .= '<button id="refresh_cart_list" type="button" class="btn btn-default" onclick="cart_list();">
                                        <i id="icon-refresh" class="icon-refresh"></i>
                                    </button><table class="table table-condensed table-hover table-striped table-bordered">';
            $cartridges .= '<tr ><th width=200>Заказ № / Клиент</th>
                <th width=30>Яч.</th>
                <th width=150>№/модель картриджа</th>
                <th width=250>Информация</th>
                <th width=100>Время входа</th>
                <th width=150>время в работе </th>
                <th></th>';

            $cartridges .= '</tr>';
            $cn = '';
            $sid = '';
            foreach ($stages->result() as $stage) {

                //Формирование кнопок управления
                $to_apprv = '<td></td>';
                if ($stage->info == 'hvdef' || $stage->info == 'defaf' || $stage->info == 'needrecikl'
                    || $stage->info == 'fullwodef' || $stage->info == 'handspenises'
                    || $stage->info == 'otk_failed') {
                    $to_apprv = '<td><div class="input-prepend input-append">' .
                        form_open('/cartridges/client_answer/' . $stage->cart_num . '/' . $stage->order_id) .
                        '<select name="apprv" class="span2">
                        <option value="refill">Заправка</option>
                        <option value="narefill">Заправка(без согласования)</option>
                        <option value="refillonly">Только заправка(без востановления)</option>
                        <option value="reckl">Восстановление</option>
                        <option value="nareckl">Восстановление(без согласования)</option>
                        <option value="recklonly">Восстановление (без заправки)</option>
                        <option value="stop">Ничего не делать</option>
                     </select>
                     <input type="submit" class="btn btn-success"  value="ОК+">' .
                        form_close() . '</div></td>';
                }
                if ($stage->stage_code == 'topck') {
                    $to_apprv = '<td><div class="input-append">' .
                        form_open('/cartridges/to_dsp/' . $stage->cart_num . '/' . $stage->order_id);
                    $to_apprv .= '<input type="input" name="adres" class="span1">
                       <button class="btn btn-small" type="submit" title="Упаковать"><i class="icon-large icon-gift"></i></button>' .
                        '<button class="btn btn-small btn-info" type="submit" title="Наклейка">' . anchor_popup('cartridges/to_pack/' . $stage->order_id . '/' . $stage->cart_num, '<i class="icon-large icon-print"></i>', $attrib) . '</button>' .
                        '<a class="btn btn-small btn-danger" href="/cartridges/not_done/' . $stage->cart_num . '/' . $stage->order_id . '/1" title="Вернуть"><i class="icon-retweet"></i></a>' .
                        form_close() . '</td>';
                }
                if ($stage->stage_code == 'todsp') {
                    $to_apprv = '<td>' . form_open('/cartridges/wait_client/' . $stage->cart_num . '/' . $stage->order_id);
                    $to_apprv .= '<input type="submit" class="btn btn-info" value="Выдан">' . form_close() . '</td>';
                }
                //конец формирования

                $needparts = '';
                $parts = $this->cartridge->get_cart_parts($stage->cart_id, 'inrfl')->num_rows();
                $parts += $this->cartridge->get_cart_parts($stage->cart_id, 'inrck')->num_rows();
                if ($parts < $this->config->item('material_amount_need')) $needparts = '<br><b>Добавить расходники для картриджа!(' . $this->config->item('material_amount_need') . ')</b>';

                if ($cn != $stage->cart_num) {

                    $contacter = str_replace('&nbsp;', ' ', $stage->contacter);
                    $contacter = trim($contacter);
                    if ($contacter != '') $contacter .= '. ';
                    $stage_time = '';


                    //время нахождения картриджа глобально от входа в офис
                    $result = $this->cartridge->timeline($stage->order_id, $stage->cart_num)->result();
                    $global_time = date('d/M H:i', $result[0]->date_start) . ' | <b>' . timespan($result[0]->date_start) . '</b>';

                    //затраченое время на выполнение работы
                    $work_time = timespan($result[0]->date_start, $stage->date_start);
                    $cito = '';
                    if ($stage->sort == -2) $cito .= ' <i class="icon-fire"></i>';
                    $cartridges .= '<tr>
                <td>' . anchor('/orders/view_order/' . $stage->hash, $stage->order_id) . br() . $contacter . ' ' . $stage->org_name . '</td>
                
                <td>' . $stage->adres . '</td>                
                <td>' . anchor('cartridges/cartridge_form/' . $stage->cart_num, $stage->cart_num) . $cito . br() .
                        anchor_popup('/cartridges/item/' . $stage->cart_id, $stage->cart_name) . '</td>
                <td><i><b>' . lang($stage->info) . '</b></i>' . $needparts . '</td>
                <td>' . date('d/M H:i', $result[0]->date_start) . '</td><td>' . timespan($result[0]->date_start) . '</td>';

                    $cartridges .= $to_apprv;


                    $cartridges .= '</tr>';

                    if (!$stage->info || !$stage->adres) $print_flag = true;
                }
                $cn = $stage->cart_num;
            }
            $cartridges .= '<table>';
        }
        echo $MAIN_MENU . $cartridges;

    }


    //вывод картриджей мастеру
    public function cartridg_master_list()
    {

        $stage_code = array('inrfl', 'inrck', 'todgs');
        $stages = $this->systema_model->cartridge_stages(false, $stage_code);

        $print_flag = false;
        if ($stages->num_rows > 0) {
            $cartridges .= '
<button id="refresh_cart_list" type="button" class="btn btn-small" onclick="cart_list();">
                                        <i id="icon-refresh" class="icon-refresh"></i>
                                    </button><table class="table table-condensed table-hover table-bordered">';
            $cartridges .= '<thead><tr><th width=60>Заказ №</th>
                <th width=130>Поступил</th>
                <th width=60>Ячейка</th>
                <th width=100>№ картриджа</th>
                <th width=150>Картридж</th>
                <th width=200>Дії</th>
                <th width=400>Інформація</th>
                <th width="*">Клієнт</th>
                </tr></thead>';
            $cn = '';
            $sid = '';
            $todoflag = 0;

            foreach ($stages->result() as $stage) {
                $parts = $this->cartridge->get_cart_parts($stage->cart_id, 'inrfl')->num_rows();
                $parts += $this->cartridge->get_cart_parts($stage->cart_id, 'inrck')->num_rows();
                if ($parts < $this->config->item('material_amount_need')) continue;
                if ($cn != $stage->cart_num) {
                    $class = '';
                    $style = '';
                    switch ($stage->stage_code) {
                        case 'todgs': //$class='alert-info';
                            $todo = anchor('cartridges/to_apprv_from_master/' . $stage->order_id . '/' . $stage->cart_num . '/nodef', 'Нет дефектов');
                            $todo .= ' | ' . anchor('cartridges/to_apprv_from_master/' . $stage->order_id . '/' . $stage->cart_num . '/hvdef', 'Есть дефекты');

                            break;
                        case 'inrfl': //$class='alert-success';
                            $todo = anchor('cartridges/cartridge_work/' . $stage->order_id . '/' . $stage->cart_num, $stage->stage_name);

                            break;
                        case 'inrck': //$class='alert-success';
                            $todo = anchor('cartridges/cartridge_work/' . $stage->order_id . '/' . $stage->cart_num, $stage->stage_name);

                            break;
                    }
                    if ($stage->sort == -4321) {
                        $class = '';
                        $style = 'style="background-color:red; color:white;"';
                    }
                    $result = $this->messages->get_cart($stage->order_id, $stage->cart_num, '');
                    $info = "";
                    foreach ($result->result() as $message) {
                        $info .= $message->text;
                        $info .= '; ';
                    }
                    $result = $this->cartridge->timeline($stage->order_id, $stage->cart_num)->result();
                    $timestamp = $result[0]->date_start;
                    $cartridges .= '<tr class="' . $class . '" >
                <td>' . $stage->order_id . '</td>
                <td>' . date('d M Y H:i', $timestamp) . '</td>
                <td '.$style.'>' . $stage->adres . '</td>
                <td>' . $stage->cart_num . '</td>
                <td>' . $stage->cart_name . '</td>
                <td>' . $todo . '</td>
                <td>' . $info . ' </td>
                <td width="*">'.$stage->org_name.'</td>
                </tr>';
                    if (!$stage->info || !$stage->adres) $print_flag = true;
                }
                $cn = $stage->cart_num;
                $todoflag++;
            }

            $cartridges .= '<table>';
            echo $cartridges;
        }

    }

    //вывод карточки картриджа с историей "болезни" и редактор
    public function cartridge_form($cart_num = false)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->get_user_menu(anchor('cartridges/register', lang('register_cartridges')) .
                ' | ' . anchor('cartridges/catalog', lang('catalog_cartridges')), 'Картриджы');

            if ($cart_num) {
                $this->data['cart_name'] = array('name' => 'cart_name',
                    'id' => 'cart_name',
                    'type' => 'text',
                    'size' => '7',
                    'onkeyup' => 'autosuggest_cart(this.value);');
                $this->data['org_name'] = array('name' => 'org_name',
                    'id' => 'class_activity',
                    'type' => 'text',
                    'size' => '7',
                    'onkeyup' => 'autosuggest(this.value);');


                $this->data['history'] = $this->systema_model->cartridge_stages_done(false, array('inrck', 'inrfl'), $cart_num, 1, date('U'));
                $this->data['material_history'] = $this->cartridge->material_history($cart_num);

                $this->data['cartridge'] = $this->systema_model->get_cartridge($cart_num)->row();
                $this->data['title'] = 'Картридж ' . $cart_num;
                $this->data['cart_num'] = $cart_num;


                $this->load->view('header', $this->data);
                $this->load->view('user_menu', $this->data);
                $this->load->view('cartridges/form', $this->data);
                $this->load->view('bottom', $this->data);
            }
        }
    }


    //вывод картриджа в панели мастера для выполнения работ
    public function cartridge_work($order_id, $cart_num)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->data['history'] = $this->systema_model->cartridge_stages_done(false, array('inrck', 'inrfl'), $cart_num, 1, date('U'));

            $this->data['refill'] = '';
            $this->data['recikl'] = '';
            $this->data['refill_parts'] = '';
            $this->data['recikl_parts'] = '';
            $refill = $this->systema_model->cartridge_stages($order_id, array('inrfl', 'apprv'), $cart_num);
            if ($refill->num_rows() > 0) {
                $this->data['refill'] = $refill;
                $this->data['recikl'] = $refill;
                $this->data['refill_parts2'] = $this->cartridge->get_cart_parts($refill->row()->cart_id, 'inrfl');
            }
            $recikl = $this->systema_model->cartridge_stages($order_id, array('inrck', 'apprv'), $cart_num);
            if ($recikl->num_rows() > 0) {
                $this->data['recikl'] = $recikl;
                $this->data['recikl_parts2'] = $this->cartridge->get_cart_parts($recikl->row()->cart_id, 'inrck');
            }
            $this->data['material_history'] = $this->cartridge->material_history($cart_num);

            $result = $this->messages->get_cart($order_id, $cart_num, '');
            foreach ($result->result() as $message) {
                $this->data['info'] .= $message->text;
                $this->data['info'] .= '; ';
            }

            $this->data['title'] = 'Работа';
            $this->data['order_id'] = $order_id;
            $this->load->view('header', $this->data);
            $this->load->view('cartridges/to_work2', $this->data);
            $this->load->view('bottom', $this->data);

        }


    }


    //закрытие работ по картриджам мастером, если не было проблем с заправкой или восстановлением
    public function done2($cart_num, $order_id)
    {
//если была заправка и все ок

        if ($this->input->post('refill')) {
            //закрыть заправку
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, false, date('U'), 'done', false, $this->ion_auth->user()->row()->id);
        }
        if (!$this->input->post('refill')) {
            //если не было заправки то пише что не было
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, false, false, false, false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, false, false, 'reciklonly', false, $this->ion_auth->user()->row()->id);

        }

        //если было восстановление
        if ($this->input->post('recikl') == '1') {
            //закрыть восстановление
            if ($this->input->post('partnotneed') == 'on') {
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, 'setnull', 'setnull', 'notneed', false, $this->ion_auth->user()->row()->id);
            } else {
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, false, date('U'), false, false, $this->ion_auth->user()->row()->id);
            }
        }
        //закрыть посттест
        $this->systema_model->update_cartridge_stage('totst', $order_id, $cart_num, date('U'), date('U'), 'done', false, $this->ion_auth->user()->row()->id);
        //открыть на упаковку
        $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, date('U'), false, 'needpck', false, $this->ion_auth->user()->row()->id);
        // $this->systema_model->update_cartridge_sort($cart_num,1111111);


        //скрипт всегда явно указывает что восстановления не было
        if ($this->input->post('recikl') == '0') {
            //помечаем этап восстановления что он не понадобился
            $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, 'setnull', 'setnull', 'notneed', false, $this->ion_auth->user()->row()->id);
        }

        //внести в журнал материалы которые применялись в заправке или восстановлении

        foreach ($this->input->post() as $key => $value) {
            if ($key == 'refill' || $key == 'recikl' || $key == 'partnotneed') {

            } else {


                echo $key . ' ' . $value . '<br>';
                $tovar = explode('-', $key);
                //date 	item_id 	amount 	order_id 	uniq_num 	user_id
                $data = array("date" => date('U'),
                    "item_id" => $tovar[0],
                    "amount" => $tovar[1] * -1,
                    "order_id" => $order_id,
                    "uniq_num" => $cart_num,
                    "user_id" => $this->ion_auth->user()->row()->id);
                if ($this->store->insert_journal($data)) {
                    $item = $this->store->get_item($tovar[0])->row();
                    echo $item_data['available'] = $item->available - $tovar[1];
                    $this->store->update_item($tovar[0], $item_data);
                }

            }

        }
        //redirect('/cartridges/to_pack/'.$order_id.'/'.$cart_num.'/from_master', 'refresh');
        redirect('/master_cartridge', 'refresh');
    }


    //если после заправки проявились дефекты
    public function not_done($cart_num, $order_id, $from = false)
    {
        $defects = array('opc' => 'дефект фотобарабана',
            'pcr' => 'дефект ВПЗ',
            'magrol' => 'дефект магвала',
            'wiper' => 'дефект ракеля',
            'doctor' => 'дефект дозлезвия',
            'patron' => 'нужен ремонт или замена корпуса',
            'other' => 'дефект ХЗ',
            'light' => 'Светлая печать',
            'wrap' => 'Серый фон',
            'dots' => 'Точки',
            'vline' => 'Вертикальные линии',
            'hline' => 'Горизотальные линии',
            'impossible' => 'невозможно сделать',
            'cartridgefull' => 'Картридж полный');
        if ($from) {
            echo 'from_admin';

            //обновить информацию в этапы тест после заправки
            $this->systema_model->update_cartridge_stage('totst', $order_id, $cart_num, date('U'), date('U'),
                'otk_failed', false, $this->ion_auth->user()->row()->id);

            //открыть апрув с признаком дефекты после заправки
            $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, date('U'), 'setnull',
                'otk_failed', false, $this->ion_auth->user()->row()->id);

            //обнулить упаковку
            $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, 'setnull', 'setnull',
                '', false, $this->ion_auth->user()->row()->id);
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
            return true;
        }

        $msg = array();
        if ($this->input->post('refill') == '1') {
            //установить заправку в ноль
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, 'setnull', 'setnull',
                'needrecikl', false, $this->ion_auth->user()->row()->id);

            foreach ($this->input->post() as $key => $value) {
                if ($key == 'recikl' || $key == 'refill') true;
                else {
                    $msg['order_id'] = $order_id;
                    $msg['uniq_num'] = $cart_num;
                    $msg['stage_code'] = 'refill';
                    $msg['text'] .= $defects[$key] . '; ';
                    $msg['user_id'] = $this->ion_auth->user()->row()->id;
                    $msg['add_date'] = date('U');
                }
            }
        }
        if ($this->input->post('recikl') == '1') {
            //установить заправку и восстановление в ноль
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, 'setnull', 'setnull',
                'needrecikl', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, 'setnull', 'setnull',
                'needrecikl', false, $this->ion_auth->user()->row()->id);

            foreach ($this->input->post() as $key => $value) {
                if ($key == 'recikl' || $key == 'refill') true;
                elseif (!$msg['order_id']) {
                    $msg['order_id'] = $order_id;
                    $msg['uniq_num'] = $cart_num;
                    $msg['stage_code'] = 'recikl';
                    $msg['text'] .= $defects[$key] . '; ';
                    $msg['user_id'] = $this->ion_auth->user()->row()->id;
                    $msg['add_date'] = date('U');
                }
            }
        }

        //обновить информацию в этапи тест после заправки
        $this->systema_model->update_cartridge_stage('totst', $order_id, $cart_num, date('U'), date('U'),
            $this->input->post('problem'), false, $this->ion_auth->user()->row()->id);


        //открыть апрув с признаком дефекты после заправки
        $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, date('U'), 'setnull',
            'hvdef', false, $this->ion_auth->user()->row()->id);

        /*$Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
                print_r($Slack->call('chat.postMessage', array(
                            'channel' => '#confirm',
                            //'as_user'=>'true',
                            //'username'=>'U0AM6U5T5',
                            'text' => 'Согласовать картридж *'.$cart_num.'* в заказе *_№'.$order_id.'_*'
                            )));
          */

        print_r($msg);
        if ($this->messages->add_message($msg)) redirect('/master_cartridge', 'refresh');
        else echo 'some error';
    }

    //картридж упакован и ставится на выдачу
    public function to_dsp($cart_num, $order_id, $adres)
    {
        if ($adres) {
            //закрыть упаковку
            $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, false, date('U'),
                'done', $adres, $this->ion_auth->user()->row()->id);
            //открыть выдачу
            $this->systema_model->update_cartridge_stage('todsp', $order_id, $cart_num, date('U'), false,
                'wantdsp', $adres, $this->ion_auth->user()->row()->id);

        }
        // redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //картридж выдан
    public function wait_client($cart_num, $order_id)
    {
        //закрыть выдачу
        $result = $this->systema_model->update_cartridge_stage('todsp', $order_id, $cart_num, false, date('U'),
            'done', false, $this->ion_auth->user()->row()->id);

       // redirect($_SERVER['HTTP_REFERER'], 'refresh');

    }

    /*функция сортировки списка карриджей у которой обращается жэкуери
    privat function sort()
    {
        foreach (explode(',', $this->input->post('ids')) as $n => $id) {
            $this->systema_model->update_cartridge_sort($id, $n);
        }
    }*/


    public function set_cart_sort($cart_num, $sort, $order_id)
    {

        $this->systema_model->update_cartridge_sort($cart_num, $sort);
   //     redirect($_SERVER['HTTP_REFERER'], 'refresh');

    }


    //вывод информации о готовых картриджах (что делалось) в заказ
    public function get_done_cartridg($order_id = false, $org_id = false, $paymethod = false, $discount = 0)
    {
        $cartridges = $this->systema_model->cartridge_stages_done($order_id, array('inrfl', 'inrck'));
        $response = '<b>Выполненные работы по картриджам</b>  <a href="/orders/without_cart/' . $order_id . '">Без картриджей</a>';
        if ($cartridges->num_rows() > 0) {
            $response = '<table class="table table-condensed table-hover table-bordered" width="100%" id="done-list">
            <thead><tr>
            <th width="20"></th>
            <th width="70">№</th>
            <th width="200">Модель</th>
            <th width="300">Сделано</th>
            <th width="50">Цена</th></thead></tr>';

            $cartridge = "";
            $cn = '';
            $num = 1;
            $sum_price = 0;
            $nacenka = 0;
            $discount ? $discountD = $discount - 1 : $discountD = 0;

            if ($paymethod == 'bnltov' && $org_id != 11) $nacenka = 0;

            foreach ($cartridges->result() as $cartridge) {
                $corrtext = '';
                if ($cn != $cartridge->cart_num) {
                    $priceInd = $this->systema_model->get_individual_cart_price($cartridge->id, $org_id, $cartridge->stage_code);
                    if ($priceInd->num_rows() > 0)
                    {
                        if ($paymethod == 'subscr')
                        {
                            $price = 0;
                        }
                        else
                        {
                            //$price=round($priceInd->row()->price*(100-$discountD+$nacenka)/100);
                            $nacenka ? $price = $priceInd->row()->price * (100 - $discountD + $nacenka) / 100 : $price = round($priceInd->row()->price * (100 - $discountD + $nacenka) / 100);
                        }
                    }
                    else
                    {
                        $price = false;
                        if ($paymethod == 'subscr')
                        {
                            $price = 0;
                        }
                        else
                        {
                            if ($cartridge->stage_code == "inrfl")
                            {
                                $nacenka ? $price = $cartridge->cena_zapravki * (100 - $discount + $nacenka) / 100 : $price = round($price = $cartridge->cena_zapravki * (100 - $discount + $nacenka) / 100);
                            }

                            if ($cartridge->stage_code == "inrck")
                            {
                                $nacenka ? $price = $cartridge->cena_vostanovlenia * (100 - $discount + $nacenka) / 100 : $price = round($cartridge->cena_vostanovlenia * (100 - $discount + $nacenka) / 100);
                            }
                        }
                    }

                    $response .= '<tr><td>' . $num . '</td>
            <td>' . anchor_popup('cartridges/cartridge_form/' . $cartridge->cart_num, $cartridge->cart_num) . '</td>
            <td>' . $cartridge->name . '</td>
            <td>' . lang($cartridge->stage_code) . $corrtext . '</td>
            <td>' . $price . '</td></tr>';

                    $invoice_item = $this->systema_model->select_invoice_item($cartridge->cart_num, $order_id);
                    if (!$invoice_item->num_rows()) {
                        $text = lang('ukrainian_' . $cartridge->stage_code) . $corrtext . ' картриджа ' . $cartridge->name;
                        $this->systema_model->insert_invoice_item($cartridge->cart_num, $order_id, $text, $price);
                    }
                    if ($invoice_item->num_rows() > 0) {
                        //$text=lang($cartridge->stage_code).$corrtext.' картриджа '.$cartridge->name;
                        $data = array('price' => $price);
                        $this->systema_model->update_invoice_item($invoice_item->row()->id, $data);
                    }

                    $sum_price += $price;
                    $num++;
                }
                $cn = $cartridge->cart_num;

            }
            $response .= '<tr style="text-align:right; font: bold 16px verdana;">
            <td colspan="4" style="text-align: right;">Всего: </td>
            <td>' . $sum_price . '</td></tr></table>';
        }
        echo $response;
    }


    public function print_cartridg_stages($order_id = false)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {

            $this->load->view('header', $this->data);
            $this->view_cartridg_stages($order_id, 1);
            $this->load->view('bottom', $this->data);

        }


    }


    // вывод картриджей в заказ
    public function view_cartridg_stages($order_id = false, $print_view = false)
    {

        $stages = $this->systema_model->cartridge_stages($order_id);

        $print_flag = false;

        if ($print_view == 1) {
            if ($stages->num_rows() > 0) {
                $cartridges = "";
                $cn = '';
                $cartridges .= '
<table class="table table-condensed table-bordered table-striped" width="100%"><thead>
<tr>
<th width="20"></th>
<th width="175">№/Модель</th>
<th width="300">Информация</th>
<th width="200">Действия</th>
<th width="*"></th></tr></thead>';
                $counter = 1;
                $todsp_flag = false;
                foreach ($stages->result() as $stage) {
                    $price = '';
                    if ($cn != $stage->cart_num) {

                        $cartridges .= '<tr><td>' . $counter . '</td>
                
                <td>' . $stage->cart_num . ' - ' . $stage->cart_name . '</td>';

                        $to_apprv = '<td></td>';

                        $info = '';
                        $result = $this->messages->get_cart($order_id, $stage->cart_num, '');
                        foreach ($result->result() as $message) {
                            $info .= $message->text;
                            $info .= '; ';
                        }

                        $cartridges .= '<td>' . $info . '</td>';


                        $cartridges .= $to_apprv;
                        $cartridges .= '<td>' . $price . '</td>';
                        $cartridges .= '</tr>';

                        if (!$stage->info || !$stage->adres) $print_flag = true;

                    }
                    $cn = $stage->cart_num;
                    $counter++;
                }
                if ($todsp_flag) {
                    $cartridges .= '<tr><td colspan="5"></td><td>
                    <button id="wait_client" class="btn btn-info" onclick="wait_client(\'0\');">Доставлено все</button>
                        </td></tr>';
                }
                $cartridges .= '</table><div id="answer">';


                echo $cartridges;
            }

        } else {
            if ($stages->num_rows() > 0) {

                $search_arr=array('Astron','inprinter');
                $replace_arr=array('Астрон','В принтере');
                $cartridges = "";
                $cn = '';
                $cartridges .= '
 <table class="table table-condensed table-bordered table-striped" width="100%"><thead>
<tr>
<th width="20"></th>
<th width="175">№/Модель</th>
<th width="65" align="centre">Ячейка</th>
<th width="300">Інформація</th>
<th  width="120">Пріорітет</th>
<th width="200">Дії</th>
<th width="*"></th></tr></thead>';
                $counter = 1;
                $todsp_flag = false;
                foreach ($stages->result() as $stage) {
                    $price = '';
                    if ($cn != $stage->cart_num) {

                        $cartridges .= '<tr><td>' . $counter . '</td>
                
                <td>' . anchor_popup('cartridges/cartridge_form/' . $stage->cart_num, $stage->cart_num) .
                            ' | '
                            . '<!-- Button to trigger modal -->
<a href="#move_to_order' . $stage->cart_num . '" role="button" class="btn" data-toggle="modal">Переместить</a>
<!-- Modal -->
<div id="move_to_order' . $stage->cart_num . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Перемещение картриджа в другой заказ</h3>
  </div>' . form_open('cartridges/move_to_order/' . $order_id . '/' . $stage->cart_num) .
                            '<div class="modal-body">
    <p>Переместить картридж ' . $stage->cart_num . ' из заказа № ' . $order_id . '</p>
    в заказ <input size="10" type="input" name="to_order" placeholder="номер заказа">
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
    <button class="btn btn-primary">Переместить</button>
  </div>
  </form>
</div>
' .

                            br() . $stage->cart_name . '</td>
                <td>' . str_replace($search_arr,$replace_arr,$stage->adres) . '</td>';
                        $to_apprv = '<td></td>';
                        if ($stage->info == 'hvdef' || $stage->info == 'defaf' || $stage->info == 'needrecikl'
                            || $stage->info == 'fullwodef' || $stage->info == 'handspenises'
                            || $stage->info == 'otk_failed'
                        ) {
                            $to_apprv = '<td>
            <div class="btn-group">        
                     <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                Що робити
            </a>
            
            <ul class="dropdown-menu">
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'refill\');">Заправка</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'narefill\');">Заправка(без согласования)</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'refillonly\');">Только заправка(без востановления)</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'reckl\');">Восстановление</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'nareckl\');">Восстановление(без согласования)</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'recklonly\');">Восстановление (без заправки)</a></li>
                <li><a onclick="client_answer(\'' . $stage->cart_num . '\',\'stop\');">Ничего не делать</a></li>
            </ul>
            </div>
                    </td>';

                            $prices = $this->systema_model->cartridge_stages_done($order_id, false, $stage->cart_num);
                            $price = 'Заправка: ' . $prices->row()->cena_zapravki . '; Восстановление: ' . $prices->row()->cena_vostanovlenia;
                        }

                        if ($stage->stage_code == 'topck')
                        {
                            $attrib = array(
                                'class' => 'btn btn-small btn-info',
                                'style' => 'color:white',
                                'width' => '600',
                                'height' => '650',
                                'scrollbars' => 'no',
                                'status' => 'no',
                                'resizable' => 'no',
                                'screenx' => '250',
                                'screeny' => '10');
                            $to_apprv = '<td><div class="btn-group">
<div class="btn-group">
            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-large icon-gift"></i></span>
            </a>
            
            <div class="dropdown-menu">
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'1\');">1</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'2\');">2</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'3\');">3</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'4\');">4</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'5\');">5</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'6\');">6</a>
            <br>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'7\');">7</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'8\');">8</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'9\');">9</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'10\');">10</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'11\');">11</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'13\');">13</a>
            <br>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-0\');">-0</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-1\');">-1</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-2\');">-2</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-3\');">-3</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-4\');">-4</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'-5\');">-5</a>
            <br>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'413\');">413</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'Astron\');">Астрон</a>
            <a class="btn btn-small" href="#" onclick="to_dsp(\'' . $stage->cart_num . '\',\'inprinter\');">В принтере</a>
           
            </div>
        </div>';
                            $to_apprv .=
                                anchor_popup('cartridges/to_pack/' . $order_id . '/' . $stage->cart_num, '<i class="icon-large icon-print"></i>', $attrib) .
                                '<a class="btn btn-small btn-danger" href="/cartridges/not_done/' . $stage->cart_num . '/' . $order_id . '/1" title="Вернуть">
                       <i class="icon-large icon-retweet"></i></a></div>';

                        }
                        if ($stage->stage_code == 'todsp')
                        {
                            $to_apprv = '<td> 
<button id="wait_client" class="btn btn-info" onclick="wait_client(\'' . $stage->cart_num . '\');">Доставлен</button></td>';

                            $todsp_flag = true;
                        }

                        if ($stage->sort == -5321) $cito = '<b class="alert-error">Магазин</b>';
                        if ($stage->sort == -4321) $cito = '<b class="alert-error">ПКС!</b>';
                        if ($stage->sort == -2) $cito = '<b>CРОЧНО!</b>';
                        if ($stage->sort == -1) $cito = '<b>Не срочно</b>';
                        if ($stage->sort > 0) $cito = '<b>Не срочно</b>';

                        $cito_menu='
                        <div class="btn-group">
            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                '.$cito.'
            </a>
            
            <ul class="dropdown-menu">
                <li><a href="#" onclick="set_cart_sort(\'' . $stage->cart_num . '\',\'-2\');">Срочно</a></li>
                <li><a href="#" onclick="set_cart_sort(\'' . $stage->cart_num . '\',\'-1\');">Не срочно</a></li>
                <li><a href="#" class="alert-error" onclick="set_cart_sort(\'' . $stage->cart_num . '\',\'-5321\');">Магазин</a></li>
                <li><a href="#" class="alert-error" onclick="set_cart_sort(\'' . $stage->cart_num . '\',\'-4321\');">ПКС</a></li>
                
            </ul>
            </div>
                        
                        ';

                        $info = '';
                        $result = $this->messages->get_cart($order_id, $stage->cart_num, '');
                        foreach ($result->result() as $message) {
                            $info .= $message->text;
                            if ($this->ion_auth->is_admin()) $info .= '<a href="/messages/hide_mess/' . $message->id . '"> - </a>';
                            $info .= '; ';
                        }

                        $atts_add_mess = array(
                            'width' => '500',
                            'height' => '250',
                            'scrollbars' => 'no',
                            'status' => 'no',
                            'resizable' => 'no',
                            'screenx' => '250',
                            'screeny' => '100');


                        $cartridges .= '<td>' . lang($stage->info) . ': ' . $info . nbs(3) . anchor_popup('/cartridges/mess_to_cart/' . $order_id . '/' . $stage->cart_num, '+', $atts_add_mess) . '</td>';
                        //         $cartridges.='<td id='.$stage->stage_code.'>'.lang($stage->info).' ('.lang($stage->stage_code).')</td>';
                        $cartridges .= '<td>' . $cito_menu . '</td>';
                        $cartridges .= $to_apprv;
                        $cartridges .= '<td>' . $price . '</td>';
                        $cartridges .= '</tr>';

                        if (!$stage->info || !$stage->adres) $print_flag = true;

                    }
                    $cn = $stage->cart_num;
                    $counter++;
                }
                if ($todsp_flag) {
                    $cartridges .= '<tr><td colspan="5"></td><td>
                    <button id="wait_client" class="btn btn-info" onclick="wait_client(\'0\');">Доставлено все</button>
                        </td></tr>';
                }
                $cartridges .= '</table><div id="answer">';


                echo $cartridges;
            }
            // end

        }


    }

    public function move_to_order($from_order, $cart_num)
    {
        if ($this->ion_auth->logged_in()) {
            echo 'Move cartridge ' . $cart_num . ' from order # ' . $from_order . ' to order ' . $this->input->post('to_order');
            $this->cartridge->move_to_order($cart_num, $from_order, $this->input->post('to_order'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
    }

    public function mess_to_cart($order_id, $cart_num)
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['order_id'] = $order_id;
            $this->data['cart_num'] = $cart_num;
            $this->load->view('header', $this->data);
            $this->load->view('cartridges/mess_to_cart', $this->data);
            $this->load->view('bottom', $this->data);
        } else {
            echo 'Access denied';
        }

    }

    // вывод листа диагностики картриджей на печать
    public function to_test($order_id, $cart_num = false)
    {
        //переводим картриджи на следующий этап
        $this->systema_model->update_cartridge_stage('inofc', $order_id, $cart_num, false, date('U'), false, false, false);
        $this->systema_model->update_cartridge_stage('todgs', $order_id, $cart_num, date('U'), false, false, false, $this->ion_auth->user()->row()->id);


        $result = $this->systema_model->update_stage_orders('prewrk', $order_id, 1, FALSE, date('U'), 'ok')->row();
        $this->systema_model->update_stage_orders($result->stage_code, $order_id, 1, date('U'), FALSE);


        $this->data['cartridges'] = $this->systema_model->cartridge_stages($order_id, 'todgs')->result();
        $this->data['order_id'] = $order_id;

        $this->load->view('header', $this->data);
        $this->load->view('cartridges/print_diag_list', $this->data);
        $this->load->view('bottom', $this->data);
    }

    // вывод листа диагностики картриджей на печать
    public function to_pack($order_id, $cart_num = false, $param = false)
    {

        $this->data['cart_in_order'] = $this->systema_model->cartridge_stages($order_id, array('inofc', 'todgs', 'apprv', 'inrck', 'inrfl', 'topck', 'todsp'))->num_rows();
        $this->data['cartridges'] = $this->systema_model->cartridge_stages($order_id, 'topck', $cart_num);
        $this->data['order_id'] = $order_id;
        $this->data['param'] = $param;

        $this->load->view('header', $this->data);
        $this->load->view('cartridges/print_pack_list', $this->data);
        $this->load->view('bottom', $this->data);
    }

    public function client_answer($cart_num, $order_id, $answer)
    {
        //проверяем какой был ответ пользователя и ставим картридж на необходимые этапы
        switch ($answer) {
            // заправка
            case 'refill':
                //закрываем этап согласования
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'answered', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо апрув
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), 'setnull',
                    'approved', false, $this->ion_auth->user()->row()->id);
                break;

            //Заправить(без согласования)
            case 'narefill':
                //закрываем этап согласования
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'noanswer', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо нотапрув
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), 'setnull',
                    'notapproved', false, $this->ion_auth->user()->row()->id);
                break;

            //Только заправка
            case 'refillonly':
                //закрываем этап согласования
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'answered', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо апрув
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), 'setnull',
                    'refillonly', false, $this->ion_auth->user()->row()->id);
                //установить страт дату и стоп дату восстановления в 0 и инфо stop
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, 'setnull', 'setnull',
                    'stop', false, $this->ion_auth->user()->row()->id);
                break;

            //Восстанавливать
            case 'reckl':
                //закрываем этап согласования инфо апрувед
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'answered', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо апрув
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), 'setnull',
                    'approved', false, $this->ion_auth->user()->row()->id);
                //установить старт дату восстановления и инфо апрув
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, date('U'), 'setnull',
                    'approved', false, $this->ion_auth->user()->row()->id);
                break;
            //Восстанавливать(без согласования)
            case 'nareckl':
                //закрываем этап согласования
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'noanswer', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо нотапрув
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), 'setnull',
                    'notapproved', false, $this->ion_auth->user()->row()->id);
                //установить старт дату восстановления и инфо нотапрув
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, date('U'), 'setnull',
                    'notapproved', false, $this->ion_auth->user()->row()->id);

                break;

            //Только восстановление, без заправки
            case 'recklonly':
                //закрываем этап согласования инфо апрувед
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'answered', false, $this->ion_auth->user()->row()->id);
                //установить старт дату заправки и инфо нотнид
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, 'setnull', 'setnull',
                    'notneed', false, $this->ion_auth->user()->row()->id);
                //установить старт дату восстановления и инфо апрув
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, date('U'), 'setnull',
                    'reciklonly', false, $this->ion_auth->user()->row()->id);
                break;

            //Не восстанавливать
            case 'stop':
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'),
                    'answered', false, $this->ion_auth->user()->row()->id);
                //установить старт дату и стоп дату заправки в 0 и инфо stop
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, 'setnull', 'setnull',
                    'stop', false, $this->ion_auth->user()->row()->id);
                //установить старт дату восстановления и стоп дату в 0 и инфо stop
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, 'setnull', 'setnull',
                    'stop', false, $this->ion_auth->user()->row()->id);
                //установить старт дату на упаковку и инфо stop
                $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, date('U'), 'setnull',
                    'stop', false, $this->ion_auth->user()->row()->id);
                break;
        }

        //redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function to_apprv_from_master($order_id, $cart_num, $deffect)
    {
        if ($deffect == 'nodef') {
            $this->systema_model->update_cartridge_stage('todgs', $order_id, $cart_num, false, date('U'), 'nodef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, date('U'), date('U'), 'answered', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), false, 'approved', false, $this->ion_auth->user()->row()->id);
            redirect('cartridges/cartridge_work/' . $order_id . '/' . $cart_num, 'refresh');
        }
        if ($deffect == 'hvdef') {
            $this->systema_model->update_cartridge_stage('todgs', $order_id, $cart_num, false, date('U'), 'hvdef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, date('U'), false, 'hvdef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, false, false, 'hvdef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, false, false, 'hvdef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('totst', $order_id, $cart_num, false, false, 'hvdef', false, $this->ion_auth->user()->row()->id);
            $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, false, false, 'hvdef', false, $this->ion_auth->user()->row()->id);

            /*$Slack = new Slack('xoxp-10727929426-10720957923-12143294610-dcee83f184');
                print_r($Slack->call('chat.postMessage', array(
                            'channel' => '#confirm',
                            //'as_user'=>'true',
                            //'username'=>'U0AM6U5T5',
                            'text' => 'Согласовать картридж *'.$cart_num.'* в заказе *_№'.$order_id.'_*'
                            )));
              */

            redirect('cartridges/cartridge_work/' . $order_id . '/' . $cart_num, 'refresh');
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }


    //запись состояния картриджей по листу диагностики
    public function to_apprv($order_id)
    {
        $flag = 0;
        $prohod = 0;
        //парсинг формы диагностики
        foreach ($this->input->post() as $param => $value) {
            if ($param != 'submit') {
                $p = explode('_', $param);
                if ($p[0] == 'defect' && $value) {
                    $cart_num = $p[1];
                    $masiv[$cart_num]['defect'] = $value;
                }
                if ($p[0] == 'adres' && $value) {
                    $cart_num = $p[1];
                    $masiv[$cart_num]['adres'] = $value;
                }
            }
        }
        //поочердно записываем картридж (этапы)
        foreach ($masiv as $cart_num => $stag_param) {
            if ($this->systema_model->check_cart_adres($masiv[$cart_num]['adres']) > 0) $masiv[$cart_num]['adres'] = FALSE;
            echo $masiv[$cart_num]['adres'];
            //записываем если зполнены поля адрес и отмечен дефект
            if ($masiv[$cart_num]['adres'] && $masiv[$cart_num]['defect']) {
                $this->systema_model->update_cartridge_stage('todgs', $order_id, $cart_num, false, date('U'), $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, date('U'), false, $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, false, false, $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                $this->systema_model->update_cartridge_stage('inrck', $order_id, $cart_num, false, false, $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                $this->systema_model->update_cartridge_stage('totst', $order_id, $cart_num, false, false, $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                $this->systema_model->update_cartridge_stage('topck', $order_id, $cart_num, false, false, $masiv[$cart_num]['defect'], $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                if ($masiv[$cart_num]['defect'] == 'nodef') {
                    $this->systema_model->update_cartridge_stage('apprv', $order_id, $cart_num, false, date('U'), 'answered', $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                    $this->systema_model->update_cartridge_stage('inrfl', $order_id, $cart_num, date('U'), false, 'approved', $masiv[$cart_num]['adres'], $this->ion_auth->user()->row()->id);
                }
                // if($masiv[$cart_num]['defect']=='hvdef') $this->systema_model->update_stage_orders('inwrk', $order_id,1,false,false,'needapprove');
            }
        }
        //переводим заказ на этап "работа"
        $result = $this->systema_model->update_stage_orders('prewrk', $order_id, 1, FALSE, date('U'), 'ok')->row();
        $this->systema_model->update_stage_orders($result->stage_code, $order_id, 1, date('U'), FALSE);


        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function cart_list($for_store = false)
    {
        if ($for_store) {
            $search = explode('#', $this->input->post('str'));

            $cartridges = $this->store->rel_item_by_printer($search[0], explode('-', $search[3]));
            if ($cartridges->num_rows() > 0) {
                foreach ($cartridges->result() as $cart) {
                    echo '<a href="/cartridges/add_material/' . $search[1] . '/' . $cart->cart_id . '/inrfl" class="btn btn-mini btn-success">ЗП</a>' . nbs();
                    echo '<a href="/cartridges/add_material/' . $search[1] . '/' . $cart->cart_id . '/inrck" class="btn btn-mini btn-warning">ВС</a>' . nbs();
                    echo $cart->cart_name . '(' . $cart->brand_name . ' ' . $cart->printer_name . ')' . br();
                    //echo '<a href="/cartridges/add_material/'.$search[1].'/'.$cart->cart_id.'/'.$search[2].'" class="btn" style="width:250px;text-align:left;">'
                    //.$cart->cart_name.'('.$cart->brand_name.' '.$cart->printer_name.')</a>'.br();

                }
            } else {
                //   echo $this->input->post('str');
            }
        } else {
            $name = addslashes($this->input->post('str'));
            $name = trim($name);

            $cartridges = $this->systema_model->select_cartridges('cart_name', $name);

            echo "<SELECT name='cart_id'>";
            foreach ($cartridges->result() as $cartridge) {
                echo '<option value=' . $cartridge->cart_id . '>' . $cartridge->cart_name . ' (' .
                    $cartridge->brand_name . ' ' . $cartridge->printer_name . ')</option>';
            }
            echo "</SELECT>";
        }
    }

    public function get_cartridge()
    {

        $uniq_num = addslashes($this->input->post('str'));
        $result = $uniq_num = trim($uniq_num);
        $cartridge = $this->systema_model->get_cartridge($uniq_num);
        if ($cartridge->num_rows() > 0) {
            $result = $cartridge->row()->name . '<input type="hidden" name="cart_name" value="' . $cartridge->row()->name . '">';
            $result .= '<input type="hidden" name="cart_id" value="' . $cartridge->row()->name_id . '">';
        } else {
            $cart_name = array('name' => 'cart_name',
                'id' => 'cart_name',
                'type' => 'text',
                'size' => '10',
                'onkeyup' => 'autosuggest_cart(this.value);',
                'value' => $this->form_validation->set_value('cart_name'));
            $result = form_input($cart_name);

        }

        echo $result;

    }

    public function change_cartridge_num($old_cart_num)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);

            if ($this->ion_auth->is_admin()) {
                echo $old_cart_num . '->' . strtoupper($this->input->post('cart_num'));
                if ($this->input->post('cart_num')) //если номер введен
                {
                    // проверить если в базе такой номер
                    // если в базе есть такой номер
                    // выводим сообщение "такой номер уже есть"

                    //если в базе номера нету такого
                    /* меняем номера в таблицах:
                     * cartdige_stages -> cart_num
                     * invoices -> uniq_num
                     * jurnal -> cart_num
                     * messages -> uniq_num
                     */

                }
            } else {
                //Менять номер картирджа может только администратор
            }


            //$this->load->view('cartridges/main', $this->data);
            $this->load->view('bottom', $this->data);

        }

    }


    // меняем наименование или организацию картриджа в реестре
    public function change_cartridge_registr($field, $cart_num)
    {
        if ($this->input->post('cart_id')) {
            $full_cart_name = $this->systema_model->get_full_cart_name($this->input->post('cart_id'));
            echo $full_cart_name;
            $data = array('name_id' => $this->input->post('cart_id'),
                'name' => $full_cart_name);
        }
        if ($this->input->post('org_id')) {
            $data = array('org_id' => $this->input->post('org_id'));
        }

        $this->systema_model->registr_cartridge_update_by_num($cart_num, $data);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //вывод карточки картриджа
    public function item($cart_id)
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {

            $this->get_user_menu('Карточка картриджа', 'Карточка картриджа');
            $this->data['cart_item'] = $this->cartridge->get_cart_item($cart_id);
            $this->data['rashodka'] = $this->cartridge->cart_rashodka($cart_id);
            $this->load->view('header', $this->data);
            $this->load->view('user_menu', $this->data);
            $this->load->view('cartridges/item', $this->data);
            $this->load->view('bottom', $this->data);
        }


    }

    //комплектование расходных для картриджа
    public function set_materials($cart_id)
    {

        foreach ($this->input->post() as $material_id => $value) {
            //    echo br().'item:'.$material_id.' val:'.$value.' cart:'.$cart_id;
            $data = array('kolvo' => $value);
            $this->cartridge->update_materials($cart_id, $material_id, $data);
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');

    }

    public function remove_material($cart_id, $material_id)
    {
        $this->cartridge->remove_material($cart_id, $material_id);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');

    }

    public function add_material($material_id, $cart_id, $stage_code)
    {
        $data = array('id_cart' => $cart_id,
            'stage_code' => $stage_code,
            'rashodnik_id' => $material_id,
            'kolvo' => 0);
        $this->cartridge->add_material($data);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');

    }

    // печать доставочного листа
    public function print_delivery_list($order_hash)
    {
        if ($this->ion_auth->logged_in()) {
            if ($order_hash) {

                $this->data['order'] = $this->systema_model->view_order($order_hash)->row();

                $this->data['cartridges'] = $this->systema_model->cartridge_stages_done($this->data['order']->id, array('topck'));

                $this->load->view('cartridges/delivery_list', $this->data);

            } else {
                return FALSE;
            }
        } else {
            return false;
        }
    }

    public function update($id)
    {

        //print_r($this->input->post());
        if ($id == 100500321) {
            foreach ($this->input->post() as $key => $value) {
                $cart_id = explode('-', $key);
                if ($cart_id[0] == 'kurs_usd_nal') continue;

                //echo $cart_id[0].'='.$value.' where '.$cart_id[1].'<br/>';
                $data[$cart_id[0]] = $value;
                $this->cartridge->update_cartridge($data, $cart_id[1]);
            }
        } else {
            foreach ($this->input->post() as $key => $value) {
                echo $key . '=' . $value . '<br/>';
                $data[$key] = $value;
            }
            //$this->cartridge->update_cartridge($data,$id);
        }


        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function set_printer_name()
    {
        /* select cartridge.id, printers.name
from `printers`
join print_join_cart on print_join_cart.printer_id=printers.id
join cartridge on cartridge.id=print_join_cart.cartridge_id
where cartridge.id=1
limit 1*/
    }

//подсчет количества заправок в разрезе наименований картриджей и запись сортировки
    public function set_sort()
    {
        $this->db->select('count(cartridge_stages.id) as count, registr_cartridge.name, registr_cartridge.name_id')
            ->from('cartridge_stages')
            ->join('registr_cartridge', 'registr_cartridge.uniq_num=cartridge_stages.cart_num')
            ->where('stage_code', 'inrfl')
            ->where('date_start >','1483231952')
            ->where('date_end <','1514764799')
            ->group_by('registr_cartridge.name_id')
            ->order_by('count', 'desc');
        $cartridges = $this->db->get();


        foreach ($cartridges->result() as $cart) {
            echo "update cartridge set sort=$cart->count where cartridge.id=$cart->name_id";
            $SORT=$SORT+$cart->count;
            echo br();
            $this->db->where('cartridge.id', $cart->name_id);
            $this->db->update('cartridge', array('sort' => $cart->count));
        }
        echo $SORT/12;
    }


    public function stat($dayly = false, $month = false, $year = false)
    {
        echo '<table><tr><th>month</th><th>all</th><th>privat</th><th>org</th></tr>';
        for ($i = 11; $i <= 12; $i++) {
            $all = $this->cartridge->get_count(mktime(0, 0, 0, $i, 1, 2012), mktime(0, 0, 0, $i + 1, 0, 2012));
            $privat = $this->cartridge->get_count(mktime(0, 0, 0, $i, 1, 2012), mktime(0, 0, 0, $i + 1, 0, 2012), 11);
            $org = $all - $privat;

            echo '<tr><td> ' . $i . '/2012</td><td>' . $all . '</td><td>' . $privat . '</td><td>' . $org . '</td></tr>';
        }
        for ($i = 1; $i <= date('m'); $i++) {
            $all = $this->cartridge->get_count(mktime(0, 0, 0, $i, 1, 2013), mktime(0, 0, 0, $i + 1, 0, 2013));
            $privat = $this->cartridge->get_count(mktime(0, 0, 0, $i, 1, 2013), mktime(0, 0, 0, $i + 1, 0, 2013), 11);
            $org = $all - $privat;

            echo '<tr><td> ' . $i . '/2013</td><td>' . $all . '</td><td>' . $privat . '</td><td>' . $org . '</td></tr>';
        }
        echo '</table>';


        $MONTH_RU = array("null", "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
        $this->data['stat'] = '<table>';
        $m = 0;
        $co = '';
        $REFILLS = 0;
        $RECIKLS = 0;
        $day = 0;

        if ($dayly) {
            $month ? $month = $month : $month = date('m');
            $year ? $in_year = $year : $in_year = date('Y');
            if ($month == date('m')) $until = date('d');
            else $until = date('t', mktime(0, 0, 0, $month));
            echo $until;
            while ($day < $until) {
                $day++;
                $date_start = mktime(0, 0, 0, $month, $day, $in_year);
                $date_end = mktime(23, 59, 59, $month, $day, $in_year);
                $co = '';
                $refill_count = $this->systema_model->cartridge_stages_done(false, array('inrfl'), false, $date_start, $date_end)->num_rows();
                $recikl_count = $this->systema_model->cartridge_stages_done(false, array('inrck'), false, $date_start, $date_end)->num_rows();

                if ($month > 9) {
                    $refill_count = $refill_count - $recikl_count;
                    $sum = $refill_count + $recikl_count;
                } else {
                    $sum = $refill_count + $recikl_count;
                }
                //     if($recikl_count!=0 and $refill_count!=0) $co=$refill_count/$recikl_count;
                $this->data['stat'] .= '<tr><td>' . $day . '</td><td>' . $refill_count . '</td>' .
                    '<td>' . $recikl_count . '</td><td>' . $sum . '</td><td>' . $co . '</td></tr>';

                $REFILLS = $REFILLS + $refill_count;
                $RECIKLS = $RECIKLS + $recikl_count;

            }
            $SUM = $RECIKLS + $REFILLS;
            $this->data['stat'] .= '<tr><td>Всего </td><td>' . $REFILLS . '</td>' .
                '<td>' . $RECIKLS . '</td><td>' . $SUM . '</td><td>' . $co . '</td></tr>';
        } else {
            while ($m < date('m')) {
                $m++;
                $date_start = mktime(0, 0, 0, $m, 1, date('Y'));
                $date_end = mktime(23, 59, 59, $m + 1, 0, date('Y'));
                $co = '';
                $refill_count = $this->systema_model->cartridge_stages_done(false, array('inrfl'), false, $date_start, $date_end)->num_rows();
                $recikl_count = $this->systema_model->cartridge_stages_done(false, array('inrck'), false, $date_start, $date_end)->num_rows();

                if ($m > 9) {
                    $sum = $recikl_count;
                    $recikl_count = 'в т.ч.';
                } else {
                    $sum = $refill_count + $recikl_count;
                }
                //       if($recikl_count!=0 and $refill_count!=0) $co=$refill_count/$recikl_count;
                $this->data['stat'] .= '<tr><td>' . $MONTH_RU[$m] . '</td><td>' . $refill_count . '</td>' .
                    '<td>' . $recikl_count . '</td><td>' . $sum . '</td><td>' . $co . '</td></tr>';
            }
        }

        $this->data['title'] = 'Статистика';
        $this->load->view('header', $this->data);
        $this->load->view('cartridges/stat', $this->data);
        $this->load->view('bottom', $this->data);
    }

    //функция внесения картриджа в заказ
    public function add_cartridge($order_id, $hash)
    {
        //print_r($_POST);
        //print_r($_GET);
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
                echo "Проверяю картридж";
                $result = $this->systema_model->add_cartridge($cart_num, $cart_data);
                echo br() . "Добавляю картридж в работу ";
                //делаем запись что картридж у нас и создаем все этапы под него
                if ($this->systema_model->create_stage($order_id, 'cartridge', 'inofc', date('U'), $this->ion_auth->user()->row()->id, '', $cart_num)) {
                    $this->systema_model->update_stage_orders('prewrk', $order_id, 1, false, false, 'Идет обработка заказа');
                    $stages = $this->systema_model->get_stages('stages_cartridge')->result();
                    foreach ($stages as $stage) {
                        if ($stage->code != 'inofc') {
                            $temp_adres = $this->systema_model->create_stage($order_id, 'cartridge', $stage->code, 0,
                                $this->ion_auth->user()->row()->id, '', $cart_num);
                        }
                        if ($stage->code == 'todgs') $adres = $temp_adres;
                    }
                    echo br() . "Картридж добавлен в работу в ячейку " . $adres . br(2);
                } else {
                    echo br() . "<span class='alert-error'> Ошибка добавления в работу! Картридж не внесен.</span>" . br(2);
                }


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
                    //$this->load->view('header', $this->data);
                    //$this->load->view('orders/add_cartridge', $this->data);
                    //$this->load->view('bottom', $this->data);
                } else {
                    echo "<span class='alert-error'>Нет свободных ячеек! Упакуйте готовые картриджи!</span>";
                }

            }
        } else {
            //redirect('main/login', 'refresh');
        }
    }


}

?>