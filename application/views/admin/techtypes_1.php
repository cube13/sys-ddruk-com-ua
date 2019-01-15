   <table cellpadding=0 cellspacing=10>
		<tr>
			<th width="150" align="left">Тип</th>
			
			<th width="100" align="left"></th>
                </tr>
        <?php foreach ($techtypes as $type):?>
               <tr>
                <td valign="top"><?php echo anchor('admin/update_techtype/'.$type->id,$type->name);?></td>
		<td valign="top">
					
				</td>
				
			</tr>
		<?php endforeach;?>
   </table>