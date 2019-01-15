<table border="0">
    <tr>
        <td valign="top">
<table>
    <tr>
        <td align="center"> 
            <div class="order_caption"><?php echo $cartridge->name;?></div>
<!--<img src="" title="Картридж" alt="тут типа картинка" height="200" width="200">-->
            <br>            <table style="font-size: 14px;">
                
                <tr><td width="100">Картридж №: </td><td><b><?php echo $cart_num;?></b></td></tr>
                <tr><td>Организация: </td><td><b><?php echo $cartridge->short_name;?></b></td></tr>
            </table>
        </td>
        
        
    </tr>
</table><br>

<table style="font-size: 14px;" width="300">
    <tr><td colspan="2">Изменить картридж</td></tr>
    <tr><td><?php echo form_open('cartridges/change_cartridge_registr/name_id/'.$cart_num).form_input($cart_name);?>
        <div class="autosuggest_cart" id="autosuggest_cart"></div>
    <?php echo form_submit('submit','Записать').form_close();?> </td></tr>
    <tr><td colspan="2" hight="5"> </td></tr>
    <tr><td colspan="2">Изменить организацию</td></tr>
    <tr><td><?php echo form_open('cartridges/change_cartridge_registr/org_id/'.$cart_num).form_input($org_name);?>
        <div class="autosuggest" id="autosuggest_list"></div>
    <?php echo form_submit('submit','Записать').form_close();?></td></tr>
    <tr><td colspan="2" hight="5"> </td></tr>
    <tr><td colspan="2">Изменить номер картриджа</td></tr>
    <tr><td><?php echo form_open('cartridges/change_cartridge_num/'.$cart_num);?>
        <input type="text" name="cart_num" value="" id="cart_num" size="7"/>
    <?php echo form_submit('submit','Записать').form_close();?></td></tr>
   
</table>

        </td>
        <td width="10"> </td>
        <td width="400" valign="top"><br>
<div align="left" style="width:100%;color:black;background-color: #ffcccc;font-size:18px;padding: 2px;">
    
    История работ картриджа №: <b><?php echo $cart_num;?></b></div>
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
        </td>
    </tr>
    
</table>