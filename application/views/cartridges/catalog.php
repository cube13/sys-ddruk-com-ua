<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Бренды  <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <?php foreach($brands->result() as $brand) :?>
            <li><?php echo anchor('/cartridges/catalog/'.$brand->id.'/0', $brand->name.' - <b>монохром</b>');?></li>
            <li style="background-color: lightgrey;"><?php echo anchor('/cartridges/catalog/'.$brand->id.'/1', $brand->name.' - <span style="color:cyan;">ц</span><span style="color:magenta;">в</span><span style="color:yellow;">е</span><span style="color:black;">т</span>');?></li>
        <?php endforeach; ?>
    </ul>
</div>

<form action="/cartridges/update/100500321" method="post">
<table class="table table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th width="200" align="left">
            <form action="/admin/set_settings" method="post">    
            <div class="input-append">
                <input style="width: 30px" class="span1 small" type="text" name="kurs_usd_nal" value="<?php echo $kurs_usd_nal;?>">
                <button class="btn">грн/$</button> 
            </div>
           </form>
        </th>
        <th width="20" style="border-left: 2px black double"></th>
        <th width="100" align="left" colspan="3">Заправка</th>
        <th width="100" align="left" colspan="3">Восстанов</th>
        
        <th width="*"></th>
    </tr>
    <tr>
        <th width="200" align="left">Наименование</th>
        
        <th width="50" style="border-left: 2px black double"></th>
        <th width="50" align="left">СБ</th>
        <th width="50" align="left">цена</th>
        <th width="60" align="left">+</th>
        <th width="50" align="left">СБ</th>
        <th width="50" align="left">цена</th>
        <th width="50" align="left">+</th>
       
        
        <th width="*"></th>
    </tr>  
    </thead>    
        <tbody>
    <?php foreach ($cartridges->result() as $cart):?>
            <?php if($cart->id!=$prev_cart_id) echo $string;?>
            <?php $cart->id!=$prev_cart_id ? $new_cart=true : $new_cart=false;?>
            <?php $cart->id!=$prev_cart_id ? $material_count=1 : $material_count++;?>
            <?php $new_cart ? $refill_sum=0 : false;?>
            <?php $new_cart ? $recikl_sum=0 : false;?>
            <?php $recikl_sum=$recikl_sum+($cart->cost*$cart->kolvo);?>
            <?php if($cart->stage_code=='inrfl') $refill_sum=$refill_sum+($cart->cost*$cart->kolvo);?>
     
                <?php //if($prev_cart_id!=$cart->id):?>
            
            <?php $atantion="";
            if(round($cart->refill-$refill_sum*$kurs_usd_nal)<100) $atantion="<b>(!)</b>";
            $atantion_rck="";
            if(round($cart->recikl-$recikl_sum*$kurs_usd_nal)<100) $atantion_rck="<b>(!)</b>";?>
    <?php $string="<tr>
        <td><b>$cart->name</b><br><i>$cart->printer_name</i></td>
        <td style=\"border-left: 2px black double\"><a href=\"javascript:void(0);\" 
               onclick=\"window.open('/cartridges/item/$cart->id', '_blank');\">Р</a>($material_count)|
          <a href=\"javascript:void(0);\" 
               onclick=\"window.open('http://ddruk.center/admin/cartridge/cartridge.php?act=view_cart&cart_id=$cart->id', '_blank');\">Ц</a>         
        </td>
                   

        <td><i>".round($refill_sum*$kurs_usd_nal)."</i></td>   
        <td><input style=\"width: 40px\" class=\"span1 small\" type=\"text\" name=\"cena_zapravki-".$cart->id."\" value=\"".$cart->refill."\">
        </td>
        <td>".round($cart->refill-$refill_sum*$kurs_usd_nal).$atantion."</td>
        <td><i>".round($recikl_sum*$kurs_usd_nal)."</i></td>   
        <td><input style=\"width: 40px\" class=\"span1 small\" type=\"text\" name=\"cena_vostanovlenia-".$cart->id."\" value=\"".$cart->recikl."\">
        </td>
        <td>".round($cart->recikl-$recikl_sum*$kurs_usd_nal).$atantion_rck."</td>
        <td></td>   
            
    </tr>";?>
            
    <?php $prev_cart_id=$cart->id;?>
    <?php //endif;?>
    <?php endforeach;?>
        </tbody>
    
</table>
<button type="submit">Сохранить</button>
</form>
    
