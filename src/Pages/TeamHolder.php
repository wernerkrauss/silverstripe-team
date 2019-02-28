<?php

namespace Netwerkstatt\Team\Pages;

use Netwerkstatt\Team\Model\TeamMember;
use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\DataList;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;


/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 20.10.2015
 * Time: 11:16
 */

/**
 * StartGeneratedWithDataObjectAnnotator
 * @method DataList|TeamMember[] TeamMembers
 * EndGeneratedWithDataObjectAnnotator
 */
class TeamHolder extends Page
{

    private static $db = [];

    private static $has_one = [];

    private static $has_many = [
        'TeamMembers' => TeamMember::class
    ];

    private static $many_many = [];

    private static $belongs_many_many = [];

    private static $table_name = 'TeamHolder';

    private static $singular_name = 'Team Holder Page';

    private static $plural_name = 'Team Holder Pages';

    private static $icon = 'wernerkrauss/silverstripe-team:images/users.png';


    public function getCMSFields()
    {
        $self =& $this;
        $this->beforeUpdateCMSFields(function (FieldList $fields) use ($self) {

            /**
             * @var GridFieldConfig_RecordEditor $conf
             */
            $conf = GridFieldConfig_RecordEditor::create();

            $conf->addComponent(new GridFieldSortableRows('SortOrder'));

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

        $fields = parent::getCMSFields();

        return $fields;
    }
}

