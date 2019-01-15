<script>
    function cart_list()
        {
            $.ajax({
                 type: "GET",
                url: "/cartridges/to_work_list_new/<?php echo $stage_code;?>",
                data: "",
                cache: false,  
                success: function(html){
                    $("#cart_list").html(html);
                }  
            });
          }  
          
                    
    $(function()
    {
        cart_list();  
        setInterval('cart_list()',240000);
    }
);
    </script>
    
    <div id="cart_list"></div>
    
