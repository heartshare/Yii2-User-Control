<?php

namespace lnch\users\traits;

use lnch\users\UserControl;

/**
 * Trait ModuleTrait
 * @property-read UserControl $module
 * @package dektrium\user\traits
 */
trait ModuleTrait
{
    /**
     * @return UserControl
     */
    public function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}