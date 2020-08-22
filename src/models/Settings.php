<?php
/**
 * Craft Status plugin for Craft CMS 3.x
 *
 * Get craft status for our dashboard.
 *
 * @link      https://agencyleroy.com
 * @copyright Copyright (c) 2020 Agency Leroy
 */

namespace agencyleroy\craftstatus\models;

use agencyleroy\craftstatus\CraftStatus;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

/**
 * Settings Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Agency Leroy
 * @package   CraftStatus
 * @since     1.0.0
 */
class Settings extends Model
{
    public $apiKey = '';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'apiKey'
            ],
        ];

        return $behaviors;
    }
}
