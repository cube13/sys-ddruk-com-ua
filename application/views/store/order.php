<table class="table table-striped table-bordered table-condensed">
    <thead>
        <tr>
            <th width="75px">Артикул</th>
            <th width="250px">Наименование</th>
            <th width="50px">Наличие</th>
            <th width="100px">Минимум</th>
            <th width="100px">заказ.мин.</th>
            <th width="">Совместимость</th>
            
        </tr>
    </thead>

    <?php foreach ($store_order->result() as $item):?>
        <?php if($item->available<$item->min_available):?>
    <?php  $item->article? $article=anchor_popup('store/edit_item/'.$item->id,$item->article): 
            $article=anchor_popup('store/edit_item/'.$item->id,'ред.');?>
        <tr>
            <td><?php echo $article;?></td>
            <td><?php echo $item->name;?></td>
            <td><?php echo $item->available.nbs().$item->units;?></td>
            <td><?php echo $item->min_available;?></td>
            <td><?php echo $item->min_available-$item->available;?></td>
            
            <td><?php echo $item->text;?></td>
        </tr>
        <?php endif;?>
 

<?php endforeach; ?>
</table>
