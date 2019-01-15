<?php echo $stages_menu.br(2);?>
<script>
    function get_stage()  
        {
            $("#refresh_table").attr("disabled",true);
            $("#icon-refresh").addClass('icon-spin');
            $.ajax({
                 type: "GET",
                url: "/orders/mega_table/",
                data: "",
                cache: false,  
                success: function(html){
                    $("#mega-table").html(html);
                    $("#refresh_table").attr("disabled",false);
                    $("#icon-refresh").removeClass('icon-spin');
                }  
            });
          }



    $(function()
    {
        get_stage();  
        setInterval('get_stage()',240000);
    }
);
    </script>
 <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
 <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

<button id="refresh_table" type="button" class="btn btn-small" onclick="get_stage();">
    <i id="icon-refresh" class="icon-refresh"></i>
</button>
 <div id="mega-table"></div>