<form class="form-inline" action="/store/writeoff" method="post">
<input style="width:100px;" type="text" name="date" value="<?php echo $date;?>" id="datepicker"/> 

<input type="submit" name="ok" value="ok" class="btn">
  </form>

<h4>Списаные материалы</h4>
<div class="row">
    <div class="span7">
        
<table class="table table-condensed table-striped">
    <tr>
        <th width="200">Картридж</th>
        <th width="50">№</th>
        <th width="200">материалы</th>
        <th width="50">кол-во</th>
        <th width="50">цена1</th>
        <th width="50">цена2</th>
        <th width="*"></th>
    </tr>
    <?php $prev_num='';$SUM=0;$item_array=array();$count_cart=0; $COST=0; ?>
    <?php foreach($writeoff->result() as $item):?>
    <tr>
        <!--<td width="120"><?php if($item->uniq_num!=$prev_num) echo $item->cart_name;?></td>-->
        <td width="120"><?php  echo $item->cart_name;?></td>
        <td width="50"><?php if($item->uniq_num!=$prev_num) echo $item->uniq_num;?></td>
        <td width="150"><?php echo $item->item_name;?></td>
        <td width="150"><?php echo $item->amount;?></td>
        <td width="50"><?php if($item->uniq_num!=$prev_num)
        {//echo $item->price; $SUM=$SUM+$item->price; $count_cart++;
            }?></td>
        <td width="50"><?php echo $cost=$item->cost*$item->amount; ?></td>
        <td><?php echo $item->last_name;?></td>
    </tr>
        <?php $COST=$COST+$cost*30;?>
    <?php if($item->uniq_num!=$prev_num) {//echo $item->price;
    $SUM=$SUM+$item->price;
    $count_cart++;}?>
    
    <?php $prev_num=$item->uniq_num;?>
        <?php $item_array[$item->item_id]['sum']+=$item->amount;?>
        <?php $item_array[$item->item_id]['name']=$item->item_name;?>
        <?php $item_array[$item->item_id]['cost']=$item->cost;?>

    <?php endforeach;?>

    <?php if($this->ion_auth->is_admin()):?>
    <tr><td>Картриджей:</td><td><?php echo $count_cart;?></td><td>Всего на сумму:</td><td><?php echo $SUM;?></td>
    <?php $profit=$SUM+$COST;?>
        <td><?php echo $COST.' '.$profit;?></td></tr>
    <?php endif;?>
    
</table>
        </div>
    <div class="span4">
        <table class="table table-condensed table-striped">
    <tr>
        <th width="200">Материал</th>
        <th width="80">Кол-во</th>
        <th width="50">цена</th>
        <th width="50">сумма</th>
    </tr>
    <?php asort($item_array);?>
    <?php foreach($item_array as $item):?>
    <tr><td><?php echo $item['name'];?></td>
        <td><?php echo -$item['sum'];?></td>
        <td><?php echo $item['cost'];?></td>
        <td><?php echo -$item['sum']*$item['cost'];?></td>
    </tr>
        <?php $sumCost=$sumCost+$item['sum']*$item['cost'];?>
    <?php endforeach;?>
            <tr>
                <td>Всего</td>
                <td></td>
                <td></td>
                <td><?php echo -$sumCost;?></td></tr>
        
    
    
    </div>
    </div>



