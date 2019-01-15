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
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<div class="row">
    <div class="span2">
        <form class="form-inline" action="/fin/issued_orders" method="post">
            <input style="width:100px;" type="text" name="date" value="<?php echo $date;?>" id="datepicker"/>

            <input type="submit" name="ok" value="ok" class="btn">
        </form>
    </div>
    <div class="span6">
        <?php if ($this->ion_auth->is_admin()):?>
        <form class="form-inline" action="/fin/issued_orders" method="post">
            <select name="month">
                <?php foreach ($monthes as $num => $name):?>
                    <?php if($num==$month):?>
                        <option selected="selected" value="<?php echo $num;?>"><?php echo $name;?></option>
                    <?php else:?>
                        <option value="<?php echo $num;?>"><?php echo $name;?></option>
                    <?php endif;?>
                <?php endforeach;?>
            </select>
            <select name="year">
                <?php for ($year1=date('Y')-3;$year1<=date('Y');$year1++):?>
                    <?php if($year==$year1):?>
                        <option selected="selected" value="<?php echo $year1;?>"><?php echo $year1;?></option>
                    <?php else:?>
                        <option value="<?php echo $year1;?>"><?php echo $year1;?></option>
                    <?php endif;?>

                <?php endfor;?>
            </select>

            <input type="submit" name="ok" value="ok" class="btn">
        </form>
        <?php endif;?>
    </div>
</div>



<h4>Выданные заказы нал</h4>
<div class="row">
    <div class="span4">
        <?php $user_array=array(); $SUM=0; $nn=1;?>
<table class="table table-condensed table-striped">
    <tr>
        <th></th>
        <th width="75">Заказ №</th>
        <th width="100">Сумма</th>
        <th width="150">Выдал</th>
    </tr>
    
    <?php foreach ($issued_nal->result() as $issued):?>
    <tr>
        <td><?php echo $nn++;?></td>
        <td><?php echo anchor_popup('orders/view_order/'.$issued->hash.'/docs', $issued->order_id);?></td>
        <td><?php echo $issued->sum;?></td>
        <td><?php echo $issued->first_name.nbs().$issued->last_name;?></td>
    </tr>
    <?php $SUM=$SUM+$issued->sum;?>
    <?php $user_array[$issued->id]['name']=$issued->first_name.nbs().$issued->last_name;
    $user_array[$issued->id]['sum']=$user_array[$issued->id]['sum']+$issued->sum;
    ?>
    <?php endforeach;?>
    <tr><td></td><td>Всего</td><td><b><?php echo $SUM;?></b></td><td></td></tr>
</table>
        </div>
    <div class="span4">
        <table class="table table-condensed table-striped">
    <tr>
        <th width="150">Выдал</th>
        <th width="100">Сумма</th>
    </tr>
        <?php foreach ($user_array as $user):?>
    <tr><td><?php echo $user['name'];?></td>
    <td><?php echo $user['sum'];?></td></tr>
        <?php endforeach;?>
        </table>
    </div>
    </div>

<h4>Выданные заказы ТОВ</h4>
<div class="row">
    <div class="span4">
        <?php $user_array=array(); $SUM=0; $nn=1;?>
<table class="table table-condensed table-striped">
    <tr>
        <th></th>
        <th width="75">Заказ №</th>
        <th width="100">Сумма</th>
        <th width="150">Выдал</th>
    </tr>
    
    <?php foreach ($issued_bnltov->result() as $issued):?>
    <tr>
        <td><?php echo $nn++;?></td>
        <td><?php echo anchor_popup('orders/view_order/'.$issued->hash.'/docs', $issued->order_id);?></td>
        <td><?php echo $issued->sum;?></td>
        <td><?php echo $issued->first_name.nbs().$issued->last_name;?></td>
    </tr>
    <?php $SUM=$SUM+$issued->sum;?>
    <?php $user_array[$issued->id]['name']=$issued->first_name.nbs().$issued->last_name;
    $user_array[$issued->id]['sum']=$user_array[$issued->id]['sum']+$issued->sum;
    ?>
    <?php endforeach;?>
    <tr><td></td><td>Всего</td><td><b><?php echo $SUM;?></b></td><td></td></tr>
</table>
        </div>
    <div class="span4">
        <table class="table table-condensed table-striped">
    <tr>
        <th width="150">Выдал</th>
        <th width="100">Сумма</th>
    </tr>
        <?php foreach ($user_array as $user):?>
    <tr><td><?php echo $user['name'];?></td>
    <td><?php echo $user['sum'];?></td></tr>
        <?php endforeach;?>
        </table>
    </div>
    </div>

<h4>Выданные заказы ФОП</h4>
<div class="row">
    <div class="span4">
        <?php $user_array=array(); $SUM=0; $nn=1;?>
<table class="table table-condensed table-striped">
    <tr>
        <th></th>
        <th width="75">Заказ №</th>
        <th width="100">Сумма</th>
        <th width="150">Выдал</th>
    </tr>
    
    <?php foreach ($issued_bnlfop->result() as $issued):?>
    <tr>
        <td><?php echo $nn++;?></td>
        <td><?php echo anchor_popup('orders/view_order/'.$issued->hash.'/docs', $issued->order_id);?></td>
        <td><?php echo $issued->sum;?></td>
        <td><?php echo $issued->first_name.nbs().$issued->last_name;?></td>
    </tr>
    <?php $SUM=$SUM+$issued->sum;?>
    <?php $user_array[$issued->id]['name']=$issued->first_name.nbs().$issued->last_name;
    $user_array[$issued->id]['sum']=$user_array[$issued->id]['sum']+$issued->sum;
    ?>
    <?php endforeach;?>
    <tr><td></td><td>Всего</td><td><b><?php echo $SUM;?></b></td><td></td></tr>
</table>
        </div>
    <div class="span4">
        <table class="table table-condensed table-striped">
    <tr>
        <th width="150">Выдал</th>
        <th width="100">Сумма</th>
    </tr>
        <?php foreach ($user_array as $user):?>
    <tr><td><?php echo $user['name'];?></td>
    <td><?php echo $user['sum'];?></td></tr>
        <?php endforeach;?>
        </table>
    </div>
    </div>


