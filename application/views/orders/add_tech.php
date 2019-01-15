<div id="infoMessage"><?php echo $message;?></div>

<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
       

   
<?php //$form_param=array('class'=>'form-horizontal');
$form_param='';?>    
 <?php echo form_open('techs/add_techs/'.$order_id.'/'.$hash, $form_param);?>
 <legend>Внесение техники в заказ №<?php echo $order_id;?></legend>
    <div class="controls">
        <div class="row">  
            <div class="span4">
        <?php echo form_dropdown('tech_type', $techtypes,'','class="span4"');?></div>
            <div class="span4">
        <?php echo form_input($serial_num);?></div>
        <span id='get_tech'></span>
        </div>
    </div>
    
    <div class="controls">
        <div class="row">  
            <div class="span4">
                <span class="help-block">Аксесcуары</span>
        <label class="checkbox"><input type="checkbox" name="cartridge"><?php echo $attachment['cartridge'];?></label>
        <label class="checkbox"><input type="checkbox" name="psu"><?php echo $attachment['psu'];?></label>
        <label class="checkbox"><input type="checkbox" name="powercable"><?php echo $attachment['powercable'];?></label>
        <label class="checkbox"><input type="checkbox" name="datacable"><?php echo $attachment['datacable'];?></label>
        
        <?php echo $attachment['otherattach'];?> <input type="text" size="30" name="otherattach">
        </div>
             <div class="span4"><textarea class="span8" name="problem" cols="" rows="10" placeholder="описание проблемы..."></textarea></div>
            
        </div>
    </div>
    <div class="controls">
        <?php $button_param = array(
                    'name' => 'button',
            'id' => 'button',
            'type' => 'submit',
            'content' => 'Внести',
            'class'=>'btn btn-primary btn-large');?>
        <?php echo form_button($button_param); ?>    
    </div>
    
  <?php echo form_close();?> 

</body>
</html>