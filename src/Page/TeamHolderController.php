<?php

namespace Netwerkstatt\Team\Page;

use SilverStripe\ORM\DataList;
use PageController;
use SilverStripe\Model\List\PaginatedList;
use SilverStripe\View\SSViewer;
use SilverStripe\Versioned\Versioned;
use Netwerkstatt\Team\Model\TeamMember;

class TeamHolderController extends PageController
{
    private static $item_class = TeamMember::class;

    /**
     * @config
     */
    private static $url_handlers = [
        '$Item!' => 'show',
    ];

    /**
     * @config
     */
    private static $allowed_actions = [
        'show'
    ];

    private static $page_length = 10;

    public function index()
    {
        $templates = SSViewer::get_templates_by_class(static::class, '');
        $this->extend('updateTemplatesForIndexAction', $templates);

        if ($this->getRequest()->isAjax()) {
            return $this->renderWith($templates);
        }

        return $this;
    }

    /**
     * action for showing a single news item
     */
    public function show()
    {
        $item = $this->getItem();
        if (!$item) {
            return $this->httpError(404);
        }

        $templates = SSViewer::get_templates_by_class(static::class, '_show');
        $templates[] = 'Page';

        //use this if you need e.g. different template for ajax
        $this->extend('updateTemplatesForShowAction', $templates);

        return $this->customise($item)->renderWith($templates);
    }

    /**
     * Returns all events unfiltered.
     * @return DataList
     */
    public function getItems()
    {
        $itemClass = static::config()->get('item_class');

        $items = $itemClass::get();

        //move it to an extension?
        if (Versioned::get_stage() === Versioned::LIVE) {
            $items = $items->filter('IsActive', 1);
        }

        $this->extend('updateGetItems', $items);

        return $items;
    }

    /**
     * @return PaginatedList
     */
    public function getPaginatedItems()
    {
        $items = $this->getItems();
        $paginatedList = PaginatedList::create($items, $this->getRequest());
        $paginatedList->setPageLength(static::config()->get('page_length'));
        $paginatedList->setLimitItems(true);
        return $paginatedList;
    }

    public function getItem()
    {
        return $this->getItems()->filter(['URLSlug' => $this->getRequest()->param('Item')])->first();
    }
}
