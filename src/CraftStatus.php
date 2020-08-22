<?php
/**
 * Craft Status plugin for Craft CMS 3.x
 *
 * Get craft status for our dashboard.
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency Leroy
 */

namespace agencyleroy\craftstatus;

use agencyleroy\craftstatus\services\Versions as VersionsService;
use agencyleroy\craftstatus\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\ProjectConfig;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\log\FileTarget;
use craft\helpers\UrlHelper;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Agency Leroy
 * @package   CraftStatus
 * @since     1.0.0
 *
 * @property  VersionsService $versions
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftStatus extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftStatus::$plugin
     *
     * @var CraftStatus
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftStatus::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerLogger();
        $this->_redirectAfterInstall();
        $this->_registerEvents();

    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'craft-status/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }


    // Private Methods
    // =========================================================================

    /**
     * Redirect user to the plugin settings page after install.
     */
    private function _redirectAfterInstall()
    {
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this && !Craft::$app->getRequest()->isConsoleRequest ) {
                    Craft::$app->response->redirect(UrlHelper::cpUrl('settings/plugins/craft-status'))->send();
                }
            }
        );
    }

    /**
     * Register event listeners.
     */
    private function _registerEvents()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-status/status'] = 'craft-status/status/index';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_BEFORE_SAVE_PLUGIN_SETTINGS,
            function (PluginEvent $event) {
                $settings = Craft::$app->request->getParam('settings');

                if ($event->plugin === $this && isset($settings['regenerate'])) {
                    $user = Craft::$app->getUser()->getIdentity();
                    $newKey = $this->_generateApiKey();

                    Craft::$app->session->setNotice(Craft::t('craft-status', 'Generated a new API Key. Make sure to save your settings.'));
                    Craft::$app->session->setFlash('apiKey', $newKey);

                    return Craft::$app->response->redirect(Craft::$app->request->getUrl())->sendAndClose();
                }
            }
        );
    }

    private function _registerLogger()
    {
        // Create a new file target
        $fileTarget = new FileTarget([
            'logFile' => '@storage/logs/craftstatus.log',
            'categories' => ['agencyleroy\craftstatus\*']
        ]);

        // Add the new target file target to the dispatcher
        Craft::getLogger()->dispatcher->targets[] = $fileTarget;

    }

    /**
     * Generates a new API Key.
     *
     * @return string
     * @throws \yii\base\Exception
     */
    private function _generateApiKey(): string
    {
        return Craft::$app->security->generateRandomString(30);
    }
}
