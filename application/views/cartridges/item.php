<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

<h2>Картридж <?php echo $cart_item->row()->brand . nbs() . $cart_item->row()->name; ?>
    <small>Расходные материалы</small>
</h2>
<div class="row">
    <div class="span2">

        <img src="http://ddruk.center/<?php echo $cart_item->row()->picture; ?>" class="img-rounded">
    </div>
    <div class="span7">
        <?php if ($rashodka->num_rows() > 0): ?>

            <form class="form-actions form-horizontal"
                  action="/cartridges/set_materials/<?php echo $cart_item->row()->id; ?>" method="post">
<?php $SUM_RFL=0; $SUM_RCK=0;?>
                <?php foreach ($rashodka->result() as $material): ?>
                    <?php //print_r($material);?>

                    <div class="control-group">
                        <div class="input-prepend input-append"><span class="add-on"
                                                                      style="width:250px;text-align: left;">
        <?php echo anchor_popup('store/edit_item/' . $material->rashodnik_id, $material->name); ?>  (<?php echo $material->stage_code;?>)</span>
                            <input class="span1" value="<?php echo $material->kolvo; ?>" size="16" type="text"
                                   name="<?php echo $material->rashodnik_id; ?>">';
                            <span class="add-on"
                                  style="width:20px;text-align: left;"><?php echo $material->units; ?></span>
                            <span class="add-on"
                                  style="width:20px;text-align: left;"><?php echo $sum=$material->kolvo*$material->cost; ?></span>
                            <a href="/cartridges/remove_material/<?php echo $cart_item->row()->id; ?>/<?php echo $material->rashodnik_id; ?>/"
                               class="add-on btn btn-danger"><i class="icon-remove-circle icon-white"></i></a>


                        </div>
                    </div>
                    <?php
                        if($material->stage_code=='inrfl') $SUM_RFL=$SUM_RFL+$sum;
                        if($material->stage_code=='inrck') $SUM_RCK=$SUM_RCK+$sum;
                    ?>
                <?php endforeach; ?>
                Собівартість заправки: <?php echo $SUM_RFL*30;?>; Собівартість відновлення: <?php echo ($SUM_RCK+$SUM_RFL)*30;?>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn">Записать</button>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>
    <div class="span4">
        Добавление материалов

        <div class="control-group">
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-search"></i></span>
                    <input class="span2" id="inputIcon" type="text" name="search" onkeyup="
                            get_items_for_cart(this.value+'#<?php echo $cart_item->row()->id; ?>');">
                </div>
            </div>
        </div>
        <div id="materials"></div>

    </div>


</div>
     

 



