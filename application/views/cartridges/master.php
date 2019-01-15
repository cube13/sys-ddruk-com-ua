<script>
    function cart_list()  
        {
            $("#refresh_cart_list").attr("disabled",true);
            $("#icon-refresh").addClass('icon-spin');
            $.ajax({
                 type: "GET",
                url: "/cartridges/cartridg_master_list",
                data: "",
                cache: false,  
                success: function(html){  
                    $("#cart_list").html(html);
                    $("#refresh_cart_list").attr("disabled",false);
                    $("#icon-refresh").removeClass('icon-spin');

                }  
            });
          }  
                    
    $(function()
    {
        cart_list();  
        setInterval('cart_list()',60000);
    }
);
    </script>
  
  <?php
 ?>
    <div style="background-color:red; color:white;text-align: center;">ВНИМАНИЕ! Выделенное красным делать в первую очередь</div>
    <br>
    <div class="" id="cart_list"></div>
    
