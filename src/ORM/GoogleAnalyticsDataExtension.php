<?php

namespace Dynamic\IntegrationConfig\ORM;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class GoogleAnalyticsDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'UseGA' => 'Boolean',
        'GACode' => 'Varchar(16)',
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'UseGA',
            'GACode',
        ]);

        $fields->addFieldToTab('Root.Main', CompositeField::create(
            CheckboxField::create('UseGA', 'Enable Google Analytics', $this->owner->GACode),
            Wrapper::create(
                /*
                LiteralField::create(
                    'AnalyticsDescrip',
                    '<p>Enter your Google Analytics Profile ID below to enable site tracking</p>'
                ),
                */
                $gaCode = TextField::create('GACode')
                    ->setTitle('Google Analytics Profile ID')
                    ->setDescription('in the format <strong>UA-XXXXX-X</strong>')
            )->displayIf('UseGA')->isChecked()->end()
        ));
    }
}
