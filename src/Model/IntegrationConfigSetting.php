<?php

namespace Dynamic\IntegrationConfig\Model;

use Dynamic\IntegrationConfig\Admin\IntegrationConfigAdmin;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\View\TemplateGlobalProvider;
use UncleCheese\DisplayLogic\Forms\Wrapper;

class IntegrationConfigSetting extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     */
    private static $singular_name = 'Integration Setting';

    /**
     * @var string
     */
    private static $plural_name = 'Integration Settings';
    /**
     *
     * @var string
     */
    private static $description = 'Add 3rd party integrations to your site';

    /**
     * @var string
     */
    private static $table_name = 'IntegrationConfigSetting';

    /**
     * @var array
     */
    private static $db = [
        'UseHubSpot' => 'Boolean',
        'HubSpotAccountID' => 'Varchar(16)',
    ];

    /**
     * Default permission to check for 'LoggedInUsers' to create or edit pages.
     *
     * @var array
     * @config
     */
    private static $required_permission = ['CMS_ACCESS_CMSMain', 'INTEGRATION_CONFIG_PERMISSION'];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->fieldByName('Root')->fieldByName('Main')
            ->setTitle('Analytics');

        $hubspot = $fields->dataFieldByName('HubSpotAccountID');

        $fields->removeByName([
            'HubSpotAccountID',
        ]);

        $fields->addFieldsToTab('Root.CRM', [
            CheckboxField::create('UseHubSpot', 'Enable HubSpot', $this->owner->UseHubSpot),
            Wrapper::create(
                $hubspot
                    ->setTitle('HubSpot Tracking-ID')
                    ->setDescription('~7 digits')
            )->displayIf('UseHubSpot')->isChecked()->end()
        ]);

        return $fields;
    }

    /**
     * Get the actions that are sent to the CMS. In
     * your extensions: updateEditFormActions($actions).
     *
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('INTEGRATION_CONFIG_PERMISSION')) {
            $actions = new FieldList(
                FormAction::create('save_integrationconfig', _t('IntegrationConfig.SAVE', 'Save'))
                    ->addExtraClass('btn-primary font-icon-save')
            );
        } else {
            $actions = FieldList::create();
        }
        $this->extend('updateCMSActions', $actions);

        return $actions;
    }

    /**
     * @throws ValidationException
     * @throws null
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $config = self::current_integration_config();
        if (!$config) {
            self::make_integration_config();
            DB::alteration_message('Added default integration config', 'created');
        }
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return IntegrationConfigAdmin::singleton()->Link();
    }

    /**
     * @param null $member
     *
     * @return bool|int|null
     */
    public function canEdit($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        $extended = $this->extendedCan('canEdit', $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::checkMember($member, 'INTEGRATION_CONFIG_PERMISSION');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'INTEGRATION_CONFIG_PERMISSION' => [
                'name' => _t(
                    'Dynamic\\IntegrationConfig\\Model\\IntegrationConfig.INTEGRATION_CONFIG_PERMISSION',
                    "Access to '{title}' section",
                    ['title' => IntegrationConfigAdmin::menu_title()]
                ),
                'category' => _t(
                    'SilverStripe\\Security\\Permission.CMS_ACCESS_CATEGORY',
                    'CMS Access'
                ),
                'help' => _t(
                    'Dynamic\\IntegrationConfig\\Model\\IntegrationConfig.INTEGRATION_CONFIG_PERMISSION_HELP',
                    'Ability to edit site integrations.'
                ),
                'sort' => 400,
            ],
        ];
    }

    /**
     * Get the current sites {@link GlobalSiteSetting}, and creates a new one
     * through {@link make_integration_config()} if none is found.
     *
     * @return GlobalSiteSetting|DataObject
     * @throws ValidationException
     */
    public static function current_integration_config()
    {
        if ($config = self::get()->first()) {
            return $config;
        }

        return self::make_integration_config();
    }

    /**
     * Create {@link GlobalSiteSetting} with defaults from language file.
     *
     * @return static
     * @throws ValidationException
     */
    public static function make_integration_config()
    {
        $config = self::create();
        $config->write();

        return $config;
    }

    /**
     * Add $IntegrationConfig to all SSViewers.
     */
    public static function get_template_global_variables()
    {
        return [
            'IntegrationConfig' => 'current_integration_config',
        ];
    }
}