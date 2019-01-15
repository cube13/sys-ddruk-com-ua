<table border="0">
    <tr>
        <td valign="top">
<table>
    <tr>
        <td align="center"> 
            <div class="order_caption"><?php echo $recikl->row()->cart_name;?></div>
<!--<img src="" title="Картридж" alt="тут типа картринка" height="200" width="200">-->
            <br>            <table style="font-size: 14px;">
                <tr><td>Заказ: <b><?php echo $recikl->row()->order_id;?></b></td></tr>
                <tr><td>Картридж №: <b><?php echo $recikl->row()->cart_num;?></b></td></tr>
                <tr><td>Ячейка: <b><?php echo $recikl->row()->adres;?></b></td></tr>
                <tr><td>Поступил: <b><?php echo date('d M Y H:i',$recikl->row()->date_start-$this->config->item('time_correction'));?></b></td></tr>
                <tr><td>Результат диагностики: <span style="color:#ff0000;font-weight: bold;"><?php echo mb_strtoupper($recikl->row()->stage_name,'utf-8');?></span></td></tr>
                <tr><td>Дополнительно: <span style="color:#ff0000;font-weight: bold;"><?php echo mb_strtoupper(lang($recikl->row()->info),'utf-8');?></span></td></tr>
            </table>
        </td>
    </tr>
</table><br>
<?php $inrck=''; $inrfl='';?>
 <?php echo form_open('cartridges/done/'.$recikl->row()->cart_num.'/'.$order_id);?>   
<div align="center" style="width:100%;color:white;background-color: #cc0000;font-size:18px;padding: 2px;">Расходные материалы</div>

 <?php if($refill_parts):?>

<?php echo $inrfl='<input type="hidden" name="refill" value="1">';?>
<?php echo $inrck='<input type="hidden" name="recikl" value="0">';?>
<table>
    <?php foreach($refill_parts->result() as $refill_part):?>
    <tr><td><input type=checkbox name=<?php echo $refill_part->id.'-'.$refill_part->kolvo;?> checked="checked"></td>
        <td width="160"><?php echo $refill_part->kat_name;?></td>
        <td width="250"><?php echo $refill_part->tovar_name;?> (<?php echo $refill_part->kolvo.' '.$refill_part->edinicy;?>)
            </td></tr>
<?php endforeach;?>
    </table>
<?php endif;?>

<?php if($recikl_parts):?>
<?php echo $inrck='<input type="hidden" name="recikl" value="1">';?>

<table>

    <?php foreach($recikl_parts->result() as $recikl_part):?>
<tr><td><input type=checkbox name=<?php echo $recikl_part->id.'-'.$recikl_part->kolvo;?>></td>
    <td width="160"><?php echo $recikl_part->kat_name;?></td>
    <td width="250"><?php echo $recikl_part->tovar_name;?> (<?php echo $recikl_part->kolvo.' '.$recikl_part->edinicy;?>)
        </td></tr>
<?php endforeach;?>
<tr><td><input type=checkbox name="partnotneed"></td>
    <td colspan="2"><b>Запчасти не использовал</b></td><tr>
</table>
<?php endif;?><br/>
<center><input type="submit" value="ГОТОВО" class="white_button"></center>
<?php echo form_close();?>


        </td>
        <td width="10"> </td>
        <td width="400" valign="top"><br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">История работ</div>
<table><tr style="color: red;"><th width="80" align="left">Дата</th>
        <th width="250" align="left">Работы</th>
        <th width="100" align="left">Мастер</th></tr>
      <?php foreach ($history->result() as $value) :?>
    <tr><td><?php echo date('d.m.Y',$value->date_end);?></td>
        <td><?php echo lang($value->stage_code);?></td>
        <td><?php echo $value->last_name;?></td></tr>
<?php endforeach;?>
        </table>
            <br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">Инcтрукция</div>
            <br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">Обратить внимание</div>
<br><div style="border:3px solid #fc0000;width:98%;">
     <?php echo form_open('cartridges/not_done/'.$recikl->row()->cart_num.'/'.$order_id);?>   
    <div align="center" style="font-weight: bold;font-size: 14px;">Проблема</div><br>
     <input type="radio" name="problem" value="needrecikl"/>требуется восстановление<br>
     <input type="radio" name="problem" value="fullwodef"/>есть тонер, печатает без дефектов<br>
     <input type="radio" name="problem" value="handspenises"/>восстановление не помагает
     <?php echo $inrck.'<br/>'.$inrfl.'<br/>';?>
     
     <?php if($refill_parts):?>
     <?php foreach($refill_parts->result() as $refill_part):?>
    <input type=checkbox name=<?php echo $refill_part->id;?> checked="checked" style="visibility: hidden">
<?php endforeach;?>
    <?php endif;?>
    
    
     
     <br>
     <center><input type="submit" value="НЕ ГОТОВ" class="red_button"></center><br><?php echo form_close();?>
            </div>
        
        </td>
    </tr>
    
</table>