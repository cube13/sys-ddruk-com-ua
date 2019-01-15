<br><table class="table table-bordered table-condensed table-striped table-hover">
    <tr>
        <th width="20" align="left">id</th>
        <th width="150" align="left">Номер карточки</th>
        <th width="150" align="left">Количество заправок (всего/исп)</th>
        <th width="50" align="left">Активна</th>
        <th width="100" align="left"></th>
    </tr>
        <?php foreach ($subscription->result() as $subs_item):?>
    <tr >
        <td><?php echo $subs_item->id; ?></td>
        <td><?php echo $subs_item->number; ?></td>
        <td><?php echo $subs_item->capacity.'/'.$subs_item->used; ?></td>
        <td><?php echo $subs_item->is_active ? 'c '.date('d/m/Y H:i:s',$subs_item->activation_date) :  'НЕТ'; ?></td>
        <td></td>
        
    </tr>
        <?php endforeach;?>
                
   </table>