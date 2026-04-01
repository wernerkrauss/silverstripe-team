<?php

/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 27.10.2015
 * Time: 15:21
 */

namespace Netwerkstatt\Team\Admin;

use Netwerkstatt\Team\Model\TeamMember;
use SilverStripe\Admin\ModelAdmin;

class TeamAdmin extends ModelAdmin
{
    /**
     * @config
     */
    private static $menu_title = 'Team';

    /**
     * @config
     */
    private static $managed_models = [TeamMember::class];

    /**
     * @config
     */
    private static $url_segment = 'team';

    /**
     * @config
     */
    private static $menu_icon = 'team/images/users.png';
}
