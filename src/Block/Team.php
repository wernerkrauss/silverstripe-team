<?php

namespace Netwerkstatt\Team\Block;


use DNADesign\Elemental\Models\ElementContent;
use Netwerkstatt\Team\Model\TeamMember;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Versioned\GridFieldArchiveAction;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

if (!class_exists(ElementContent::class)) {
    return;
}

class Team extends ElementContent
{
    private static $icon = 'font-icon-torsos-all';
    private static $singular_name = 'Team Member';
    private static $plural_name = 'Team Members';
    private static $description = 'Displays Team Members';
    private static $table_name = 'Netwerkstatt_Elements_TeamMember';

    private static $inline_editable = false;

    private static $many_many = [
        'Members' => TeamMember::class
    ];

    private static $many_many_extraFields = [
        'Members' => [
            'SortOrderElemental' => 'Int'
        ]
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {

            $fields->removeByName('TeamMember');

            if ($this->ID) {
                $gridConfig = GridFieldConfig_RelationEditor::create();
                $gridConfig = $gridConfig->addComponents(
                    new GridFieldOrderableRows('SortOrder'),
                    new GridFieldAddExistingSearchButton()
                )
                    ->removeComponentsByType([
                        GridFieldAddNewButton::class,
                        GridFieldAddExistingAutocompleter::class,
                        GridFieldDeleteAction::class,
                        GridFieldArchiveAction::class
                    ]);

                $grid = Gridfield::create(
                    'Members',
                    'Team',
                    $this->Members()
                )->setConfig($gridConfig);

                $fields->addFieldToTab('Root.Members', $grid);

            }
        });

        return parent::getCMSFields();
    }


    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Team Member');
    }

}
