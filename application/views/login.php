<div style="">

	<div style="text-align: center;"><?php echo $message;?></div>

    <?php echo form_open("main/login");?>
    	<table align="center">
            <tr>
      	<td><?php echo lang('login_login','login');?></td>
      	<td><?php echo form_input($login);?></td>
        </tr>

        <tr>
            <td><?php echo lang('login_password','password');?></td>
      	<td><?php echo form_input($password);?></td>
        <tr>

      <tr><td colspan="2"><?php echo lang('login_remember','remember');?>
          	      <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <?php echo form_submit('submit', lang('login_enter'));?></td></tr>
        </table>
    <?php echo form_close();?>


</div>