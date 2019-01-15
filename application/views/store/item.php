<!--
id 	article 	name 	units 	prefix 	partnumber 	cost 	price 	available
-->
<?php $label=array('id'=>'id', 	
    'article'=>'Артикул', 
    'name'=>'Наименование',
    'units'=>'Единицы',
    'prefix'=>'Префикс',
    'partnumber'=>'Партномер',
    'cost'=>'Закупка, $',
    'price'=>'Цена, $',
    'min_available'=>'Минимум',
    'available'=>'Наличие');

$placeholder=array('prefix'=>'0-послуга; 1-ЗИП; 2-продажа',
        'cost'=>'для послуги в грн; для товару в $',
        'price'=>'для послуги в грн; для товару в $');


$form='';
foreach ($item as $key => $value){    
    if($key=='id' || $key=='text')
    {
        
    }
    elseif($key=='available')
    {
    $available='<div class="lead">'.$label[$key].': '.$value.nbs().$item->units.'</div>';
    }
    else
    {
    $form.='<div class="control-group">';
    $form.='<div class="input-prepend">';
    $form.='<span class="add-on"  style="width:100px; text-align: left;">'.$label[$key].'</span>';
    $form.='<input class="span3 '.$disable.'" id="'.$key.'" value="'.$value.'" size="16" type="text" name="'.$key.'" placeholder="'.$placeholder[$key].'">';
    
    $form.='</div>';
    $form.='</div>';
    }
}
?>

 <div class="row">
     <div class="span4"> 
         
         <form class="" action="/store/update_item/<?php echo $item->id;?>" method="post">
    <?php echo $form;?>
    <div class="control-group">
        <div class="controls">
        <button type="submit" class="btn">Записать</button>
        </div>
    </div>
    </form>
         
         <div class="row">
             <div class="span2">
             В группах
         <?php if($groups_in->num_rows()>0):?>
         <table class="table-condensed table-bordered">
         <?php foreach ($groups_in->result() as $group_in):?>
             <tr><td><?php echo $group_in->name;?></td>
         <td><a href="/store/remove_item_from_group/<?php echo $item->id;?>/<?php echo $group_in->id;?>"><i class="icon-minus"></i></a></td>
         <?php endforeach;?>
         </table>
         <?php         endif;?>
         
             </div>
             <div class="span2">Включить в группу
             <?php if($groups->num_rows()>0):?>
         <table class="table-condensed table-bordered">
         <?php foreach ($groups->result() as $group):?>
             <tr><td><?php echo $group->name;?></td>
         <td><a class="" href="/store/add_item_to_group/<?php echo $item->id;?>/<?php echo $group->id;?>"><i class="icon-plus"></i></a></td>
         <?php endforeach;?>
         </table>
         <?php         endif;?>
             </div>
         </div>
         <br><br>
        <?php echo $available;?>
          <div class="input-append">
         <form class="" action="/store/incoming/<?php echo $item->id;?>" method="post" >
             <input class="span2" value="" size="16" type="text" name="amount">
                         <button class="btn" type="submit">приход</button>
         </form>
         </div>     
     </div>
     <div class="span3">
         Совместимости по картриджам 
         <?php if($edit_cart_table==0) :?><a href="/store/edit_item/<?php echo $item->id;?>/1"><i class="icon-unlock icon-large"></i></a><?php endif;?>
         <?php if($edit_cart_table==1) :?><a href="/store/edit_item/<?php echo $item->id;?>/"><i class="icon-lock icon-large"></i></a><?php endif;?>
         <?php if($this->ion_auth->is_admin()):?>
         <br/>
         <a href="/store/add_item_to_all/<?php echo $item->id;?>/">Добавить ко всем</a>
         <br/>
         <?php endif;?>
         <table class="table table-striped table-condensed"><thead><tr><th>Картридж</th>
                     <th>кол-во, <?php echo     $item->units;?></th><th></th><tr></thead>
         <?php 
     $not_in='';
     foreach ($cart_rashodka->result() as $cart)
     {
         
         echo '<tr><td>'.anchor_popup('/cartridges/item/'.$cart->id_cart, $cart->name).'</td>';
         if($edit_cart_table==1)
         {
             echo '<td><div class="input-append">
             <form action="/cartridges/set_materials/'.$cart->id_cart.'" method="post" >
             <input class="span1" id="'.$item->id.'" value="'.$cart->kolvo.'" size="16" type="text" name="'.$item->id.'">
             <button class="btn" type="submit">ок</button>
             <a href="/cartridges/remove_material/'.$cart->id_cart.'/'.$item->id.'/" class="add-on btn btn-danger"><i class="icon-remove-circle icon-white"></i></a></form>
                 </div></td>';
            
         }
         else
         {
          echo '<td>'.$cart->kolvo.'</td>';
         }
            echo '</tr>';
            $stage_code=$cart->stage_code;
            $not_in.=$cart->id_cart.'-'; 
     }  
     $not_in.='1';
     
     ?>
             </table>
     <div class="control-group">
  <div class="controls">
    <div class="input-prepend">
      <span class="add-on"><i class="icon-search"></i></span>
      <input class="span2" id="inputIcon" type="text" name="search" onkeyup="
          get_carts_for_item(this.value+'#<?php echo $item->id;?>'+'#<?php echo $stage_code;?>'+'#<?php echo $not_in;?>');">
    </div>
  </div>
</div>
      <div id="cartridges"></div>
     </div>
      
     <?php if($rel_printers->num_rows()>0):?>
     <div class="span3"> Совместимости по принтерам
           <table class="table table-striped table-condensed"><thead><tr><th></th><tr></thead>
            <?php foreach($rel_printers->result() as $printer):?>
                <?php if($printer->name!=$printer_name):?>
                    <tr><td><?php echo $printer->brand.' '.$printer->name;?></td></tr>
                <?php  endif;?>
                    <?php $printer_name=$printer->name;?>
            <?php endforeach;?>
           </table>
     </div>
     
     <?php endif;?>
 
 </div>


     

 



