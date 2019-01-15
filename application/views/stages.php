<div style="border: 1px solid black; margin-bottom: 5px;">Этапы работ по картриджам   
<table cellpadding=0 cellspacing=10>
		<tr>
			<th width="250" align="left">Этап</th>
                        <th width="150" align="left">Индекс-код</th>
			<th width="200" align="left">Описание</th>
			<th width="120" align="left">Порядок</th>
			<th width="100" align="left"></th>
                </tr>
                <?php $count=1;?>
        <?php foreach ($stages_cartridge as $stage):?>
                <tr>
                    <td valign="top"><?php echo $count.'. '.$stage->name.nbs(2).anchor('admin/stage_down/'.$stages_cartridge_table.'/'.$stage->id, '&darr;').nbs(2).anchor('admin/stage_up/'.$stages_cartridge_table.'/'.$stage->id, '&uarr;');?></td>
                    <td valign="top"><?php echo $stage->code;?></td>
                    <td valign="top"><?php echo $stage->description;?></td>
                    <td valign="top"><?php echo $stage->sort;?></td>
                    <td valign="top"></td>
                    <?php $count++;?>
		</tr>
	<?php endforeach;?>
   </table>
</div>

<div style="border: 1px solid black; margin-bottom: 5px;">Этапы работ по технике
<table cellpadding=0 cellspacing=10>
		<tr>
			<th width="250" align="left">Етап</th>
                        <th width="150" align="left">Індекс-код</th>
			<th width="200" align="left">Опис</th>
			<th width="120" align="left">Порядок</th>
			<th width="100" align="left"></th>
                         
                </tr>
                <?php $count=1;?>
        <?php foreach ($stages_tech as $stage):?>
                <tr>
                    <td valign="top"><?php echo $count.'. '.$stage->name.nbs(2).anchor('admin/stage_down/'.$stages_tech_table.'/'.$stage->id, '&darr;').nbs(2).anchor('admin/stage_up/'.$stages_tech_table.'/'.$stage->id, '&uarr;');?></td>
                    <td valign="top"><?php echo $stage->code;?></td>
                    <td valign="top"><?php echo $stage->description;?></td>
                    <td valign="top"><?php echo $stage->sort;?></td>
                    <td valign="top"></td>
		</tr>
                 <?php $count++;?>
	<?php endforeach;?>
   </table></div>

<div style="border: 1px solid black; margin-bottom: 5px;">Этапы работ по заказам  
<table cellpadding=0 cellspacing=10>
		<tr>
			<th width="250" align="left">Этап</th>
                        <th width="150" align="left">Индекс-код</th>
			<th width="200" align="left">Описание</th>
			<th width="120" align="left">Порядок</th>
			<th width="100" align="left"></th>
                </tr>
                <?php $count=1;?>
        <?php foreach ($stages_order as $stage):?>
                <tr>
                    <td valign="top"><?php echo $count.'. '.$stage->name.nbs(2).anchor('admin/stage_down/'.$stages_order_table.'/'.$stage->id, '&darr;').nbs(2).anchor('admin/stage_up/'.$stages_order_table.'/'.$stage->id, '&uarr;');?></td>
                    <td valign="top"><?php echo $stage->code;?></td>
                    <td valign="top"><?php echo $stage->description;?></td>
                    <td valign="top"><?php echo $stage->sort;?></td>
                    <td valign="top"></td>
		</tr>
                <?php $count++;?>
	<?php endforeach;?>
   </table>
    </div> 