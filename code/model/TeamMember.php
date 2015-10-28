<?php
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
class TeamMember extends DataObject implements PermissionProvider {

	private static $extensions = array(
		'Slug("Title", null, true)', //adds URLSlug field and some logic
	);

	private static $db = array(
		'DegreeFront' => 'Varchar(12)',
		'FirstName' => 'Varchar(255)',
		'Surname' => 'Varchar(255)',
		'DegreeBack' => 'Varchar(12)',
		'Position' => 'Varchar',
		'Description' => 'Text',
		'Tel' => 'Varchar(255)',
		'Email' => 'Varchar(255)',
		'IsActive' => 'Boolean',
		'SortOrder' => 'Int'
	);

	private static $has_one = array(
		'TeamHolder' => 'TeamHolder',
		'Portrait' => 'Image',
	);

	private static $singular_name = 'Employee';

	private static $plural_name = 'Employees';

	private static $summary_fields = array(
		'Surname' => 'Nachname',
		'FirstName' => 'Vorname');

	private static $searchable_fields = array('Surname', 'Description');

	private static $upload_path = 'team';

	private static $dummy_image = 'dummy.jpg';

	private static $default_dummy = 'mysite/images/dummy.jpg';

	private static $default_sort = 'SortOrder';

	/**
	 * for configuring fluent
	 * @var array
	 */
	private static $translate = [
		'Position',
		'Description'
	];

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeByName(['SortOrder', 'TeamHolderID']);

		$fields->dataFieldByName('Portrait')->setFolderName($this->stat('upload_path'));

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	public function getTitle() {
		return $this->Surname . ' ' . $this->FirstName;
	}

	/**
	 * Helper function to display the full name with degrees
	 * @return string
	 */
	public function getName() {
		$name = join(' ', array_filter([$this->DegreeFront, $this->FirstName, $this->Surname]));

		return join(', ', array_filter([$name, $this->DegreeBack]));
	}

	public function getPortraitPhoto() {
		return $this->PortraitID ? $this->Portrait() : $this->getDummyPortrait();
	}


	public function getDummyPortrait() {
		$dummyName = $this->stat('dummy_image');
		$uploadPath = $this->stat('upload_path');

		$uploadFolder = Folder::find_or_make($uploadPath);

		$dummyPic = Image::find(join('/', [$uploadPath, $dummyName]));

		if (!$dummyPic) {
			//create it
			$defaultDummy = join('/', [BASE_PATH, $this->stat('default_dummy')]);
			$assetsDummy = join('/', [BASE_PATH, 'assets', $uploadPath, $dummyName]);

			if (copy($defaultDummy, $assetsDummy)) {
				$dummyPic = Image::create();
				$dummyPic->setFilename(join('/', [$uploadFolder->getRelativePath(), $dummyName]));
				$dummyPic->ParentID = $uploadFolder->ID;
				$dummyPic->write();
			}
		}

		return $dummyPic;
	}

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canView($member = null) {
		return true;
	}

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canCreate($member = null) {
		$parent = parent::canCreate($member);

		$manage = Permission::check('TEAM_MANAGE', 'any', $member);
		$create = Permission::check('TEAM_CREATE', 'any', $member);

		return $parent || $manage || $create;
	}

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canEdit($member = null) {
		$member = $member ?: Member::currentUser();
		$parent = parent::canCreate($member);

		$manage = Permission::check('TEAM_MANAGE', 'any', $member);
		$owner  = $member ? $this->OwnerID == $member->ID : false;

		return $parent || $manage || $owner;
	}

	/**
	 * @param null $member
	 * @return bool
	 */
	public function canDelete($member = null) {
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
	public function providePermissions() {
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
	public function Link() {
		$link = $this->TeamHolderID ? $this->TeamHolder()->Link() : '';

		$this->extend('UpdateLink', $link);

		return $link;
	}

}
