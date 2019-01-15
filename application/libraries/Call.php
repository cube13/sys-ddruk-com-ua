<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of call
 *
 * @author volodymyrshvayko
 */
class call
{
    function __construct()
    {
        $this->serverIp='89.162.234.194';
        $this->serverPort='5038';
        $this->userName='crm';
        $this->secret='baron137';
        $this->timeout='100';
    }

    public function makeCall($clientNum, $userExtension)
    {
echo $_SERVER['REMOTE_ADDR'];
       // echo $this->serverIp;
       // echo $clientNum;
       // echo $userExtension;
        $connection=fsockopen($this->serverIp,$this->serverPort,$errNum,$errStr,$this->timeout);

        $readSocket=fread($connection,1024);
        print_r($readSocket);
        sleep(2);

        fwrite($connection,"Action: login\r\n
                UserName: crm\r\n
                Secret: baron137\r\n\r\n");
        print_r(socket_get_status($connection));

        echo "<br>";
        $readSocket=fread($connection,1024);
        print_r($readSocket);
        sleep(2);

//        fwrite($connection,'Action: Originate\r\n
//Channel: SIP/golden/'.$clientNum.'\r\n
//Callerid: 0443928687\r\n
//Context: extnumbers\r\n
//Exten: '.$userExtension.'\r\n
//Priority: 1\r\n
//WaitTime: 20000\r\n\r\n');

        print_r(socket_get_status($connection));
        echo "<br>";
        $readSocket=fread($connection,1024);
        sleep(2);

        print_r($readSocket);
        fwrite($connection,'Action: logoff');
    }


/*Action: Originate
Channel: SIP/golden/2326757
Callerid: 0443928687
Context: extnumbers
Exten: 201
Priority: 1
WaitTime: 20000*/

}
