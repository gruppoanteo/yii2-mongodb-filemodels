<?php
namespace anteo\mongodb\filemodels;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;


class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $this->initTranslations($app);
    }
    
    /**
     * Registers module translation messages.
     *
     * @param Application $app
     */
    protected function initTranslations(Application $app)
    {
        if (!isset($app->get('i18n')->translations['anteo/mongodb/filemodels*'])) {
            $app->get('i18n')->translations['anteo/mongodb/filemodels*'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/messages',
                'fileMap' => [
                    'anteo/mongodb/filemodels' => 'filemodels.php',
                ],
                'sourceLanguage' => 'en',
            ];
        }
    }
}