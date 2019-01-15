   	<div id="infoMessage"><?php echo $message;?></div>
	
       
    <?php echo form_open("admin/update_user_passwd/".$userid);?>
        <table>
            <tr><td width="100"><?php echo lang('form_create_user_password');?></td><td> <?php echo form_input($password);?></td></tr>
            <tr><td><?php echo lang('form_create_user_passwordconfirm');?></td><td><?php echo form_input($password_confirm);?></td></tr>
            <tr><td></td><td><?php echo form_submit('submit', lang('form_change_password_submit'));?></td></tr>
        </table>
          
    <?php echo form_close();?>