<?php echo $stages_menu;?>

    <?php if($orders->num_rows()>0):?>
<table width="100%"><tr align="left">
            <th>№ заказа</th><th width="200">Клиент</th><th>Доставка в сервис</th>
            <th>Подготовка</th><th>Работа</th>
            <th>Упаковка</th><th>Доставка клиенту</th><th>Оплата</th>
            </tr>
    <?php $prev_id=-1;?>
<?php foreach ($orders->result() as $order):?>
    <?php if($prev_id==$order->id):?>
     <td><?php $order->date_end ? $mess=date('d M y  H:i',$order->date_end): $mess='Обработка'; echo $mess;?></td>
    <?php endif;?>
    <?php if($prev_id!=$order->id):?>
    <tr><td><?php echo anchor_popup('orders/view_order/'.$order->hash,$order->id);?></td>
        <td><?php echo $order->short_name;?></td>
        <td><?php $order->date_end ? $mess=date('d M y  H:i',$order->date_end): $mess='Обработка'; echo $mess;?></td>
    <?php endif;?>
        <?php $prev_id=$order->id;?>
        
<?php endforeach;?>
</table>
<?php endif;?>
