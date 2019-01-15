<div id="infoMessage"><?php echo $message;?></div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="/assets2/vendor/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
       
<script>

  $('#street').autocomplete({
      serviceUrl: '/test/get_cart',
      onSelect: function (suggestion) {
          alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
      }
  });
  </script>
   
 <?php echo form_open();?>

    <div class="controls">
        <div class="row">  
            <div class="span4">
                <label>Улица: </label>
                <input id="street" size="40"/>
            </div>
        </div>
    </div>
   
    <div class="controls">
        <?php $button_param = array(
                    'name' => 'button',
            'id' => 'button',
            'type' => 'submit',
            'content' => 'Внести',
            'class'=>'btn btn-primary btn-small');?>
        <?php echo form_button($button_param); ?>    
    </div>
    
  <?php echo form_close();?> 

</body>
</html>