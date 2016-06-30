<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\models;

use Yii;

use yii\db\ActiveRecord;

use lnch\users\traits\ModuleTrait;
    
/**
 * Token Active Record model.
 *
 * @property integer $user_id
 * @property string  $code
 * @property integer $created_at
 * @property integer $type
 * @property string  $url
 * @property bool    $isExpired
 * @property User    $user
 *
 * @author Tom Lynch <tom@lnch.co.uk>
 */
class UserType extends ActiveRecord
{
    use ModuleTrait;

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%lnch_user_types}}';
    }
    
    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['type_id'];
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if($insert) 
        {
            
        }
        return parent::beforeSave($insert);
    }
}