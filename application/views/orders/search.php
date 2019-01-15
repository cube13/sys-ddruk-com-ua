<script>
    function order_search()
        {
            id=$("input[name='order_id']").val();
            if (id.length == 0)
            {
                $('#order').fadeOut(500);
            }
            else
            {
                $.ajax({
                    type: "GET",
                    url: "/orders/search_action/"+id,
                    data: "",
                    cache: false,
                    success: function(html){
                        $("#order").html(html);
                        $('#order').fadeIn(500);

                    }
                });
            }

          }  
      

    </script>
<form action="javascript:void(null);" method="post" accept-charset="utf-8" onsubmit="order_search();">

    <input type="text" name="order_id" value="" id="order_id" size="30"  autocomplete="off" />
    <button type="button" class="btn" onclick="order_search();">Поиск</button>
</form>

<div id="order" style=""></div>
    
    