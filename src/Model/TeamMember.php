<?php

/**
 * Created by IntelliJ IDEA.
 * User: Werner M. Krauß <werner.krauss@netwerkstatt.at>
 * Date: 20.10.2015
 * Time: 11:12
 */

namespace Netwerkstatt\Team\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use Override;
use Netwerkstatt\Team\Page\TeamHolder;
use Nightjar\Slug\Slug;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use TractorCow\Fluent\Extension\FluentExtension;

/**
 * StartGeneratedWithDataObjectAnnotator
 * @property string $DegreeFront
 * @property string $FirstName
 * @property string $Surname
 * @property string $DegreeBack
 * @property string $Position
 * @property string $Description
 * @property string $Tel
 * @property string $Email
 * @property boolean $IsActive
 * @property int $SortOrder
 * @property string $URLSlug
 * @property int $TeamHolderID
 * @property int $PortraitID
 * @method TeamHolder TeamHolder()
 * @method Image Portrait()
 * @mixin Slug
 * EndGeneratedWithDataObjectAnnotator
 */
class TeamMember extends DataObject implements PermissionProvider
{
    /**
     * @config
     */
    private static $table_name = 'TeamMember';

    /**
     * @config
     */
    private static $extensions = [
        Slug::class . '("Title", null, true)',
        FluentExtension::class,
    ];

    /**
     * @config
     */
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

    /**
     * @config
     */
    private static $has_one = [
        'TeamHolder' => TeamHolder::class,
        'Portrait' => Image::class,
    ];

    /**
     * @config
     */
    private static $singular_name = 'Employee';

    /**
     * @config
     */
    private static $plural_name = 'Employees';

    /**
     * @config
     */
    private static $summary_fields = [
        'Surname' => 'Nachname',
        'FirstName' => 'Vorname'];

    /**
     * @config
     */
    private static $searchable_fields = ['Surname', 'Description'];

    private static $upload_path = 'team';

    private static $dummy_image = 'dummy.jpg';

    private static $default_dummy = 'mysite/images/dummy.jpg';

    /**
     * @config
     */
    private static $default_sort = 'SortOrder';

    /**
     * @TODO: Implement JSON-LD Person schema for output in page <head>
     * This should be gathered by the PageController during page analysis.
     */
    /**
     * for configuring fluent
     * @var array
     * @config
     */
    private static $translate = [
        'Position',
        'Description',
        'DegreeFront'
    ];

    #[Override]
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['SortOrder', 'TeamHolderID']);

        $portraitField = $fields->dataFieldByName('Portrait');
        if ($portraitField instanceof UploadField) {
            $portraitField->setFolderName($this->config()->get('upload_path'));
        }

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    #[Override]
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
        $name = implode(' ', array_filter([$this->DegreeFront, $this->FirstName, $this->Surname]));

        return implode(', ', array_filter([$name, $this->DegreeBack]));
    }

    public function getPortraitPhoto()
    {
        return $this->PortraitID ? $this->Portrait() : $this->getDummyPortrait();
    }


    public function getDummyPortrait()
    {
        $dummyName = $this->config()->get('dummy_image');
        $uploadPath = $this->config()->get('upload_path');

        $uploadFolder = Folder::find_or_make($uploadPath);

        $dummyPic = Image::get()->filter([
            'Name' => $dummyName,
            'ParentID' => $uploadFolder->ID
        ])->first();

        if (!$dummyPic) {
            //create it
            $defaultDummy = implode(DIRECTORY_SEPARATOR, [BASE_PATH, $this->config()->get('default_dummy')]);
            $assetsPath = Director::publicFolder() . DIRECTORY_SEPARATOR . 'assets';
            $assetsDummy = implode(DIRECTORY_SEPARATOR, [$assetsPath, $uploadPath, $dummyName]);

            if (file_exists($defaultDummy) && copy($defaultDummy, $assetsDummy)) {
                $dummyPic = Image::create();
                $dummyPic->setFilename(implode('/', [$uploadPath, $dummyName]));
                $dummyPic->ParentID = $uploadFolder->ID;
                $dummyPic->write();
            }
        }

        return $dummyPic;
    }

    /**
     * @param Member|null $member
     * @return bool
     */
    #[Override]
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @param Member|null $member
     * @param array $context
     * @return bool
     */
    #[Override]
    public function canCreate($member = null, $context = [])
    {
        $parent = parent::canCreate($member, $context);

        $manage = Permission::checkMember($member, 'TEAM_MANAGE');
        $create = Permission::checkMember($member, 'TEAM_CREATE');

        return $parent || $manage || $create;
    }

    /**
     * @param Member|null $member
     * @return bool
     */
    #[Override]
    public function canEdit($member = null)
    {
        $parent = parent::canEdit($member);

        $manage = Permission::checkMember($member, 'TEAM_MANAGE');
        $owner = $member ? $this->getField('OwnerID') == $member->ID : false;

        return $parent || $manage || $owner;
    }

    /**
     * @param Member|null $member
     * @return bool
     */
    #[Override]
    public function canDelete($member = null)
    {
        $parent = parent::canDelete($member);

        $manage = Permission::checkMember($member, 'TEAM_MANAGE');

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
                'name'     => _t('Team.PERMISSION_MANAGE_DESCRIPTION', 'Create, edit and delete Teams'),
                'category' => _t('Permissions.TEAM_CATEGORY', 'Teams'),
            ],
            'TEAM_CREATE' => [
                'name'     => _t('Team.PERMISSION_CREATE_DESCRIPTION', 'Create Teams'),
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
        $teamHolder = $this->TeamHolder();
        if (!$teamHolder || !$teamHolder->exists()) {
            return '';
        }
        $link = Controller::join_links($teamHolder->Link(), $this->URLSlug);

        $this->extend('UpdateLink', $link);

        return $link;
    }
}
