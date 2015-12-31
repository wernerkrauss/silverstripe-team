<?php
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

    private static $db = array();

    private static $has_one = array();

    private static $has_many = array(
        'TeamMembers' => 'TeamMember'
    );

    private static $many_many = array();

    private static $belongs_many_many = array();

    private static $singular_name = 'Team Holder Page';

    private static $plural_name = 'Team Holder Pages';

    private static $icon = 'team/images/users.png';


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

class TeamHolder_Controller extends Page_Controller
{
    private static $item_class = 'TeamMember';

    private static $url_handlers = array(
        '$Item!' => 'show',
    );

    private static $allowed_actions = array(
        'show'
    );

    private static $page_length = 10;

    public function index()
    {
        return $this;
    }

    /**
     * action for showing a single news item
     */
    public function show()
    {
        $templates = SSViewer::get_templates_by_class(__CLASS__, '_show');
        $templates[] = 'Page';

        //use this if you need e.g. different template for ajax
        $this->extend('updateTemplatesForShowAction', $templates);

        return $this->renderWith($templates);
    }

    /**
     * Returns all events unfiltered.
     * @return DataList
     */
    public function getItems()
    {
        $itemClass = $this->stat('item_class');

        $items = $itemClass::get();

        //move it to an extension?
        if (Versioned::current_stage() === 'Live') {
            $items = $items->filter('IsActive', 1);
        }

        $this->extend('updateGetItems', $items);

        return $items;
    }

    /**
     * @param string $type future, all, past
     * @return PaginatedList
     */
    public function getPaginatedItems()
    {
        $items = $this->getItems();
        $paginatedList = new PaginatedList($items, $this->request);
        $paginatedList->setPageLength($this->stat('page_length'));
        $paginatedList->setLimitItems(true);
        return $paginatedList;
    }

    public function getItem()
    {
        return $this->getItems()->filter(['URLSlug' => $this->request->param('Item')])->first();
    }
}
