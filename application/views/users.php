   <table cellpadding=0 cellspacing=10 class="table table-bordered table-bordered">
		<tr>
            <th>id</th>
			<th width="150" align="left"><?php echo lang('form_create_user_name');?></th>
			<th width="120" align="left"><?php echo lang('form_create_user_login');?></th>
			<th width="120" align="left"><?php echo lang('form_create_user_email');?></th>
			<th width="120" align="left"><?php echo lang('form_create_user_phone');?></th>
                        <th width="120" align="left"><?php echo lang('user_lastlogin');?></th>
                        <th width="100" align="left"><?php echo lang('user_groupsin');?></th>
                        <th width="100" align="left"><?php echo lang('user_groupsnotin');?></th>
			<th width="100" align="left"></th>
                </tr>
        <?php foreach ($users as $user):?>
                
			<tr>
                <td valign="top"><?php echo $user->id;?></td>
				<td valign="top"><?php echo $user->first_name." ".$user->last_name;?></td>
				<td valign="top"><?php echo anchor('admin/update_user/'.$user->id,$user->username);?></td>
				<td valign="top"><?php echo $user->email;?></td>
				<td valign="top"><?php echo $user->phone;?></td>
                                <td valign="top"><?php echo date('d.m.Y H:i',$user->last_login);?></td>
                                <td valign="top">
					<?php foreach ($user->groups as $group):?>
						<?php echo anchor('admin/remove_from_group/'.$user->id.'/'.$group->id,lang($group->description));?><br />
                                                
	                <?php endforeach?>
				</td>
                                <td valign="top">
					<?php foreach ($groups as $group):?>
						<?php echo anchor('admin/add_to_group/'.$user->id.'/'.$group->id,lang($group->description));?><br />
                                                
	                <?php endforeach?>
				</td>
                                
				<td valign="top"><?php echo ($user->active) ? "Вкл".nbs(1).anchor("admin/deactivate_user/".$user->id, 'Выкл') : "Выкл".nbs(1).anchor("admin/activate_user/". $user->id, 'Вкл'); ?><br>
                                <?php echo anchor('admin/update_user_passwd/'.$user->id, lang('form_change_password_submit'));?></td>
			</tr>
		<?php endforeach;?>
   </table>