<?php

namespace Netwerkstatt\Team\Admin;

/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 27.10.2015
 * Time: 15:21
 */


use Netwerkstatt\Team\Model\TeamMember;
use SilverStripe\Admin\ModelAdmin;


class TeamAdmin extends ModelAdmin
    {
        private static $menu_title = 'Team';

        private static $managed_models = [TeamMember::class];

        private static $url_segment = 'team';

        private static $menu_icon = 'team/images/users.png';
    }

