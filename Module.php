<?php

namespace anteo\mongodb\filemodels;

use anteo\mongodb\filemodels\helpers\ClassMapHelper;
use anteo\mongodb\filemodels\models\FilemodelQuery;
use anteo\mongodb\filemodels\models\FilemodelSearch;
use Yii;
use yii\base\Exception;
use yii\web\Application as WebApplication;

/**
 * Module for ActiveRecord file attachments.
 */
class Module extends \yii\base\Module
{
    public $classMap = [];
    
//    public $routes = [
//        '<action>' => 'default/<action>',
//    ];
    
//    public $prefix = 'filemodels';
    
    public function init()
    {
        $map = $this->buildClassMap($this->classMap);
        $this->initContainer($map);
        if (Yii::$app instanceof WebApplication) {
//            $this->initControllerNamespace();
//            $this->initUrlRoutes();
        } else {
            $this->initConsoleCommands();
        }

        parent::init();
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t("anteo/mongodb/$category", $message, $params, $language);
    }
    
    /**
     * Initialize container with module classes.
     *
     * @param array                 $map the previously built class map list
     */
    protected function initContainer($map)
    {
        $di = Yii::$container;
        try {
            // events
//            $di->set(Event\FormEvent::class);
//            $di->set(Event\ProfileEvent::class);
//            $di->set(Event\ResetPasswordEvent::class);
//            $di->set(Event\SocialNetworkAuthEvent::class);
//            $di->set(Event\SocialNetworkConnectEvent::class);
//            $di->set(Event\UserEvent::class);
            // forms
//            $di->set(Form\LoginForm::class);
//            $di->set(Form\RecoveryForm::class);
//            $di->set(Form\RegistrationForm::class);
//            $di->set(Form\ResendForm::class);
//            $di->set(Form\SettingsForm::class);
            // helpers
//            $di->set(Helper\AuthHelper::class);
//            $di->set(Helper\GravatarHelper::class);
//            $di->set(Helper\SecurityHelper::class);
//            $di->set(Helper\TimezoneHelper::class);
            // services
//            $di->set(Service\AccountConfirmationService::class);
//            $di->set(Service\EmailChangeService::class);
//            $di->set(Service\PasswordRecoveryService::class);
//            $di->set(Service\ResendConfirmationService::class);
//            $di->set(Service\ResetPasswordService::class);
//            $di->set(Service\SocialNetworkAccountConnectService::class);
//            $di->set(Service\SocialNetworkAuthenticateService::class);
//            $di->set(Service\UserBlockService::class);
//            $di->set(Service\UserCreateService::class);
//            $di->set(Service\UserRegisterService::class);
//            $di->set(Service\UserConfirmationService::class);
//            $di->set(Service\AuthItemEditionService::class);
//            $di->set(Service\UpdateAuthAssignmentsService::class);
//            $di->set(Service\SwitchIdentityService::class);
            // email change strategy
//            $di->set(Strategy\DefaultEmailChangeStrategy::class);
//            $di->set(Strategy\InsecureEmailChangeStrategy::class);
//            $di->set(Strategy\SecureEmailChangeStrategy::class);
            // validators
//            $di->set(Validator\AjaxRequestModelValidator::class);
//            $di->set(TimeZoneValidator::class);
            // class map models + query classes
            $modelClassMap = [];
            foreach ($map as $class => $definition) {
                $di->set($class, $definition);
                $model = is_array($definition) ? $definition['class'] : $definition;
                $name = (substr($class, strrpos($class, '\\') + 1));
                $modelClassMap[$class] = $model;
                if (in_array($name, ['Filemodel'])) {
                    $di->set(
                        "anteo\\mongodb\\filemodels\\models\\{$name}Query",
                        function () use ($model) {
                            return $model::find();
                        }
                    );
                }
            }
            $di->setSingleton(ClassMapHelper::class, ClassMapHelper::class, [$modelClassMap]);
            // search classes
            if (!$di->has(FilemodelSearch::class)) {
                $di->set(FilemodelSearch::class, [$di->get(FilemodelQuery::class)]);
            }
        } catch (Exception $e) {
            die($e);
        }
    }
       
    /**
     * Registers console commands to main app.
     */
    protected function initConsoleCommands()
    {
        $this->controllerNamespace = 'anteo\mongodb\filemodels\commands';
    }

    /**
     * Registers controllers.
     */
//    protected function initControllerNamespace()
//    {
//        $this->controllerNamespace = 'anteo\mongodb\filemodels\controllers';
//    }
    /**
     * Builds class map according to user configuration.
     *
     * @param array $userClassMap user configuration on the module
     *
     * @return array
     */
    protected function buildClassMap(array $userClassMap)
    {
        $map = [];
        $defaults = [
            // --- models
            'Filemodel' => 'anteo\mongodb\filemodels\models\Filemodel',
            'Metadata' => 'anteo\mongodb\filemodels\models\embedded\Metadata',
            // --- search
            'FilemodelSearch' => 'anteo\mongodb\filemodels\models\FilemodelSearch',
        ];
        $routes = [
            'anteo\mongodb\filemodels\models' => [
                'Filemodel',
                'FilemodelSearch',
            ],
            'anteo\mongodb\filemodels\models\embedded' => [
                'Metadata',
            ],
        ];
        $mapping = array_merge($defaults, $userClassMap);
        foreach ($mapping as $name => $definition) {
            $map[$this->getRoute($routes, $name) . "\\$name"] = $definition;
        }
        return $map;
    }
    /**
     * Returns the parent class name route of a short class name.
     *
     * @param array  $routes class name routes
     * @param string $name
     *
     * @throws Exception
     * @return int|string
     *
     */
    protected function getRoute(array $routes, $name)
    {
        foreach ($routes as $route => $names) {
            if (in_array($name, $names)) {
                return $route;
            }
        }
        throw new Exception("Unknown configuration class name '{$name}'");
    }
    
    /**
     * Initializes web url routes (rules in Yii2).
     */
//    protected function initUrlRoutes()
//    {
//        $app = Yii::$app;
//        /** @var $module Module */
//        $config = [
//            'class' => 'yii\web\GroupUrlRule',
//            'prefix' => $this->prefix,
//            'rules' => $this->routes,
//        ];
//        if ($this->prefix !== 'filemodels') {
//            $config['routePrefix'] = 'filemodels';
//        }
//        $rule = Yii::createObject($config);
//        $app->getUrlManager()->addRules([$rule], false);
//    }
}
