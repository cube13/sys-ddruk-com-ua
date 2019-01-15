<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>GADC - LOOP</title>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/assets/js/jquery-1.8.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/assets/bootstrap3/js/bootstrap.min.js"></script>
    
<!-- Bootstrap -->
    <link href="/assets/bootstrap3/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
    
     function setServoPosition(servo,pos)
    {
      $.ajax({
                type: "GET",
                url: "driver.php?servo=" + servo + "&pos=" + pos,
                data: "",
                cache: false,
                success: function(html)
                {
                    $("#answer").html(html);
                }
            });
    }
    
   



  </script>
  
  </head>
  <body>
      <table>
          <tr>
              <td>
                  <h1>Сервоприводы</h1>
              </td>
              <td>&nbsp;&nbsp;&nbsp;</td>
              
          </tr>
      </table>
    
    <div id="answer"></div>
    
 <?php
 
 $servos[0]['name']="C0";
 $servos[0]['id']=0;
 $servos[0]['min_pos']=123;
 $servos[0]['max_pos']=145;
 
 $servos[1]['name']="C1";
 $servos[1]['id']=1;
 $servos[1]['min_pos']=130;
 $servos[1]['max_pos']=160;
 
 print_r($servos);
 
 ?>
 
 
    <br>
    <br>
    
    <div class="row">
        
        <div class="col-md-3">
            <?php foreach ($servos as $servo):?>
    <div class="btn-group btn-group-lg" role="group">
        <button type="button" class="btn btn-primery">
            <?php echo $servo['name'];?>
        </button>
        <button type="button" class="btn btn-default" id="setServoPosition" onclick="setServoPosition(<?php echo $servo['id'];?>,<?php echo $servo['min_pos'];?>)">
             <?php echo $servo['min_pos'];?>
        </button>
        <button type="button" class="btn btn-default" id="setServoPosition" onclick="setServoPosition(<?php echo $servo['id'];?>,<?php echo $servo['max_pos'];?>)">
             <?php echo $servo['max_pos'];?>
        </button>
    </div>
    <br>
<?php endforeach;?>
            
        </div>
        
       
    </div>
    
    
  </body>
</html>
