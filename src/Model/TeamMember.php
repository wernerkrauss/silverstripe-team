<?php

namespace Netwerkstatt\Team\Model;


use Netwerkstatt\Team\Pages\TeamHolder;
use Nightjar\Slug\Slug;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Versioned\Versioned;


/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 20.10.2015
 * Time: 11:12
 */


/**
 * StartGeneratedWithDataObjectAnnotator
 * @property string DegreeFront
 * @property string FirstName
 * @property string Surname
 * @property string DegreeBack
 * @property string Position
 * @property string Description
 * @property string Tel
 * @property string Email
 * @property boolean IsActive
 * @property int SortOrder
 * @property string URLSlug
 * @property int TeamHolderID
 * @property int PortraitID
 * @method TeamHolder TeamHolder
 * @method Image Portrait
 * @mixin Slug("Title", null, true)
 * EndGeneratedWithDataObjectAnnotator
 */
class TeamMember extends DataObject implements PermissionProvider
{

    private static $extensions = [
        Slug::class . '("Title", null, true)', //adds URLSlug field and some logic
    ];

    private static $db = [
        'DegreeFront' => 'Varchar(64)',
        'FirstName' => 'Varchar(255)',
        'Surname' => 'Varchar(255)',
        'DegreeBack' => 'Varchar(64)',
        'Position' => 'Varchar',
        'Description' => 'Text',
        'Tel' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'IsActive' => 'Boolean',
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'TeamHolder' => TeamHolder::class,
        'Portrait' => Image::class,
    ];

    private static $owns = [
        'Portrait'
    ];

    private static $table_name = 'TeamMember';

    private static $singular_name = 'Employee';

    private static $plural_name = 'Employees';

    private static $summary_fields = [
        'Surname' => 'Nachname',
        'FirstName' => 'Vorname'
    ];

    private static $searchable_fields = ['Surname', 'Description'];

    private static $upload_path = 'team';

    private static $dummy_image = 'dummy.jpg';

    private static $default_dummy = 'wernerkrauss/silverstripe-team:images/dummy.jpg';

    private static $default_sort = 'SortOrder';

    /**
     * for configuring fluent
     * @var array
     */
    private static $translate = [
        'Position',
        'Description',
        'DegreeFront'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['SortOrder', 'TeamHolderID']);

        $fields->dataFieldByName('Portrait')->setFolderName($this->stat('upload_path'));

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getTitle()
    {
        return $this->Surname . ' ' . $this->FirstName;
    }

    /**
     * Helper function to display the full name with degrees
     * @return string
     */
    public function getName()
    {
        $name = join(' ', array_filter([$this->DegreeFront, $this->FirstName, $this->Surname]));

        return join(', ', array_filter([$name, $this->DegreeBack]));
    }

    public function getPortraitPhoto()
    {
        return $this->PortraitID ? $this->Portrait() : $this->getDummyPortrait();
    }


    public function getDummyPortrait()
    {
        $dummyName = $this->stat('dummy_image');
        $uploadPath = $this->stat('upload_path');

        $dummyPic = Image::find(join('/', [$uploadPath, $dummyName]));

        if (!$dummyPic) {
            $dummyPath = ModuleResourceLoader::singleton()->resolvePath($this->stat('default_dummy'));

            //create it
            $defaultDummy = join('/', [BASE_PATH, $dummyPath]);
            $assetsDummy = join('/', [$uploadPath, $dummyName]);

            $dummyPic = Versioned::withVersionedMode(function () use ($defaultDummy, $assetsDummy) {
                $dummyPic = Image::create();
                $dummyPic->setFromLocalFile($defaultDummy, $assetsDummy);
                $dummyPic->write();
                $dummyPic->doPublish();

                return $dummyPic;
            });

        }

        return $dummyPic;
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canCreate($member = null, $context = [])
    {
        $parent = parent::canCreate($member, $context);

        $manage = Permission::check('TEAM_MANAGE', 'any', $member);
        $create = Permission::check('TEAM_CREATE', 'any', $member);

        return $parent || $manage || $create;
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canEdit($member = null)
    {
        $member = $member ?: Member::currentUser();
        $parent = parent::canCreate($member);

        $manage = Permission::check('TEAM_MANAGE', 'any', $member);
        $owner = $member ? $this->OwnerID == $member->ID : false;

        return $parent || $manage || $owner;
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canDelete($member = null)
    {
        $parent = parent::canCreate($member);

        $manage = Permission::check('TEAM_MANAGE', 'any', $member);

        return $parent || $manage;
    }

    /**
     * Return a map of permission codes to add to the dropdown shown in the Security section of the CMS.
     * array(
     *   'VIEW_SITE' => 'View the site',
     * );
     */
    public function providePermissions()
    {
        return [
            'TEAM_MANAGE' => [
                'name' => _t('Team.PERMISSION_MANAGE_DESCRIPTION', 'Create, edit and delete Teams'),
                'category' => _t('Permissions.TEAM_CATEGORY', 'Teams'),
            ],
            'TEAM_CREATE' => [
                'name' => _t('Team.PERMISSION_CREATE_DESCRIPTION', 'Create Teams'),
                'category' => _t('Permissions.TEAM_CATEGORY', 'Teams'),
            ]
        ];
    }

    /**
     * Link to this DO
     * @return string
     */
    public function Link()
    {
        $link = $this->TeamHolderID ? $this->TeamHolder()->Link($this->URLSlug) : '';

        $this->extend('UpdateLink', $link);

        return $link;
    }

    /**
     * The absolute URL of this DataObject. Needed for sitemap.xml
     *
     * @return string
     */
    public function AbsoluteLink()
    {
        return Director::absoluteURL($this->Link());
    }
}
