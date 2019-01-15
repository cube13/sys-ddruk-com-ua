<br><table class="table table-bordered table-condensed table-striped table-hover">
		<tr>
			<th width="150" align="left"><?php echo lang('form_create_user_company');?></th>
                        <th width="150" align="left"><?php echo lang('partners_adres');?></th>
                        <th width="150" align="left">Договор</th>
                        
			<th width="100" align="left"></th>
                </tr>
        <?php foreach ($partners->result() as $partner):?>
                <tr >
			<td bgcolor=<?php echo $color;?> valign="top"><?php echo anchor('partners/view/'.$partner->id,$partner->short_name);?></td>
			<td bgcolor=<?php echo $color;?> valign="top"><?php echo $partner->adres;?></td>
                        <td bgcolor=<?php echo $color;?> valign="top" width="100"><?php echo $partner->contract;?></td>
                        <td bgcolor=<?php echo $color;?> valign="top">
                        <?php if(!$partner->edrpou || strlen($partner->edrpou)<7) echo anchor_popup('/partners/edit/'.$partner->id,'ЕДРПОУ, ИНН');?></td>
                        <?php if($color=="#ffffff") $color="#eeeeee";
                    else $color="#ffffff";?>
		</tr>
        <?php endforeach;?>
                
   </table>
<?php if($this->ion_auth->is_admin()):?>
<p>Всього організацій <?php echo $partners->num_rows();?></p>
<?php endif;?>