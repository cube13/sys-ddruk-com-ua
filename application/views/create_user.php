   	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("admin/create_user");?>
      
        <table>
            <tr><td><?php echo lang('form_create_user_name');?></td><td><?php echo form_input($first_name);?></td></tr>
            <tr><td><?php echo lang('form_create_user_sername');?></td><td><?php echo form_input($last_name);?></td></tr>
            <tr><td><?php echo lang('form_create_user_company');?></td><td><?php echo form_input($company);?></td></tr>
            <tr><td><?php echo lang('form_create_user_email');?></td><td><?php echo form_input($email);?></td></tr>
            <tr><td><?php echo lang('form_create_user_phone');?></td><td><?php echo form_input($phone);?></td></tr>
            <tr><td><?php echo lang('form_create_user_login');?></td><td><?php echo form_input($login);?></td></tr>
            <tr><td><?php echo lang('form_create_user_password');?></td><td> <?php echo form_input($password);?></td></tr>
            <tr><td><?php echo lang('form_create_user_passwordconfirm');?></td><td><?php echo form_input($password_confirm);?></td></tr>
            <tr><td></td><td><?php echo form_submit('submit', lang('form_create_user_submit'));?></td></tr>
            
        </table>
      
    <?php echo form_close();?>

