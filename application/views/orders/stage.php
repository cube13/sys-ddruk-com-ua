<?php echo $stages_menu . br(2); ?>

<script>
    function get_stage() {
        $.ajax({
            type: "GET",
            url: "/orders/stage_dynamic/<?php echo $stage_code;?>/<?php echo $user_id;?>",
            data: "",
            cache: false,
            success: function (html) {
                $("#stage_table").html(html);
            }
        });
    }


    $(function () {
            get_stage(<?php echo $user_id;?>);
            setInterval('get_stage()', 60000);
        }
    );
</script>
<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        Фильтр <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><?php echo anchor('/orders/stage/' . $stage_code . '/0', 'Всі'); ?></li>
        <?php foreach ($couriers as $id => $name):?>
            <li><a href="/orders/stage/<?php echo $stage_code;?>/<?php echo $id;?>"><?php echo $name;?></a></li>
        <?php endforeach;?>
    </ul>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-11">
            <div id="stage_table" style=""></div>
        </div>
    </div>
</div>

    
    