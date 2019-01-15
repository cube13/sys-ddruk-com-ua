<table border="1">
    <tr>
        <td valign="top">
<table>
    <tr>
        <td align="center"> 
            <div class="order_caption"><?php echo $recikl->row()->cart_name;?></div>
<!--<img src="" title="Картридж" alt="тут типа картринка" height="200" width="200">-->
            <br>            <table style="font-size: 14pt;">
                <tr><td>Заказ: <b><?php echo $recikl->row()->order_id;?></b></td></tr>
                <tr><td>Картридж №: <b><?php echo $recikl->row()->cart_num;?></b></td></tr>
                <tr><td>Ячейка: <b><?php echo $recikl->row()->adres;?></b></td></tr>
                <tr><td>Поступил: <b><?php echo date('d M Y H:i',$recikl->row()->date_start-$this->config->item('time_correction'));?></b></td></tr>
                <tr><td>Результат диагностики: <span style="color:#ff0000;font-weight: bold;"><?php echo mb_strtoupper($recikl->row()->stage_name,'utf-8');?></span></td></tr>
                <tr><td>Дополнительно: <span style="color:#ff0000;font-weight: bold;"><?php echo mb_strtoupper(lang($recikl->row()->info),'utf-8');?></span></td></tr>
                <?php if($info):?>
                <tr><td>Информация: <span style="color:#ff0000;font-weight: bold;"><?php echo $info;?></span></td></tr>
                <?php endif;?>
            </table>
        </td>
    </tr>
</table>

<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">История работ</div>
<table>
    <tr style="color: red;">
        <th width="130" align="left">Дата/мастер</th>
        <th width="300" align="left">Работы</th>
    </tr>
<?php foreach ($history->result() as $value) :?>
    <?php if($prev_order_id!=$value->order_id):?>
    <tr>
        <td valign="top" style="border: 1px solid black"><?php echo date('d.m.Y',$value->date_end).' ('.$value->order_id.')';?><br/>
        <?php echo $value->last_name;?></td>
        <td valign="top" style="border: 1px solid black"><?php echo '<b>'.lang($value->stage_code).'</b><br/>';?>
            <?php foreach($material_history->result() as $materials):?>
                <?php if($materials->order_id==$value->order_id) echo $materials->name.' | ';?>
            <?php endforeach;?>
        </td>
    </tr>
    <?php endif;?>
    <?php $prev_order_id=$value->order_id;?>
<?php endforeach;?>
        
</table>
<!--            <br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">Инcтрукция</div>
            <br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">Обратить внимание</div>
-->
        
        </td>
        <td width="10"> </td>
        <td width="500" valign="top">
            <!--Форма отметки материалов и готов-->
<?php $inrck=''; $inrfl='';?>
<?php echo form_open('cartridges/done2/'.$recikl->row()->cart_num.'/'.$order_id);?>   
<div align="center" style="width:100%;color:white;background-color: #cc0000;font-size:18px;padding: 2px;">Расходные материалы</div>

 <?php if($refill_parts2):?>

<?php echo $inrfl='<input type="hidden" name="refill" value="1">';?>
<?php echo $inrck='<input type="hidden" name="recikl" value="0">';?>
<table>
    <?php foreach($refill_parts2->result() as $refill_part):?>
    <tr><td><input type=checkbox name=<?php echo $refill_part->id.'-'.$refill_part->kolvo;?> checked="checked"></td>
        <td width="400"><?php echo $refill_part->name;?> - <?php echo $refill_part->kolvo.' '.$refill_part->units;?> </td></tr>
<?php endforeach;?>
    </table>
<?php endif;?>

<?php if($recikl_parts2):?>
<?php echo $inrck='<input type="hidden" name="recikl" value="1">';?>

<table>
<?php foreach($recikl_parts2->result() as $recikl_part):?>
    <tr><td><input type=checkbox name=<?php echo $recikl_part->id.'-'.$recikl_part->kolvo;?>></td>
        <td width="400"><?php echo $recikl_part->name;?> - <?php echo $recikl_part->kolvo.' '.$recikl_part->units;?>
        </td>
    </tr>
<?php endforeach;?>
    <tr><td><input type=checkbox name="partnotneed"></td>
    <td colspan="2"><b>Запчасти не использовал</b></td><tr>
</table>
<?php endif;?><br/>
<input type="submit" value="ГОТОВО" class="white_button">
<?php echo form_close();?>

<?php echo form_open('cartridges/not_done/'.$recikl->row()->cart_num.'/'.$order_id);?>   
     <!--<input type="radio" name="problem" value="needrecikl"/>требуется восстановление<br>
     <input type="radio" name="problem" value="fullwodef"/>есть тонер, печатает без дефектов<br>
     <input type="radio" name="problem" value="handspenises"/>восстановление не помагает-->
    <table border="1">
        <tr>
            <td>Проблема</td>
            <td></td>
            <td>Нужне менять</td>
        </tr>
        <tr>
            <td valign="top">
                <label><input type=checkbox name=light> Светлая печать</label>
                <label><input type=checkbox name=wrap> Серый фон</label>
                <label><input type=checkbox name=dots> Точки</label>
                <label><input type=checkbox name=vline> Вертикальные линии</label>
                <label><input type=checkbox name=hline> Горизотальные линии</label>
                <label><input type=checkbox name=cartridgefull> Картридж полный</label>
                <label><input type=checkbox name=impossible> Невозможно сделать</label>
            </td>
            <td width="5"></td>
            <td valign="top" style="border-left: 1px solid black;"><label><input type=checkbox name=opc> Фотобарабан</label>
                <label><input type=checkbox name=pcr> ВПЗ</label>
                <label><input type=checkbox name=magrol> Маг. вал.</label>
                <label><input type=checkbox name=wiper> Чистящее лезвие </label>
                <label><input type=checkbox name=doctor> Доз. лезвие</label>
                <label><input type=checkbox name=patron> Ремонт/замена корпуса</label>
                <label><input type=checkbox name=other> Другое</label>
            </td>   
        </tr>
        
        
    </table>
     <?php echo $inrck.'<br/>'.$inrfl.'<br/>';?>
     
     <?php if($refill_parts):?>
     <?php foreach($refill_parts->result() as $refill_part):?>
    <input type=checkbox name=<?php echo $refill_part->id;?> checked="checked" style="visibility: hidden">
<?php endforeach;?>
    <?php endif;?>
   <input type="submit" value="НЕ ГОТОВ" class="red_button"><?php echo form_close();?>
 
        </td>
    </tr>
    
</table>