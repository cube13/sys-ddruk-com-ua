<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
 <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
 

<?php $nav_tab='';$tab_content='';
//print_r($messages->result());
 
    function messages_to_table($messages,$code)
    {
        if($messages->num_rows()>0)
        {$count=false;
        $messages_list='<table class="table table-condensed table-bordered alert-info" width="650">';
            foreach($messages->result() as $message)
            {
                //messages.stage_code, stages_tech.name, text, messages.user_id, users.first_name, users.last_name, add_date 
                if($message->stage_code==$code)
                {
                $messages_list.='<tr>';
                $messages_list.='<td >'.$message->text.'</td>';
                $messages_list.='<td width="150" class="muted">'.date("d/m/Y@H:i",$message->add_date).br().$message->first_name.'</td>';
                $messages_list.='</tr>';
                $count=true;
                }
            }
            if(!$count) return false;
            $messages_list.='</table>';
            return $messages_list;
        }
        else return false;
    }
    
    function tab_content($active,$stage_code,$order_id,$serial_num,$messages,$label='',$ended=false)
    {
        $button = array(
                    'name' => 'button',
            'id' => 'button',
            'type' => 'submit',
            'content' => 'Записать',
            'class'=>'btn');
                
            
            if(!$ended)
            {
                $content.=form_open('/messages/add/'.$order_id.'/'.$serial_num.'/'.$stage_code);
                $content.='<label><b>'.$label.'</b></label>
                <textarea id="text-'.$stage_code.'" name="text"  placeholder="описание проблемы..."></textarea>';
                $content.=form_button($button).form_close();
                $content.=messages_to_table($messages, $stage_code);
            
            }
    
            $content.='';        
            
            return $content;
    }

    $button_param = array(
                    'name' => 'button',
            'id' => 'button',
            'type' => 'submit',
            'content' => 'Записть',
            'class'=>'btn btn-primary');
      
$java_script='';
      
     foreach($stages as $stage)
    {   $active=''; $ended=true;
    if($stage->stage_code=='inofc')
    {
         if($stage->date_end==0) 
         {
             $ended=false;
             $java_script.='$("#text-'.$stage->stage_code.'").cleditor({width:600, height:150, useCSS:true})[0].focus();';
             
         }
            $tab_content.=tab_content($active, $stage->stage_code, $order_id, $serial_num, $messages,'<h4>Описание проблемы (со слов клиента)</h5>',$ended);
    }
            
    }
 ?>
 <legend class="alert-info text-center">Внесение техники в заказ №<?php echo $order_id;?></legend>
 <h3 class="text-center"><?php echo $tech_info->name;?> <small> S/N: <?php echo $serial_num;?></small></h3>
 
  <div class="alert alert-info">
     
          <?php echo tab_content(0, 'attach', $order_id, $serial_num, $messages,'АКСЕССУАРЫ');
          $java_script.='$("#text-attach").cleditor({width:600, height:100, useCSS:true})[0];';?>
   </div>  
 <div class="alert alert-error">
  <?php echo $tab_content;?>
    </div>
 
      <?php echo form_open('/techs/close_stage/inofc/'.$serial_num.'/'.$order_id);?>
      <label class="checkbox">
      <input type="checkbox" name="close_please">Форму заполнил верно. Все данные корректно внес.
    </label>
    <button type="submit" class="btn btn-primary">Отправить</button>
  
</form>
 
 <script>
 $(document).ready(function() {
        <?php echo $java_script;?>
      });
    </script>
 
 
 
