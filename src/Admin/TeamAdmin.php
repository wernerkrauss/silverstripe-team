<?php

namespace Netwerkstatt\Team\Admin;

use Netwerkstatt\Team\Model\TeamMember;
use SilverStripe\Admin\ModelAdmin;

/**
 * Class \TeamAdmin
 *
 */
class TeamAdmin extends ModelAdmin
{
    private static $managed_models = [
        TeamMember::class
    ];

    private static $url_segment = 'team';

    private static $menu_title = 'Team';

    private static $menu_icon_class = 'font-icon-torso';


}
