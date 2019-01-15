<?php $monthes=array(
        '1'=>'Cічень',
    '2'=>'Лютий',
    '3'=>'Березень',
    '4'=>'Квітень',
    '5'=>'Травень',
    '6'=>'Червень',
    '7'=>'Липень',
    '8'=>'Серпень',
    '9'=>'Вересень',
    '10'=>'Жовтень',
    '11'=>'Листопад',
    '12'=>'Грудень',
);?>
<div class="container">
    <div class="row">
        <div class="col-4">
            <form class="form-inline" action="/fin/issued_orders" method="post">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="date" value="<?php echo $date;?>" id="" class="form-control col-6">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Гоу</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div class="col-4">
            <?php if ($this->ion_auth->is_admin()):?>
                <form class="form-inline" action="/fin/issued_orders" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <select name="month" class="form-control">
                                <?php foreach ($monthes as $num => $name): ?>
                                    <?php if ($num == $month): ?>
                                        <option selected="selected"
                                                value="<?php echo $num; ?>"><?php echo $name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $num; ?>"><?php echo $name; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            </div>
                            <div class="input-group-append">
                                <select name="year"class="form-control">
                                    <?php for ($year1 = date('Y') - 3; $year1 <= date('Y'); $year1++): ?>
                                        <?php if ($year == $year1): ?>
                                            <option selected="selected"
                                                    value="<?php echo $year1; ?>"><?php echo $year1; ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $year1; ?>"><?php echo $year1; ?></option>
                                        <?php endif; ?>

                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-small btn-primary">Гоу</button>
                    </div>
                </form>
            <?php endif;?>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-4">
            <h4>Выданные заказы нал</h4>
                    <?php $user_array = array();
                    $SUM = 0;
                    $nn = 1; ?>
            <table class="table table-sm">
                        <tr>
                            <th></th>
                            <th width="75">№</th>
                            <th width="100">Сума</th>
                            <th width="150">Видав</th>
                        </tr>

                        <?php foreach ($issued_nal->result() as $issued): ?>
                            <tr>
                                <td><?php echo $nn++; ?></td>
                                <td><?php echo anchor_popup('orders/view_order/' . $issued->hash . '/docs', $issued->order_id); ?></td>
                                <td><?php echo $issued->sum; ?></td>
                                <td><?php echo $issued->first_name . nbs() . $issued->last_name; ?></td>
                            </tr>
                            <?php $SUM = $SUM + $issued->sum; ?>
                            <?php $user_array[$issued->id]['name'] = $issued->first_name . nbs() . $issued->last_name;
                            $user_array[$issued->id]['sum'] = $user_array[$issued->id]['sum'] + $issued->sum;
                            ?>
                        <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <td>Всього</td>
                            <td><b><?php echo $SUM; ?></b></td>
                            <td></td>
                        </tr>
                    </table>


            <table class="table table-sm table-info">
                        <tr>
                            <th width="150">Видав</th>
                            <th width="100">Сума</th>
                        </tr>
                        <?php foreach ($user_array as $user): ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['sum']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>


        </div>
        <div class="col-4">
            <h4>Выданные заказы ТОВ</h4>
            <?php $user_array = array();
            $SUM = 0;
            $nn = 1; ?>
            <table class="table table-sm">
                <tr>
                    <th></th>
                    <th width="75">№</th>
                    <th width="100">Сума</th>
                    <th width="150">Видав</th>
                </tr>

                <?php foreach ($issued_bnltov->result() as $issued): ?>
                    <tr>
                        <td><?php echo $nn++; ?></td>
                        <td><?php echo anchor_popup('orders/view_order/' . $issued->hash . '/docs', $issued->order_id); ?></td>
                        <td><?php echo $issued->sum; ?></td>
                        <td><?php echo $issued->first_name . nbs() . $issued->last_name; ?></td>
                    </tr>
                    <?php $SUM = $SUM + $issued->sum; ?>
                    <?php $user_array[$issued->id]['name'] = $issued->first_name . nbs() . $issued->last_name;
                    $user_array[$issued->id]['sum'] = $user_array[$issued->id]['sum'] + $issued->sum;
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>Всього</td>
                    <td><b><?php echo $SUM; ?></b></td>
                    <td></td>
                </tr>
            </table>
            <table class="table table-sm table-info">
                <tr>
                    <th width="150">Видав</th>
                    <th width="100">Сума</th>
                </tr>
                <?php foreach ($user_array as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['sum']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="col-4">
            <h4>Выданные заказы ФОП</h4>
            <?php $user_array = array();
            $SUM = 0;
            $nn = 1; ?>
            <table class="table table-sm">
                <tr>
                    <th></th>
                    <th width="75">№</th>
                    <th width="100">Сума</th>
                    <th width="150">Видав</th>
                </tr>

                <?php foreach ($issued_bnlfop->result() as $issued): ?>
                    <tr>
                        <td><?php echo $nn++; ?></td>
                        <td><?php echo anchor_popup('orders/view_order/' . $issued->hash . '/docs', $issued->order_id); ?></td>
                        <td><?php echo $issued->sum; ?></td>
                        <td><?php echo $issued->first_name . nbs() . $issued->last_name; ?></td>
                    </tr>
                    <?php $SUM = $SUM + $issued->sum; ?>
                    <?php $user_array[$issued->id]['name'] = $issued->first_name . nbs() . $issued->last_name;
                    $user_array[$issued->id]['sum'] = $user_array[$issued->id]['sum'] + $issued->sum;
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>Всього</td>
                    <td><b><?php echo $SUM; ?></b></td>
                    <td></td>
                </tr>
            </table>
            <table class="table table-sm table-info">
                <tr>
                    <th width="150">Выдал</th>
                    <th width="100">Сумма</th>
                </tr>
                <?php foreach ($user_array as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['sum']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <h4>Выданные заказы ITFS</h4>
            <?php $user_array = array();
            $SUM = 0;
            $nn = 1; ?>
            <table class="table table-sm">
                <tr>
                    <th></th>
                    <th width="75">№</th>
                    <th width="100">Сума</th>
                    <th width="150">Видав</th>
                </tr>

                <?php foreach ($issued_bnlitfs->result() as $issued): ?>
                    <tr>
                        <td><?php echo $nn++; ?></td>
                        <td><?php echo anchor_popup('orders/view_order/' . $issued->hash . '/docs', $issued->order_id); ?></td>
                        <td><?php echo $issued->sum; ?></td>
                        <td><?php echo $issued->first_name . nbs() . $issued->last_name; ?></td>
                    </tr>
                    <?php $SUM = $SUM + $issued->sum; ?>
                    <?php $user_array[$issued->id]['name'] = $issued->first_name . nbs() . $issued->last_name;
                    $user_array[$issued->id]['sum'] = $user_array[$issued->id]['sum'] + $issued->sum;
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>Всього</td>
                    <td><b><?php echo $SUM; ?></b></td>
                    <td></td>
                </tr>
            </table>
            <table class="table table-sm table-info">
                <tr>
                    <th width="150">Выдал</th>
                    <th width="100">Сумма</th>
                </tr>
                <?php foreach ($user_array as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['sum']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
        <div class="col-5">
            <h4>Выданные заказы ФСУ</h4>
            <?php $user_array = array();
            $SUM = 0;
            $nn = 1; ?>
            <table class="table table-sm">
                <tr>
                    <th></th>
                    <th width="75">№</th>
                    <th width="100">Сума</th>
                    <th width="150">Видав</th>
                </tr>

                <?php foreach ($issued_bnlfsu->result() as $issued): ?>
                    <tr>
                        <td><?php echo $nn++; ?></td>
                        <td><?php echo anchor_popup('orders/view_order/' . $issued->hash . '/docs', $issued->order_id); ?></td>
                        <td><?php echo $issued->sum; ?></td>
                        <td><?php echo $issued->first_name . nbs() . $issued->last_name; ?></td>
                    </tr>
                    <?php $SUM = $SUM + $issued->sum; ?>
                    <?php $user_array[$issued->id]['name'] = $issued->first_name . nbs() . $issued->last_name;
                    $user_array[$issued->id]['sum'] = $user_array[$issued->id]['sum'] + $issued->sum;
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td>Всього</td>
                    <td><b><?php echo $SUM; ?></b></td>
                    <td></td>
                </tr>
            </table>
            <table class="table table-sm table-info">
                <tr>
                    <th width="150">Выдал</th>
                    <th width="100">Сумма</th>
                </tr>
                <?php foreach ($user_array as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['sum']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
    </div>
</div>