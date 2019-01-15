    <?php echo form_open("admin/create_techtype");?>
      
        <table>
            <tr><td>Група</td><td><?php echo form_input($name);?></td></tr>
            
            <tr><td></td><td><?php echo form_submit('submit', 'Добавить тип');?></td></tr>
            
        </table>
      
    <?php echo form_close();?>

