   	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("admin/create_group");?>
      
        <table>
            <tr><td>Група</td><td><?php echo form_input($groupname);?></td></tr>
            <tr><td>Опис</td><td><?php echo form_input($description);?></td></tr>
            
            <tr><td></td><td><?php echo form_submit('submit', 'Створити групу');?></td></tr>
            
        </table>
      
    <?php echo form_close();?>

