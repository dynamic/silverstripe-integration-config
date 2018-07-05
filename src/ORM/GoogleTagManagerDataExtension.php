<?php

namespace Dynamic\IntegrationConfig\ORM;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataExtension;
use UncleCheese\DisplayLogic\Forms\Wrapper;

/**
 * Class GoogleTagManagerDataExtension
 * @package Dynamic\IntegrationConfig\ORM
 */
class GoogleTagManagerDataExtension extends DataExtension
{
    private static $db = array(
        'UseGTM' => 'Boolean',
        'GTMCode' => 'HTMLText',
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'UseGTM',
            'GTMCode',
        ]);

        $fields->addFieldToTab('Root.Main', CompositeField::create(
            CheckboxField::create('UseGTM', 'Enable Google Tag Manager', $this->owner->GTMCode),
            Wrapper::create(
            /*
            LiteralField::create(
                'AnalyticsDescrip',
                '<p>Enter your Google Analytics Profile ID below to enable site tracking</p>'
            ),
            */
                $gaCode = TextareaField::create('GTMCode')
                    ->setTitle('Google Tag Manager Code')
                    ->setDescription('It is strongly recomended to set up a google analytics tag in tag manager, instead of managing tags and analytics sepratly.')
            )->displayIf('UseGTM')->isChecked()->end()
        ));
    }
}
