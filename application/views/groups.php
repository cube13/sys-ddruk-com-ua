   <table class="table table-bordered table-striped table-condensed">
		<tr>
			<th width="150" align="left">Група</th>
			<th width="200" align="left">Опис</th>
			<th width="120" align="left">Порядок</th>
			<th width="100" align="left"></th>
                </tr>
        <?php foreach ($groups as $group):?>
                
			<tr>
				<td valign="top"><?php echo anchor('admin/update_group/'.$group->id,$group->name);?></td>
				<td valign="top"><?php echo lang($group->description)." (".$group->description.")";?></td>
                                <td valign="top"><?php echo $group->sort.nbs(2).anchor('admin/down_group/'.$group->id, '&darr;').nbs(2).anchor('admin/up_group/'.$group->id, '&uarr;');?></td>
				<td valign="top">
					
				</td>
				
			</tr>
		<?php endforeach;?>
   </table>