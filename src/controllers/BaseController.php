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

use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Base Controller
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
class BaseController extends Controller
{

    /**
     * Checks if the request should be fulfilled or not.
     *
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     */
    protected function checkIfAuthenticated()
    {
        if (!Craft::$app->request->isPost) {
            $message = 'Only POST requests are supported.';

            throw new BadRequestHttpException($message);
        }

        $key = Craft::$app->request->getParam('key');
        $apiKey = Craft::parseEnv(CraftStatus::$plugin->getSettings()->apiKey);

        if (!$key) {
            $message = 'Missing parameter: `key`.';

            throw new BadRequestHttpException($message);
        }

        if ($key !== $apiKey) {
            $message = 'Unauthenticated access is not allowed.';

            throw new UnauthorizedHttpException($message);
        }
    }
}
