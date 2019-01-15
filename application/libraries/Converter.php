<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of converter
 *This class convert numeric money amount to text
 * @author volodymyrshvayko
 */
class converter
{
    function __construct()
    {
        $this->digits=array(0 => "",
            1 => "одна",
            2 => "дві",
            3 => "три",
            4 => "чотири",
            5 => "п'ять",
            6 => "шість",
            7 => "сім",
            8 => "вісім",
            9 => "дев'ять");

        $this->decimal=array(0 => "",
            1 => "",
            2 => "",
            3 => "",
            4 => "",
            5 => "",
            6 => "",
            7 => "",
            8 => "",
            9 => "");
        $this->hundrets=array(0 => "",
            1 => "",
            2 => "",
            3 => "",
            4 => "",
            5 => "",
            6 => "",
            7 => "",
            8 => "",
            9 => "");
    }
    
    

}
