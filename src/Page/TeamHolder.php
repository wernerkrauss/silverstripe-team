<?php

/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 20.10.2015
 * Time: 11:16
 */

namespace Netwerkstatt\Team\Page;

use Override;
use SilverStripe\ORM\HasManyList;
use Netwerkstatt\Team\Model\TeamMember;
use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * StartGeneratedWithDataObjectAnnotator
 * @method HasManyList<TeamMember> TeamMembers()
 * EndGeneratedWithDataObjectAnnotator
 */
class TeamHolder extends Page
{
    /**
     * @config
     */
    private static $table_name = 'TeamHolder';

    /**
     * @config
     */
    private static $has_many = [
        'TeamMembers' => TeamMember::class
    ];

    /**
     * @config
     */
    private static $singular_name = 'Team Holder Page';

    /**
     * @config
     */
    private static $plural_name = 'Team Holder Pages';

    private static $cms_icon = 'team/images/users.png';

    /**
     * @return FieldList
     */
    #[Override]
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {

            /**
             * @var GridFieldConfig_RecordEditor $conf
             */
            $conf = GridFieldConfig_RecordEditor::create();

            if (class_exists(GridFieldSortableRows::class)) {
                $conf->addComponent(new GridFieldSortableRows('SortOrder'));
            }

            $fields->addFieldToTab(
                "Root." . _t('TeamHolder.TeamTabName', 'Team'),
                Gridfield::create(
                    'Team',
                    _t('Team.TeamFieldTitle', 'Team'),
                    $this->TeamMembers(),
                    $conf
                )
            );
        });

        return parent::getCMSFields();
    }
}
