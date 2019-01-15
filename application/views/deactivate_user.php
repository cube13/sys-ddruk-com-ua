<p>Ви дійсно бажаєте деактивувати користувача '<?php echo $user->username; ?>'</p>
	
    <?php echo form_open("admin/deactivate_user/".$user->id);?>
    	
      <p>
      	<label for="confirm">Так:</label>
		<input type="radio" name="confirm" value="yes" checked="checked" />
      	<label for="confirm">Ні:</label>
		<input type="radio" name="confirm" value="no" />
      </p>
      
      <?php echo form_hidden($csrf); ?>
      <?php echo form_hidden(array('id'=>$user->id)); ?>
      
      <p><?php echo form_submit('submit', 'Відправити');?></p>

    <?php echo form_close();?>

</div>
