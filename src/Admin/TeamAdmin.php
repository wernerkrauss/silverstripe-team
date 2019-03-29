<?php

namespace Netwerkstatt\Team\Admin;

use LittleGiant\SinglePageAdmin\SinglePageAdmin;
use Netwerkstatt\Team\Pages\TeamHolder;


/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 27.10.2015
 * Time: 15:21
 */
if (class_exists(SinglePageAdmin::class)) {
    class TeamAdmin extends SinglePageAdmin
    {

        private static $menu_title = 'Team';

        private static $tree_class = TeamHolder::class;

        private static $url_segment = 'team';

        private static $menu_icon_class = 'font-icon-torso';
    }
}
