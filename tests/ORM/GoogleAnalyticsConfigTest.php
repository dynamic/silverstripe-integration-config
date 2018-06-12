<?php

namespace Dynamic\IntegrationConfig\Tests;

use Dynamic\IntegrationConfig\Model\IntegrationConfigSetting;
use Dynamic\IntegrationConfig\ORM\GoogleAnalyticsDataExtension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class GoogleAnalyticsConfigTest extends SapphireTest
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

        IntegrationConfigSetting::add_extension(GoogleAnalyticsDataExtension::class);
    }

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = Injector::inst()->create(IntegrationConfigSetting::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNotNull($fields->dataFieldByName('GACode'));
    }
}
