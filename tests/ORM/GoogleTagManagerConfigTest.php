<?php

namespace Dynamic\IntegrationConfig\Tests;

use Dynamic\IntegrationConfig\Model\IntegrationConfigSetting;
use Dynamic\IntegrationConfig\ORM\GoogleTagManagerDataExtension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class GoogleTagManagerConfigTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        IntegrationConfigSetting::add_extension(GoogleTagManagerDataExtension::class);
    }

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = Injector::inst()->create(IntegrationConfigSetting::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNotNull($fields->dataFieldByName('GTMHeadCode'));
        $this->assertNotNull($fields->dataFieldByName('GTMBodyCode'));
    }
}
