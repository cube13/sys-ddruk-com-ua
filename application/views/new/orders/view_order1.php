<script>
    function cart_list() {
        $("#refresh_cart_list").attr("disabled",true);
        $("#icon-refresh").addClass('icon-spin');
        $.ajax({
            type: "GET",
            url: "/cartridges/view_cartridg_stages/<?php echo $order->id;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#cart_list").html(html);
                $("#refresh_cart_list").attr("disabled",false);
                $("#icon-refresh").removeClass('icon-spin');

            }
        });

        $.ajax({
            type: "GET",
            url: "/techs/view_tech_stages/<?php echo $order->id;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#tech_list").html(html);
            }
        });

    }

    function cart_list_done() {

        $.ajax({
            type: "GET",
            url: "/cartridges/get_done_cartridg/<?php echo $order->id . '/' . $order->org_id . '/' . $order->paymethod . '/' . $order->discount;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#cart_done_list").html(html);
            }
        });

    }

    function tech_list_done() {

        $.ajax({
            type: "GET",
            url: "/techs/get_done_techs/<?php echo $order->id . '/' . $order->org_id . '/' . $order->paymethod . '/' . $order->discount;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#tech_done_list").html(html);
            }
        });

    }

    function invoice_preview() {
        $.ajax({
            type: "GET",
            url: "/fin/invoice_preview/<?php echo $order->id;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#invoice_preview").html(html);
            }
        });

    }


    $(function () {
            $("#extrawork").autocomplete({
                source: "/store/get_service_for_order"
            });

            $("#sale").autocomplete({
                source: "/store/get_sale_for_order"
            });

            cart_list();
            setInterval('cart_list()', 240000);

            cart_list_done();
            setInterval('cart_list_done()', 240000);

            tech_list_done();
            setInterval('tech_list_done()', 240000);

            invoice_preview();
            setInterval('invoice_preview()', 240000);

        }
    );
</script>

<div class="row">
    <div class="span2" style="font-weight: bold;font-size: 22px;">
        <a>
             Заказ №<?php echo $order->id; ?>
        </a>
    </div>
    <div class="span3">
        <?php if ($tofcdl->row()->date_end != 0): ?>
            Курьер вернулся <?php echo date('d.m.Y в H:i', $tofcdl->row()->date_end); ?>
        <?php else: ?>
            <?php if ($tofcdl->row()->date_start == 0): ?>
                <?php $tofcdl->row()->date_start = date('U'); ?>
                <div class="alert">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Дата выезда неустановленна!</strong>
                </div>
                Дата выезда
                <form class="form-inline" action="/orders/set_delivery_date/tofcdl/<?php echo $order->id; ?>/0"
                      method="post">
                    <input style="width:75px;" type="text" name="date"
                           value="<?php echo date('d.m.Y', $tofcdl->row()->date_start); ?>" id="datepicker"/>
                    <select style="width:55px;" name="hour">
                        <?php for ($h = 9; $h <= 18; $h++): ?>
                            <?php if ($h == date('H', $tofcdl->row()->date_start)) {
                                $sel = 'selected=selected';
                            } ?>
                            <option <?php echo $sel; ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
                            <?php $sel = ''; ?>
                        <?php endfor; ?>
                    </select>
                    <select style="width:55px;" name="min">
                        <?php for ($m = 0; $m <= 45; $m = $m + 15): ?>
                            <?php if ($m == date('i', $tofcdl->row()->date_start)) $sel = 'selected=selected'; ?>
                            <option <?php echo $sel; ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
                            <?php $sel = ''; ?>
                        <?php endfor; ?>
                    </select>

                    <input type="submit" name="ok" value="ok" class="btn">
                </form>
            <?php else: ?>
                <form class="form-inline" action="/orders/set_delivery_date/tofcdl/<?php echo $order->id; ?>/0"
                      method="post">
                    <input style="width:75px;" type="text" name="date"
                           value="<?php echo date('d.m.Y', $tofcdl->row()->date_start); ?>" id="datepicker"/>
                    <select style="width:55px;" name="hour">
                        <?php for ($h = 9; $h <= 18; $h++): ?>
                            <?php if ($h == date('H', $tofcdl->row()->date_start)) {
                                $sel = 'selected=selected';
                            } ?>
                            <option <?php echo $sel; ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
                            <?php $sel = ''; ?>
                        <?php endfor; ?>
                    </select>

                    <select style="width:55px;" name="min">
                        <?php for ($m = 0; $m <= 45; $m = $m + 15): ?>
                            <?php if ($m == date('i', $tofcdl->row()->date_start)) $sel = 'selected=selected'; ?>
                            <option <?php echo $sel; ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
                            <?php $sel = ''; ?>
                        <?php endfor; ?>
                    </select>

                    <input type="submit" name="ok" value="ok" class="btn">
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="span5">
        <?php switch ($order->paymethod) {
            case 'nal':
                $nala = 'btn-info';
                break;
            case 'bnlfop':
                $bnlfopa = 'btn-info';
                break;
            case 'bnltov':
                $bnltova = 'btn-info';
                break;
            case 'subscr':
                $subscr = 'btn-info';
                break;
        } ?>

        <div class="btn-group">

            <a href="/orders/set_paymethod/<?php echo $order->id; ?>/nal"
               class="btn <?php echo $nala; ?>">Наличные</a>
            <?php if ($order->org_id != 11): ?>
                <a href="/orders/set_paymethod/<?php echo $order->id; ?>/bnlfop"
                   class="btn <?php echo $bnlfopa; ?>">ФОП Швайко В.В.</a>
                <a href="/orders/set_paymethod/<?php echo $order->id; ?>/bnltov"
                   class="btn <?php echo $bnltova; ?>">ТОВ Добрый Друк</a>
            <?php endif; ?>
            <a href="/orders/set_paymethod/<?php echo $order->id; ?>/subscr" class="btn <?php echo $subscr; ?>">Абонемент</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="span2 alert-info">

    </div>

    <div class="span1 ">
        <?php $atts_order = array(
            'class' => '',
            'width' => '600',
            'height' => '650',
            'scrollbars' => 'no',
            'status' => 'no',
            'resizable' => 'no',
            'screenx' => '250',
            'screeny' => '10'); ?>
        <div class="btn-group">
            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                Доки <span class="caret"></span>
            </a>


            <ul class="dropdown-menu">
                <li><?php echo anchor_popup('/orders/print_order1/' . $hash, 'Квитанция на картриджи', $atts_order); ?></li>
                <li><?php echo anchor_popup('/orders/print_order_tech/' . $hash, 'Квитанция на технику', $atts_order); ?></li>
                <li>
                    <?php if ($this->ion_auth->is_admin()): ?>
                        <?php echo anchor_popup('/fin/print_check/' . $order->id, 'Напечатать чек/счет', $atts_order); ?>
                    <?php endif; ?>
                    <?php if (!$this->ion_auth->is_admin()): ?>
                        <?php if (!$is_payd): ?>
                            <?php if ($nal['checked']): ?>
                                <?php echo anchor_popup('/fin/print_check/' . $order->id, 'Напечатать чек', $atts_order); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
                <li><?php echo anchor_popup('/cartridges/print_delivery_list/' . $hash, 'Акт доставки/выдачи картриджей', $atts_order); ?></li>
                <li><?php echo anchor_popup('/techs/print_delivery_list/' . $hash, 'Акт доставки/выдачи техники', $atts_order); ?></li>

            </ul>

        </div>
    </div>

    <div class="span2">
        <?php echo $delivery; ?>
        <?php echo $toclnt_close; ?>
    </div>

    <div class="span3">
        <div class="btn-group">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Забрать
                </button>
                <ul class="dropdown-menu">
                    <?php foreach ($couriers as $id => $name):?>
                        <li><a href="/orders/set_executant/tofcdl/<?php echo $order_id;?>/<?php echo $id;?>"><?php echo $name;?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Отвозит
                </button>
                <ul class="dropdown-menu">
                    <?php foreach ($couriers as $id => $name):?>
                        <li><a href="/orders/set_executant/toclnt/<?php echo $order_id;?>/<?php echo $id;?>"><?php echo $name;?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>


    <div class="span2">
        <div class="input-append">
            <?php echo form_open('orders/clarify/' . $order->id, 'class="form-inline"'); ?>
            <?php echo form_input($delivery_price); ?>
            <button class="btn" type="submit"><i class="icon-truck"></i></button>
            </form>
        </div>
    </div>

</div>

    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#order" aria-controls="order" role="tab" data-toggle="tab">Замовлення</a>
            </li>
            <!--<li role="presentation">
                <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Профіль клієнта</a>
            </li>-->
            <li role="presentation">
                <a href="#cart_tab" aria-controls="cart_tab" role="tab" data-toggle="tab">Картриджі</a>
            </li>
            <li role="presentation">
                <a href="#tech_tab" aria-controls="tech_tab" role="tab" data-toggle="tab">Техніка</a>
            </li>
            <li role="presentation">
                <a href="#accaunt" aria-controls="tech_tab" role="tab" data-toggle="tab">Розрахунки</a>
            </li>
        </ul>

        <!-- Tab ORDER -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="order">

                <div class="row">
                    <div class="span4">
                        <span style="font-size:20px;"><?php echo nbs() . anchor_popup('/partners/view/' . $order->org_id, $order->short_name); ?></span>
                        <!--Блок контактных данных из карточки клиента-->
                        <table class="table table-condensed table-bordered" width="200" style="background-color:">
                            <tr>
                                <td width="70">Договор</td>
                                <td width="150"><?php if ($order->contract) echo $order->contract; else echo "нет"; ?></td>
                                <td></td>
                            <tr>
                            <tr>
                                <td width="70">ЕДРПОУ</td>
                                <td width="150"><?php if ($order->edrpou) echo $order->edrpou; else echo "-"; ?></td>
                                <td></td>
                            <tr>
                            <tr <?php if ($order->orgpaymethod != $order->paymethod) echo 'class="warning"'; ?>>
                                <td width="70">Способ оплаты</td>
                                <td width="150">
                                    <?php switch ($order->orgpaymethod) {
                                        case 'bnltov':
                                            echo 'ТОВ "СЦ Добрий Друк"';
                                            break;
                                        case 'nal':
                                            echo 'Наличные';
                                            break;
                                        case 'bnlfop':
                                            echo 'ФОП Швайко В. В.';
                                            break;
                                    } ?>
                                </td>
                                <td></td>
                            <tr>
                            <tr class="info">
                                <td width="70">Скидка</td>
                                <td width="150"><?php if ($order->org_discount) echo $order->org_discount; else echo "нет"; ?></td>
                                <td></td>
                            <tr>
                                <?php foreach ($contacts->result() as $contact): ?>
                                <?php if ($contact->type == 'contact-info'): ?>
                                    <?php $extraInfo .= $contact->value . '<br>';
                                    continue; ?>
                                <?php endif; ?>
                            <tr class="success">
                                <td width="100"><?php echo lang($contact->type); ?></td>
                                <td><?php echo $contact->value; ?></td>
                                <td width="10"><a
                                            href="/messages/add/<?php echo $order->id; ?>/<?php echo $order->id; ?>/<?php echo $contact->type; ?>/<?php echo $contact->id; ?>"><i
                                                class="icon-download-alt"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="error">
                                <td>Доп.инфо</td>
                                <td colspan="2"><?php echo $extraInfo; ?></td>
                            <tr>
                                <td colspan="3">
                                    <?php echo form_open('orders/move/' . $order->id) . form_input($org_name); ?>
                                    <div class="autosuggest" id="autosuggest_list"></div>
                                    <?php echo form_close(); ?>
                                </td>
                        </table>

                        <!--Блок контактных данных для заказа-->

                        <table class="table table-condensed table-bordered" width="200" style="background-color:gainsboro">
                            <?php if ($extracontact->num_rows() > 0): ?>

                                <?php foreach ($extracontact->result() as $contact): ?>

                                    <tr class="info">
                                        <td width="40"><?php echo lang($contact->stage_code); ?>
                                        </td>
                                        <td width="150"><?php echo $contact->text; ?><?php if ($contact->stage_code == 'contact-mob') echo $send_sms; ?></td>
                                        <td width="10">
                                            <?php if ($contact->stage_code != 'contact-subscr'): ?>
                                                <a class="" href="/messages/hide_mess/<?php echo $contact->id; ?>"><i
                                                            class="icon-remove"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr>
                                <td colspan="2">
                                    <form action="/messages/add/<?php echo $order->id; ?>/<?php echo $order->id; ?>/info" method="post"
                                          accept-charset="utf-8">
                                        <div class="input-prepend input-append">
                                            <select class="span1" name="code">
                                                <option value="contact-name">Имя</option>
                                                <option value="contact-mob">Моб.тел.</option>
                                                <option value="contact-tel">Тел.</option>
                                                <option value="contact-adres">Адрес</option>
                                                <option value="contact-subscr">Абонемент</option>
                                            </select>
                                            <input class="span3" value="" size="12" type="text" name="text">
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>

                        <table class="table table-condensed table-bordered" width="200" style="background-color:gainsboro">
                            <?php if ($extrainfo->num_rows() > 0): ?>
                                <?php foreach ($extrainfo->result() as $exinfo): ?>

                                    <tr class="error">
                                        <td colspan="2"><?php echo $exinfo->text; ?></td>
                                        <td width="10"><a class="" href="/messages/hide_mess/<?php echo $exinfo->id ?>"><i
                                                        class="icon-minus"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr>
                                <td colspan="2">
                                    <form action="/messages/add/<?php echo $order->id; ?>/<?php echo $order->id; ?>/info" method="post"
                                          accept-charset="utf-8">
                        <textarea class="span4" style="width: 330px" id="text-info" rows="2" name="text"
                                  placeholder="информация к заказу..."></textarea>
                                        <button name="button" type="submit" id="button" class="btn">Добавить</button>
                                    </form>
                                </td>
                            </tr>
                        </table>

                    </div>
                    <div class="span4">
                        <table class="table table-condensed">
                            <tbody>
                            <tr>
                                <td>Замовлення створене</td>
                                <td><?php echo date('d.m.Y о H:s', $order->date_create); ?></td>
                            </tr>
                            <tr>
                                <td>Відповідальний</td>
                                <td><?php echo $order->first_name; ?></td>
                            </tr>
                            <tr>
                                <td>Кур'єр</td>
                                <td><b><?php echo $tofcdl->row()->first_name;?></b></td>
                            </tr>
                            <tr>
                                <?php if($tofcdl->row()->date_start && !$tofcdl->row()->date_end): ?>
                                    <?php if(!$tofcdl->row()->action_flag):?>
                                        <td>Забрати</td>
                                        <td><?php echo date('d.m.Y о H:i', $tofcdl->row()->date_start); ?></td>
                                    <?php elseif ($tofcdl->row()->action_flag):?>
                                        <td>Виїхав</td>
                                        <td><?php echo date('d.m.Y о H:i', $tofcdl->row()->date_start); ?></td>
                                    <?php endif;?>
                                <?php elseif($tofcdl->row()->date_start && $tofcdl->row()->date_end):?>
                                    <td>
                                        Доставленно в офіс
                                    </td>
                                    <td>
                                        <?php echo date('d.m.Y о H:i', $tofcdl->row()->date_end); ?>
                                    </td>
                                <?php endif;?>


                            </tr>
                            <tr>
                                <td>Доставка клієнту</td>
                                <td><?php if($toclnt->row()->date_start) echo "<b>".$toclnt->row()->first_name."</b> ".date('d.m.Y о H:i', $toclnt->row()->date_start); ?></td>
                            </tr>
                            <tr>
                                <td>Оплата</td>
                                <td>
                                    <?php

                                    if($ispayd->row()->date_start)
                                    {
                                        if($ispayd->row()->date_end)
                                        {
                                            echo "Сплачений ".date('d.m.Y',$ispayd->row()->date_end);
                                        }
                                        else
                                        {
                                            echo '<a class="btn btn-primary btn-small" href="/orders/close_stage/ispayd/'.$order->id.'"> + </a>';
                                        }

                                    }
                                    else
                                    {
                                        echo "Чек/рахунок не створено";
                                    }


                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">Профіль клієнта</div>

            <div role="tabpanel" class="tab-pane" id="cart_tab">
               <?php $atts = array('width' => '600',
                'height' => '570',
                'scrollbars' => 'no',
                'status' => 'no',
                'resizable' => 'no',
                'screenx' => '450',
                'screeny' => '10',
                'class' => 'btn');

                $atts_add_cart = array('width' => '600',
                'height' => '270',
                'scrollbars' => 'no',
                'status' => 'no',
                'resizable' => 'no',
                'screenx' => '250',
                'screeny' => '150',
                'class' => 'btn');

                $cart_num = array('name' => 'cart_num',
                'id' => 'cart_num',
                'type' => 'text',
                'size' => '30',
                'onkeyup' => 'get_cartridge(this.value);',
                'value' => $this->form_validation->set_value('cart_num'),
                'autocomplete' => 'off'
                );
                ?>



                <?php if ($order_recive): ?>
                    <div class="container">
                        <div class="row">
                            <div class="span12">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#add_cart">
                                        Внести картриджи
                                    </button>
                                    <?php echo anchor_popup('/cartridges/to_test/' . $order_id, 'Лист диагностики', $atts); ?>
                                    <button id="refresh_cart_list" type="button" class="btn btn-default" onclick="cart_list();">
                                        <i id="icon-refresh" class="icon-refresh"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="collapse out" id="add_cart">
                        <div class="container">
                            <div class="row">
                                <div class="span5">
                                    <form action="javascript:void(null)" onsubmit="call();" name="form" id="add_cart_form"
                                          class="form-horizontal"
                                          novalidate="novalidate" enctype="multipart/form-data" method="post" accept-charset="utf-8">

                                        <h3>Внесение картриджей в заказ №<?php echo $order_id; ?></h3>

                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>Номер картриджа</td>
                                                <td><input type="text" name="cart_num" value="" id="cart_num" size="30"
                                                           onkeyup="get_cartridge(this.value);" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Наименование</td>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                        <tr>
                                                            <td>
                                                                <div id="get_cartridge"></div>
                                                            </td>
                                                            <td>
                                                                <div class="autosuggest_cart" id="autosuggest_cart"></div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <input id="add_cartridge" type="submit" name="submit" value="Внести" class="btn btn-primary btn-sm">
                                    </form>
                                </div>
                                <div class="span6" style="margin-top: 10%;"><div id="results"></div></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="pack"></div>
                    <div id="cart_list"></div>

                <?php endif; ?>

                <script type="text/javascript" language="javascript">

                    function call() {
                        $("#refresh_cart_list").attr("disabled",true);
                        $("#icon-refresh").addClass('icon-spin');
                        $( "#results" ).empty();
                        $("#add_cartridge").attr("disabled",true);
                        var msg   = $('#add_cart_form').serialize();
                        $.ajax({
                            type: 'POST',
                            url: '/cartridges/add_cartridge/<?php echo $order_id;?>/<?php echo $hash?>',
                            data: msg,
                            success: function(data) {
                                $('#results').fadeOut(250);
                                $('#results').html(data);
                                $('#results').fadeIn(250);
                                $('#results').fadeOut(250);
                                $('#results').fadeIn(250);
                                $('#results').fadeOut(250);
                                $('#results').fadeIn(250);
                                $("#add_cartridge").attr("disabled",false);
                                cart_list();
                            },
                            error:  function(xhr, str){
                                alert('Возникла ошибка: ' + xhr.responseCode);
                            }
                        });

                    }

                    function to_dsp(cart, adres)
                    {
                        $("#wait_client").attr("disabled",true);
                        $.ajax({
                            type: "POST",
                            url: "/cartridges/to_dsp/"+cart+"/<?php echo $order_id;?>/"+adres,
                            success: function(data)
                            {
                                $('#pack').html(data);
                                cart_list();
                            }
                        });
                    }

                    function wait_client(cart)
                    {
                        $.ajax({
                            type: "POST",
                            url: "/cartridges/wait_client/"+cart+"/<?php echo $order_id;?>/",
                            success: function(data)
                            {
                                $('#pack').html(data);
                                cart_list();
                            }
                        });
                    }

                    function client_answer(cart, answer)
                    {
                        $.ajax({
                            type: "POST",
                            url: "/cartridges/client_answer/"+cart+"/<?php echo $order_id;?>/"+answer,
                            success: function(data)
                            {
                                $('#pack').html(data);
                                cart_list();
                            }
                        });
                    }

                    function set_cart_sort(cart, order)
                    {
                        $.ajax({
                            type: "POST",
                            url: "/cartridges/set_cart_sort/"+cart+"/"+order+"/<?php echo $order_id;?>/",
                            success: function(data)
                            {
                                $('#pack').html(data);
                                cart_list();
                            }
                        });
                    }

                </script>

            </div>

            <div role="tabpanel" class="tab-pane" id="tech_tab">
               <?php
                    $atts_add_cart = array(
                    'width' => '750',
                    'height' => '570',
                    'scrollbars' => 'no',
                    'status' => 'no',
                    'resizable' => 'no',
                    'screenx' => '150',
                    'screeny' => '150',
                    'class' => 'btn');
               ?>

                <?php if ($order_recive): ?>
                    <div class="order_part_caption">
                        <table>
                            <tr>
                                <td> <?php echo anchor_popup('/techs/add_techs2/' . $order->id . '/' . $hash, 'Внести технику', $atts_add_cart); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="tech_list"></div>
                <?php endif; ?>

            </div>

            <div role="tabpanel" class="tab-pane" id="accaunt">
                <?php if ($order_recive): ?>
                    <div class="container">
                        <div class="row">
                            <div class="span8">
                                Виконані роботи по замовленню
                                <?php $atts_check = array(
                                    'width' => '600',
                                    'height' => '550',
                                    'scrollbars' => 'no',
                                    'status' => 'no',
                                    'resizable' => 'no',
                                    'screenx' => '250',
                                    'screeny' => '10'); ?>


                                <div id="cart_done_list"></div>
                                <div id="tech_done_list"></div>

                                <div><?php echo $extra_works; ?></div>
                                <div><?php echo $sale; ?></div>

                                <div id="invoice_preview" style="text-align: left;width:650px; border:2px solid black;"></div>
                                <div class="row">
                                    <div class="span8">

                                        <div>
                                            <?php echo form_open('orders/clarify/' . $order->id); ?>
                                            <label>Знижка по замовленню:</label>
                                            <div class="input-append">
                                                <?php echo form_input($discount); ?>
                                                <button class="btn" type="submit"><span style="font-size: 14px;font-weight: bold">%</span>
                                                </button>
                                                <?php echo form_close(); ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

