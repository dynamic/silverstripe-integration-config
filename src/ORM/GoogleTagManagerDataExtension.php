<?php

namespace Dynamic\IntegrationConfig\ORM;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
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
        'GTMHeadCode' => 'HTMLText',
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'UseGTM',
            'GTMHeadCode',
        ]);

        $fields->addFieldToTab('Root.Main', CompositeField::create(
            CheckboxField::create('UseGTM', 'Enable Google Tag Manager', $this->owner->GTMCode),
            Wrapper::create(
                LiteralField::create('GTMDescription', '<p>It is strongly recomended to set up a google analytics tag in tag manager, 
                    instead of managing tags and analytics sepratly.</p>'),
                $gtmHeadCode = TextareaField::create('GTMHeadCode')
                    ->setTitle('Google Tag Manager Head Code')
                    ->setDescription('The code that should go in the &lt;head&gt; tag.'),
                $gtmBodyCode = TextareaField::create('GTMHBodyCode')
                    ->setTitle('Google Tag Manager Body Code')
                    ->setDescription('The code that goes after the opening &lt;body&gt; tag.')
            )->displayIf('UseGTM')->isChecked()->end()
        ));
    }
}
