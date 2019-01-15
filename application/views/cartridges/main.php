<script>
    function main_table()  
        {  
            $.ajax({
                 type: "GET",
                url: "/cartridges/main_table/",
                data: "",
                cache: false,  
                success: function(html){  
                    $("#main_table").html(html);
                }  
            });
          }  
          
                    
    $(function()
    {
        main_table();  
        setInterval('main_table()',30000);
    }
);
    </script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<div id="main_table" class=""></div>
    
