<?php if($groups->num_rows()>0):?>
<?php foreach ($groups->result() as $group):?>
<?php echo $group->name;?><br>
<?php endforeach;?>
<?php endif;?>
          <div class="input-append">
         <form class="form-actions form-horizontal" action="/store/add_group" method="post" >
             <input class="span2" value="" size="16" type="text" name="name">
                         <button class="btn" type="submit">+</button>
         </form>
         </div>
    