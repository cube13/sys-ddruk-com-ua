<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fin extends CI_Controller
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
        $this->config->item('use_mongodb', 'ion_auth') ?
            $this->load->library('mongo_db') :
            $this->load->database();
        $this->lang->load('ion_auth', 'russian');
        $this->lang->load('systema', 'russian');

        $this->load->model('systema_fin_model', 'fin');
        $this->load->model('systema_model');
        $this->load->model('messages_model', 'messages');
        $this->load->model('cartridge_model', 'cartridge');

        $this->data['userGroups'] = $this->ion_auth->get_users_groups()->result();

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

    public function index()
    {
        if (!$this->ion_auth->logged_in())
        {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        }
        else
        {
            redirect('fin/issued_orders');
        }

    }

    // печать чека и запись суммы к оплате
    public function print_check($order_id, $paymethod = '')
    {
        if ($this->ion_auth->logged_in()) {
            if ($order_id) {
                $this->data['order_id'] = $order_id;
                $this->data['invoice'] = $invoice = $this->fin->select_invoice($order_id);

                $accounting = $this->systema_model->select_accounting($order_id);
                $this->data['accounting']=$accounting->result();
                if ($accounting->num_rows() > 0) {
                    $check_sum = 0;
                    foreach ($invoice->result() as $price) {
                        $check_sum += $price->price;
                    }
                    if ($check_sum != $accounting->row()->sum) {
                        echo "PRINT ERROR!!!";
                        return false;
                    }

                } else {
                    //$this->data['error_check']=TRUE;
                }

                $this->data['paymethod']=$paymethod;
                $this->load->view('orders/invoice', $this->data);

                $this->systema_model->update_stage_orders('pprdoc', $order_id, 1, FALSE, date('U'), 'ok'); //документы по заказу выписаны. пока чека достаточно
                $this->systema_model->update_stage_orders('ispayd', $order_id, 1, date('U'), false, 'ok'); //документы по заказу выписаны. пока чека достаточно

                //лочим запись в аккаунтинге и обновляем дату выписки чека - возникает долг по оплате
                $date = date('U');
                $accounting_data = array('locked' => '1', 'date' => $date);
                $this->systema_model->update_accounting($order_id, $accounting_data);

            } else {
                return FALSE;
            }
        } else {
            return false;
        }
    }

    // превью чека
    public function invoice_preview($order_id, $org_id = false)
    {
        if ($this->ion_auth->logged_in()) {
            if ($order_id) {
                $preview = '';
                $table = array();

                $invoice = $this->fin->select_invoice($order_id);
                $preview = '<table align="center" rules="all" border="1" style="font: normal 10pt times-new-roman;">
     <tr valign="top"><td width="20" align="center">№</td>
         <td width="450" align="center">Найменування</td>
         <td width="50" align="center">Од. вим.</td>
         <td width="40" align="center">К-сть</td>
         <td width="40" align="center">Ціна</td>
         <td width="50" align="center">Сума</td></tr>';
                $SUMA = 0;
                $n = 0;
                $prev_text = "";
                $prev_num = "";
                $count = 1;
                $nums = "";
                foreach ($invoice->result() as $invoice_item) {
                    if ($invoice_item->text == $prev_text) {
                        $count++;
                        $nums .= '; ' . $invoice_item->uniq_num;

                    } else {
                        $n++;
                        $count = 1;
                        $nums = $invoice_item->uniq_num;
                    }


                    $suma[$n] = $invoice_item->price * $count;

                    $prev_text = $invoice_item->text;
                    $prev_num = $invoice_item->uniq_num;

                    $search = array('(delivery)', '(extra)', '(sale)', 'sale', '; ;', '( ; )', '()');
                    $text = str_replace($search, '', $invoice_item->text . ' (' . $nums . ')');
                    $table[$n] = '<tr><td align="center">' . $n . '</td>
        <td align="left">' . $text . '</td>
        <td align="center">шт.</td>
        <td align="center">' . $count . '</td>
        <td align="center">' . $invoice_item->price . '</td>
        <td align="center">' . $suma[$n] . '</td></tr>';
                }

                $n = 1;
                if ($table) {
                    foreach ($table as $tr) {
                        $preview .= $tr;
                        $SUMA += $suma[$n];
                        $n++;
                    }
                }

                $preview .= ' </table>
   <div style="font: normal 11pt times-new-roman;">
    Всього на суму ' . $SUMA . ' грн. в т.ч. ПДВ ( -----%) ------- грн.<br/><br/>';

            }


            $accounting = $this->systema_model->select_accounting($order_id);
            //если есть и сумма в таблице != выводимой сумме обновляем запись в таблице
            if ($accounting->num_rows() == 1 && $accounting->row()->sum != $SUMA && !$accounting->row()->locked) {

                $accounting_data = array('sum' => $SUMA, 'date' => date('U'));
                $this->systema_model->update_accounting($order_id, $accounting_data);
            }

            // если нету добавляем с суммой долга
            if ($accounting->num_rows() == 0) {

                $partner = $this->systema_model->view_partners(FALSE, $org_id);
                if ($partner->row()->paymethod == 'bnlfop' || $partner->row()->paymethod == 'nal') $for_edrpou = '3047313536';
                if ($partner->row()->paymethod == 'bnltov') $for_edrpou = '36832980';
                $accounting_data = array('order_id' => $order_id, 'sum' => $SUMA,
                    'date' => date('U'), 'debet_kredit' => 0, 'edrpou' => $partner->row()->edrpou, 'for_edrpou' => $for_edrpou);
                $this->systema_model->insert_accounting($accounting_data);
            }
            if ($accounting->row()->locked == 1 && ($this->ion_auth->is_admin() || $this->ion_auth->user()->row()->id == 14)) {
                $unlock = anchor('/fin/unlock_check/' . $order_id, 'Открыть чек');
            }

            if ($accounting->row()->sum != $SUMA) echo nbs(20) . '<b>Чек заблокирован!</b> ' . $unlock;
            if ($SUMA) echo $preview;
        }
    }

    //добавление дополнительных услуг в чек заказа
    public function add_extra_works($order_id, $type)
    {
        if ($this->input->post('price')) {
            $price = $this->input->post('price');
            $text = $this->input->post('text');
        } else {
            $string = explode('$', $this->input->post('text'));
            $text = $string[0];
            $price = $string[1];
        }
        $this->systema_model->insert_invoice_item($type, $order_id, $text, $price);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //удаление дополнительных услуг в чеке
    public function del_invoice_item($item_id)
    {
        $invoice_data = array('uniq_num' => 'deleted', 'info' => 'удалено пользователем ' . $this->ion_auth->user()->row()->first_name . nbs() . $this->ion_auth->user()->row()->last_name);
        $this->systema_model->update_invoice_item($item_id, $invoice_data);
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    //просмотр оплаты по выданным заказам
    public function issued_orders($month = false, $year = false)
    {

        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('main/login', 'refresh');
        } else {
            $month ? $month = $month : $month = $this->input->post('month');
            $year ? $year = $year : $year = $this->input->post('year');
            if ($this->ion_auth->is_admin() || $this->ion_auth->user()->row()->id == 28 || $this->ion_auth->user()->row()->id == 16) {
                $this->get_user_menu(anchor('fin/issued_orders', 'Выданные заказы')
                    , 'Деньги');
                if ($month) {
                    if (!$year) $year = date('Y');
                    $start = mktime(0, 0, 0, $month, 1, $year);
                    $end = mktime(23, 59, 59, $month + 1, 0, $year);
                } else {
                    $this->input->post('date') ? $this->data['date'] = $this->input->post('date') : $this->data['date'] = date('d.m.Y');
                    $sel_date = explode('.', $this->data['date']);
                    $start = mktime(0, 0, 0, $sel_date[1], $sel_date[0], $sel_date[2]);
                    $end = mktime(23, 59, 59, $sel_date[1], $sel_date[0], $sel_date[2]);
                }
                $this->data['issued_nal'] = $this->fin->issued_orders($start, $end, 'nal');
                $this->data['issued_bnltov'] = $this->fin->issued_orders($start, $end, 'bnltov');
                $this->data['issued_bnlfop'] = $this->fin->issued_orders($start, $end, 'bnlfop');
                $this->data['issued_bnlitfs'] = $this->fin->issued_orders($start, $end, 'bnltovitfs');
                $this->data['issued_bnlfsu'] = $this->fin->issued_orders($start, $end, 'bnltovfsu');
                $this->data['year'] = $year;
                $this->data['month'] = $month;

                if ($this->ion_auth->is_admin()) {
                    $this->load->view('new/header', $this->data);
                    $this->load->view('new/fin/issued_orders', $this->data);
                    $this->load->view('new/footer', $this->data);
                } else {
                    $this->load->view('header', $this->data);
                    $this->load->view('user_menu', $this->data);
                    $this->load->view('fin/issued_orders', $this->data);
                    $this->load->view('bottom', $this->data);
                }

            } else {
                redirect('/', 'refresh');
            }
        }
    }

    public function unlock_check($order_id)
    {
        $data['order_id'] = $order_id;
        $data['uniq_num'] = $order_id;
        $data['text'] = 'Открытие чека';
        $data['user_id'] = $this->ion_auth->user()->row()->id;
        $data['add_date'] = date('U');

        //print_r($data);
        $this->messages->adminlog($data);

        $this->systema_model->update_accounting($order_id, array('locked ' => 0));
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }


}

?>