<div id="infoMessage"><?php echo $message;?></div>
<?php echo form_open('orders/add_cartridge/'.$order_id.'/'.$hash);?>


<h3>Внесение картриджей в заказ №<?php echo $order_id;?></h3>
    <table>
        <tr><td>Номер картриджа</td><td><?php echo form_input($cart_num);?></td>
        <tr><td>Наименование</td><td>
            <table><tr><td><div id='get_cartridge'></div></td>
        <td><div class="autosuggest_cart" id="autosuggest_cart"></div></td></tr></table>
            </td></tr>
    </table>

 <?php echo form_submit('submit', lang('submit_cartridge_add')); ?>    
<?php echo form_close();?>     

</body>
</html>