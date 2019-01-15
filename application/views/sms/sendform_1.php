 <?php echo form_open("sendsms/send");?>
 <table>
     <tr><td><?php echo form_input($message);?></td></tr>
     <tr><td><?php echo form_input($phonemob);?>
         <?php echo form_input($orderid);?></td></tr>
     
     <tr><td><?php echo form_submit('submit', lang('form_sendsms_submit'));?></td></tr>
</table>
      
    <?php echo form_close();?>

