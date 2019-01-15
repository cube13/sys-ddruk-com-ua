<h4>Сообщение к картриджу <?php echo $cart_num;?><br/>
в заказе <?php echo $order_id;?></h4><br/>
<?php echo form_open('messages/add/'.$order_id.'/'.$cart_num.'/info');?>



    <textarea class="span4" style="width: 330px" id="text-info" rows="2" 
              name="text" placeholder="информация..."></textarea>
 <?php echo form_submit('submit', lang('submit_cartridge_add')); ?>    
<?php echo form_close();?>     

</body>
</html>