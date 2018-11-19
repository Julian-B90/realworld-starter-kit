<?php


namespace app\modules\api\controllers;

use app\modules\api\components\Serializer;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\rest\ActiveController;
use yii\rest\Controller;

class CommonController extends Controller
{
    const AUTHENTICATOR_BEHAVIOR = 'authenticator';

    /**
     * @var string|null $root - root tag for response
     */
    public $root = null;

    public $displayCount = false;
    /**
     * @var bool $pluralize - if pluralize needed for index action of controller
     */
    public $pluralize = true;

    public $serializer = Serializer::class;

    /**
     * Adding to action result root tag if is set for controller
     * @param $action
     * @param $result
     *
     * @return array|mixed
     */
    public function afterAction($action, $result)
    {

    /**
     * @var bool $pluralize - if pluralize needed for index action of controller
     */
        // If result is model and has Errors we return error response
        if ($result instanceof Model && $result->hasErrors()) {
            return [
                'errors' => $this->serializeData($result)
            ];
        }
        $serializedData = $this->serializeData($result);

        if (!is_null($serializedData) && $this->root) {
            $serializedData = $this->addRoot($serializedData);
        }
        return $serializedData;
    }

    public function addRoot($data) {
        if (in_array($this->action->id, $this->pluralizeRootActions()) && $this->pluralize) {

            $result = [];
            $result[Inflector::pluralize($this->root)] = $data;

            if ($this->displayCount) {
                $result[Inflector::pluralize($this->root) . 'Count'] = count($data);
            }
            return $result;
        }
        return [
            $this->root => $data
        ];
    }

    /**
     * Returns array of actions, which root tags must be pluralized
     * @return array
     */
    protected function pluralizeRootActions() {
        return [
            'index'
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'schema' => 'Token',
        ];
        return $behaviors;
    }
}