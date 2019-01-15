<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
 <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
 

<?php $nav_tab='';$tab_content='';
//print_r($messages->result());
 
    function messages_to_table($messages,$code)
    {
        if($messages->num_rows()>0)
        {$count=false;
        $messages_list=br().'<table class="table table-condensed table-bordered">';
            foreach($messages->result() as $message)
            {
                $local_date=gmt_to_local($message->add_date, 'UP1', true);
                
                $messages_list.='<tr>';
                $messages_list.='<td class="muted">'.date("d/m/Y@H:i",$local_date).br().$message->first_name.'</td>';
                $messages_list.='<td width="400">'.$message->text.'</td>';
                $messages_list.='</tr>';
                $count=true;
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
                
            $content='<div class="tab-pane '.$active.'" id="tab-'.$stage_code.'">';
            $content.='<table><tr>';
            
            $content.='<td valign="top">';
             $content.=messages_to_table($messages, $stage_code);
            $content.='</td>';
            $content.='</td>';
            $content.='<td width="20">';
            $content.='</td>';
            
            $content.='<td valign="top">';
            if(!$ended)
            {
                $content.=form_open('/messages/add/'.$order_id.'/'.$serial_num.'/'.$stage_code);
                $content.='<label><b>'.$label.'</b></label>
                <textarea id="text-'.$stage_code.'" name="text"  placeholder="описание проблемы..."></textarea>';
                $content.=form_button($button).form_close();
            
            
            if($stage_code!='attach')
            {
                $content.=br();
                $content.=form_open('/techs/close_stage/'.$stage_code.'/'.$serial_num.'/'.$order_id);
                $content.='<fieldset>
    <label class="checkbox">
      <input type="checkbox" name="close_please"> Этап закончен
    </label>
    <button type="submit" class="btn btn-primary">Отправить</button>
  </fieldset>
</form>';   
            }
            
            }
            
    
            $content.='</tr></table>';        
            $content.='</div>';
            
            return $content;
    }


?>
 <table class="table"><tr>
         <td><h2><?php echo $tech_info->name;?> <small> S/N: <?php echo $serial_num;?></small></h2></td>
         <td><?php 
         $popup_settings = array(
              'width'      => '750',
              'height'     => '570',
              'scrollbars' => 'no',
              'status'     => 'no',
              'resizable'  => 'no',
              'screenx'    => '150',
              'screeny'    => '150');
         
         echo anchor_popup('techs/diag_list/'.$serial_num.'/'.$order_id,'Лист дигностики',$popup_settings)?></td>
 
 </tr></table>

     <?php
      
$java_script='';
      
     foreach($stages as $stage)
    {   $active=''; $ended=true;
         if($stage->date_start!=0&&$stage->date_end==0) $active='active'; 
         if($stage->date_end==0) 
         {
             $ended=false;
             $java_script.='$("#text-'.$stage->stage_code.'").cleditor({width:400, height:250, useCSS:true})[0].focus();';
             
         }
    
         $nav_tab.='<li class="'.$active.'">
            <a href="#tab-'.$stage->stage_code.'" data-toggle="tab">'.$stage->stage_name.'</a></li>';
    
            $tab_content.='<div class="tab-pane '.$active.'" id="tab-'.$stage->stage_code.'">';
            $tab_content.=tab_content($active, $stage->stage_code, $order_id, $serial_num, $messages,'Пояснения к этапу',$ended);
            $tab_content.='</div>';
            
    }
  //print_r($stages) `adres`,`info`,`stage_code`,`manager_id`,`date_end`,`date_start` 
  //print_r($messages);
 ?>
 
 
 <div class="tabbable">
  <ul class="nav nav-tabs">
    <?php echo $nav_tab;?>
      <li><a href="#tab-attach" data-toggle="tab">Аксессуары</a></li>
  </ul>
  <div class="tab-content">
      <div class="tab-pane" id="tab-attach">
          <?php echo tab_content(0, 'attach', $order_id, $serial_num, $messages,'Внести аксессуары');
          $java_script.='$("#text-attach").cleditor({width:400, height:250, useCSS:true})[0].focus();';?>
      </div>
     <?php echo $tab_content;?>
  </div>
</div>
 
 <script>
 $(document).ready(function() {
        <?php echo $java_script;?>
      });
    </script>
 
 
 
