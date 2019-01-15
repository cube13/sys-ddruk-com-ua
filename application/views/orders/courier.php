<?php echo $stages_menu.br(2);?>
<script>
    function get_stage(user)  
        {  
            $.ajax({
                 type: "GET",
                url: "/orders/stage_dynamic/toclnt/"+user,
                data: "",
                cache: false,  
                success: function(html){  
                    $("#stage_table").html(html);
                }  
            });
          }  
      
      
    
    $(function()
    {
        get_stage(<?php echo $user_id;?>);  
        setInterval('get_stage(<?php echo $user_id;?>)',20000);
    }
);
    </script>
      <div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Фильтр  <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
      <li><?php echo anchor('/courier/index' . $stage_code . '/14', 'Маша'); ?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/18', 'Юрий'); ?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/24', 'Григорий'); ?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/26', 'Богдан'); ?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/27', 'Егор'); ?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/1', 'Вова');?></li>
      <li><?php echo anchor('/courier/index/' . $stage_code . '/0', 'ВСЕ'); ?></li>
  </ul>
</div>
    <div id="stage_table" style=""></div>
    
    