<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace anteo\mongodb\filemodels;

use yii\web\AssetBundle;

/**
 * FilemodelsAsset
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\gallery
 */
class FilemodelAsset extends AssetBundle
{
    public $sourcePath = '@anteo/mongodb/filemodels/assets';
    
    public $css = [
    ];
    
    public $js = [
        'filemodel.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'anteo\fontawesome\AssetBundle'
    ];
}