<?php
/**
 * Craft Status plugin for Craft CMS 3.x
 *
 * Get craft status for our dashboard.
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency Leroy
 */

namespace agencyleroy\craftstatus\controllers;

use agencyleroy\craftstatus\CraftStatus;
use agencyleroy\craftstatus\services\Versions;


use Craft;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Status Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Agency Leroy
 * @package   CraftStatus
 * @since     1.0.0
 */
class StatusController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * Disable CSRF validation for the entire controller
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Function that gets hit when a request is made to `/reporter/status`.
     *
     * @return array|false|string
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionIndex()
    {
        /**
         * Check if the request has the proper API keys, deny access if not.
         */
        $this->checkIfAuthenticated();

        /**
         * Get and return the response of Versions service.
         */
        $versions = new Versions();

        return $versions->all();
    }
}
