   	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("admin/update_techtype/".$typeid."/1");?>
      
        <table>
            <tr><td width="100"><?php echo lang('update_group_name');?></td><td><?php echo form_input($name);?></td></tr>
            <tr><td></td><td><?php echo form_submit('submit', lang('update_group_submit'));?></td></tr>
            
        </table>
      
    <?php echo form_close();?>