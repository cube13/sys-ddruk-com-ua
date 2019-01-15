<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
 <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
 <script type="text/javascript" language="javascript">
         <!--
                        function prn() { i=setTimeout('window.print();',1000); }
--></script>
 <body  onload="prn()">

<?php $nav_tab='';$tab_content='';
 
    function messages_to_table($messages,$code)
    {
        if($messages->num_rows()>0)
        {$count=false;
        $messages_list='';
            foreach($messages->result() as $message)
            {
                //messages.stage_code, stages_tech.name, text, messages.user_id, users.first_name, users.last_name, add_date 
                if($message->stage_code==$code)
                {
                $messages_list.=$message->text.'; ';
                
                $count=true;
                }
            }
            if(!$count) return false;
            $messages_list.='';
            return $messages_list;
        }
        else return false;
    }
    
    function tab_content($active,$stage_code,$order_id,$serial_num,$messages,$label='',$ended=false)
    {
                $content.='<h4>'.$label.'</h4>';
                $content.=messages_to_table($messages, $stage_code);
          
            $content.='';        
            
            return $content;
    }

      
     foreach($stages as $stage)
    {   $active=''; $ended=true;
    if($stage->stage_code=='inofc')
    {
         if($stage->date_end==0) 
         {
             $ended=false;
         }
         $date_end=$stage->date_end;
            $tab_content.=tab_content($active, $stage->stage_code, $order_id, $serial_num, $messages,'Описание проблемы (со слов клиента)',$ended);
    }
            
    }
 ?>
 <h2 class="text-center">Заказ №<?php echo $order_id;?></h2>
 <h3 class="text-center"><?php echo $tech_info->name;?> | S/N: <?php echo $serial_num;?></h3>
 <div style="border:2px solid black"> 
<?php echo tab_content(0, 'attach', $order_id, $serial_num, $messages,'Аксессуары');?>
 </div>
 <br>
 <div style="border:2px solid black"> 
<?php echo $tab_content;?>
 </div>
 <?php $local_date=gmt_to_local($date_end, $this->config->item('timezone'), $this->config->item('$daylight_saving'));?>
            
 <H3>Диагностику выполнить до: 15:00 <?php echo date('d/m/y',$local_date+3600*24);?>
</H3>
 <table><tr><td>Приемщик:</td><td><?php echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->last_name;?>
</td></tr></table>
      
 
 
