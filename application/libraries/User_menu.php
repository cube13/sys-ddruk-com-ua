<?php
/**
 * Created by PhpStorm.
 * User: volodymyrshvayko
 * Date: 8/30/17
 * Time: 14:33
 */

namespace Usermenu;


class User_menu
{
    public function get_user_menu($usermenu="",$userhere="")
    {
        $user=$this->ion_auth->user()->row();
        if($usermenu)
        {$tomain='';
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach($user_groups as $group)
            {
                $tomain.=anchor($group->name,lang($group->description))." | ";
                if($group->name==$this->uri->segment(1))
                {
                    $userhere=" - ".lang($group->description);
                }

            }
            $this->data['usermenu']=$usermenu;
            $this->data['title']=$user->first_name." ".$user->last_name." - ".$userhere;
            $this->data['tomain']= $tomain;
            //$this->data['tomain']= anchor('main', lang('menu_tomain'));

        }
        else
        {
            $user_groups = $this->ion_auth->get_users_groups()->result();
            foreach($user_groups as $group)
            {
                $usermenu.=anchor($group->name,lang($group->description))." | ";
                if($group->name==$this->uri->segment(1))
                {
                    $userhere=" - ".lang($group->description);
                }

            }
            $this->data['tomain']= "";
            $this->data['usermenu']=$usermenu;
            $this->data['title']=$user->first_name." ".$user->last_name.$userhere;


        }
    }
}