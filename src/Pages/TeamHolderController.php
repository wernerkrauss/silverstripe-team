<?php

namespace Netwerkstatt\Team\Pages;

use GridFieldSortableRows;
use Netwerkstatt\Team\Model\TeamMember;
use PageController;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\SSViewer;


class TeamHolderController extends PageController
{
    private static $item_class = TeamMember::class;

    private static $url_handlers = [
        '$Item!' => 'show',
    ];

    private static $allowed_actions = [
        'show'
    ];

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

    /**
     * Returns all events unfiltered.
     * @return DataList
     */
    public function getItems()
    {
        $itemClass = $this->stat('item_class');

        $items = $itemClass::get();

        //move it to an extension?
        if (Versioned::get_stage() === 'Live') {
            $items = $items->filter('IsActive', 1);
        }

        $this->extend('updateGetItems', $items);

        return $items;
    }

    public function getItem()
    {
        return $this->getItems()->filter(['URLSlug' => $this->request->param('Item')])->first();
    }
}
