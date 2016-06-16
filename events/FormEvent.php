<?php
/*
 * This file is part of a LNCH Yii2 Extension 
 *
 * (c) LNCH UK Group <http://www.lnch.co.uk/licencing>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\events;

use yii\base\Event;
use yii\base\Model;

/**
 * @property Form $model
 * @author Tom Lynch <tom@lnch.co.uk>
 */
class FormEvent extends Event
{
    /**
     * @var Model
     */
    private $_form;

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param Form $form
     */
    public function setForm(Model $form)
    {
        $this->_form = $form;
    }
}