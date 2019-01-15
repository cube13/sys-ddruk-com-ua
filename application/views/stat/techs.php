<?php echo form_open("stat/techs");?>
 <table>
     <tr><td><input type="date" name="date_start" value="<?php echo $date_start;?>" placeholder="дд/мм/гг"></td>
     <td><input type="date" name="date_end" value="<?php echo $date_end;?>" placeholder="дд/мм/гг"></td>
         <td><button>ок</button></td></tr>
</table>
<?php echo form_close();?>
Ремонтов с <?php echo $date_start;?> по <?php echo $date_end;?>: <?php echo $tech_stat->num_rows();?>
<table class="table table-bordered table-condensed table-striped">
<?php foreach ($tech_stat->result() as $state):?>
    <tr>
        <td><?php echo $state->order_id;?></td>
        <td><?php echo $state->name;?></td>
        <td><?php echo $state->serial_num;?></td>
        <td><?php echo date('d/m/Y',$state->date_end);?></td>
    </tr>
<?php endforeach;?>    
    
</table>
