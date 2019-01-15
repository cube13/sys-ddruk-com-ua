<?php $id = 0; ?>

<?php echo '<' . anchor('partners/create_price_cart/' . $partner->id, 'Сформировать прайс') . '><br/>'; ?>
<?php if ($prices->num_rows() > 0): ?>

    <?php echo form_open('partners/change_price/'); ?>
    <table class="table table-condensed table-bordered span10">
        <tr style="font: bold 14px black">
            <td class="span1">id</td>
            <td class="span3">Картридж</td>
            <td class="span1">Заправка</td>
            <td class="span1">Восстановление</td>
        </tr>
        <?php foreach ($prices->result() as $price): ?>
            <?php if ($id != $price->cart_id) {

                $price_input = array('name' => $price->id,
                    'type' => 'text',
                    'size' => '4',
                    'value' => $price->price
                );
                $price->locked ? $price_form = '<table width="100%" border=0><tr><td align="left">' . $price->price . '</td><td align="right">' . anchor('partners/unlock_price_item/' . $price->id, 'O') . '</td></tr></table>' :
                    $price_form = '<table  width="100%"><tr><td td align="left">' . form_input($price_input) . '</td><td align="right">' . anchor('partners/lock_price_item/' . $price->id, 'З') . '</td></tr></table>';

                echo "<tr bgcolor='#eeeeee'><td>".$price->cart_id."</td><td width='250'>" . $price->brand . " " . $price->name . "</td><td width='125'>" . $price_form . "</td>";
            } else {
                $price_input = array('name' => $price->id,
                    'type' => 'text',
                    'size' => '4',
                    'value' => $price->price
                );

                $price->locked ? $price_form = '<table width="100%" border=0><tr><td align="left">' . $price->price . '</td><td align="right">' . anchor('partners/unlock_price_item/' . $price->id, 'O') . '</td></tr></table>' :
                    $price_form = '<table  width="100%"><tr><td td align="left">' . form_input($price_input) . '</td><td align="right">' . anchor('partners/lock_price_item/' . $price->id, 'З') . '</td></tr></table>';

                echo "<td>" . $price_form . "</td></tr>";
            }
            ?>

            <?php $id = $price->cart_id; ?>

        <?php endforeach; ?>
    </table>
    <?php echo form_submit('submit', 'Сохранить цены') ?>
<?php endif; ?>