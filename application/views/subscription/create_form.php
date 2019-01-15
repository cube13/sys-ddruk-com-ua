<?php echo form_open("subscription/create_charges/");?>
<div class="row-fluid">
    <div class="span6">
<table width="100%" class="table table-bordered table-condensed" border="0">
    <tr><td width="150">Начало</td><td width="*"><input type="number" size="20" name="start_num"></td></tr>
    <tr><td>Конец</td><td><input type="number" size="20" name="end_num"></td></tr>
    <tr><td>Количество заправок</td><td><input type="number" size="20" name="capacity"></td></tr>
   
</table>
    </div>
   
<?php echo form_submit('submit', 'Создать тираж');?>
<?php echo form_close();?>    