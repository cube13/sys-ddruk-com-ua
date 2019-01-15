<?php
require_once 'websockets.php';

class Server_websocket extends WebSocketServer
{
    protected $ci;
    
    public function __construct($args=array()) 
    {
        parent::__construct($addr, $port, $bufferLength);
        call_user_func_array('parent::__cunstruct',$args);
        $this->ci=&get_instance();
    }
    
    protected function process($user, $message) 
    {
        foreach ($this->users as $usr)
        {
            $this->send($usr,  utf8_decode($message));
        }
    }
    
    protected function connected($user)
    {
        echo ('New client conected');
    }
    
    protected function closed($user) 
    {
        echo ('Client close conection');
    }
}
?>

