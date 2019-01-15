<?php if($groups->num_rows()>0):?>

<?php foreach ($groups->result() as $group):?>
<div class="input-append">
<form class="" action="/store/rename_group" method="post" >
             <input class="span2" value="<?php echo $group->name;?>" 
                    size="16" type="text" name="<?php echo $group->id;?>">
                         <button class="btn" type="submit"><i class="icon-ok"></i></button>
                         <a href="/store/remove_group" class="btn btn-danger"><i class="icon-remove"></i></a>
         </form>
    </div>
<br>
<?php endforeach;?>

<?php endif;?>
          <div class="input-append">
         <form class="" action="/store/add_group" method="post" >
             <input class="span2" value="" size="16" type="text" name="name">
                         <button class="btn" type="submit">+</button>
         </form>
         </div>
    