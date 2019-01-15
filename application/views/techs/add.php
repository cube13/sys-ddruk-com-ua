<div id="infoMessage"><?php echo $message;?></div>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
       

   
<?php //$form_param=array('class'=>'form-horizontal');
$form_param='';?>    
 <?php echo form_open('techs/add_techs2/'.$order_id.'/'.$hash, $form_param);?>
 <legend class="alert-info text-center">Внесение техники в заказ №<?php echo $order_id;?></legend>
    <div class="controls">
        <div class="row">  
            <div class="span12">
        <?php echo form_dropdown('tech_type', $techtypes,'','class="span4"');?></div>
            <div class="span12">
        <?php echo form_input($serial_num);?></div>
        
        </div>
        <div id='get_tech' class="span12"></div>
    </div>
    
    
    <div class="controls text-center">
        <?php $button_param = array(
                    'name' => 'button',
            'id' => 'button',
            'type' => 'submit',
            'content' => 'Далее >',
            'autocomplete'=>'off',
            'class'=>'btn btn-primary btn-large');?>
        <?php echo form_button($button_param); ?>    
    </div>
    
  <?php echo form_close();?> 

</body>
</html>