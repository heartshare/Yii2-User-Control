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

use yii\db\ActiveRecord;

use lnch\users\traits\ModuleTrait;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $public_email
 * @property string  $gravatar_email
 * @property string  $gravatar_id
 * @property string  $location
 * @property string  $website
 * @property string  $bio
 * @property string  $timezone
 * @property User    $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class Profile extends ActiveRecord
{
    use ModuleTrait;

    /** @var \dektrium\user\Module */
    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = \Yii::$app->getModule('user');
    }

    /**
     * Returns avatar url or null if avatar is not set.
     * @param  int $size
     * @return string|null
     */
    public function getAvatarUrl($size = 200)
    {
        return '//gravatar.com/avatar/' . $this->gravatar_id . '?s=' . $size;
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lnch_user_profiles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'nameLength'            => [['title', 'first_name', 'middle_names', 'surname'], 'string', 'max' => 255],

            'dateOfBirthDefault'    => ['date_of_birth', 'default', 'value' => NULL],
            // 'dateOfBirth'           => ['date_of_birth', 'date', 'format' => 'MM/dd/yyyy'],

            'websiteLength'         => ['website', 'string', 'max' => 255],
            'websiteUrl'            => ['website', 'url'],

            'locationLength'        => ['location', 'string', 'max' => 255],

            'timeZoneValidation'    => ['timezone', 'validateTimeZone'],
            
            'jobTitleLength'        => ['job_title', 'string', 'max' => 255],

            'contactNumberString'   => ['contact_number', 'string', 'max' => 56],

            'languageSafe'          => ['language', 'safe'],

            'bioString'             => ['bio', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name'     => \Yii::t('user', 'First Name'),
            // 'public_email'   => \Yii::t('user', 'Email (public)'),
            // 'gravatar_email' => \Yii::t('user', 'Gravatar email'),
            'location'       => \Yii::t('user', 'Location'),
            'website'        => \Yii::t('user', 'Website'),
            'bio'            => \Yii::t('user', 'Bio'),
            'timezone'       => \Yii::t('user', 'Time zone'),
        ];
    }
    
    /**
     * Validates the timezone attribute.
     * Adds an error when the specified time zone doesn't exist.
     * @param string $attribute the attribute being validated
     * @param array $params values for the placeholders in the error message
     */
    public function validateTimeZone($attribute, $params)
    {
        if(!in_array($this->$attribute, timezone_identifiers_list())) 
        {
            $this->addError($attribute, \Yii::t('user', 'Time zone is not valid'));
        }
    }
    
    /**
     * Get the user's time zone.
     * Defaults to the application timezone if not specified by the user.
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        try 
        {
            return new \DateTimeZone($this->timezone);
        } 
        catch(\Exception $e) 
        {
            // Default to application time zone if the user hasn't set their time zone
            return new \DateTimeZone(\Yii::$app->timeZone);
        }
    }
    
    /**
     * Set the user's time zone.
     * @param \DateTimeZone $timezone the timezone to save to the user's profile
     */
    public function setTimeZone(\DateTimeZone $timeZone)
    {
        $this->setAttribute('timezone', $timeZone->getName());
    }
    
    /**
     * Converts DateTime to user's local time
     * @param \DateTime the datetime to convert
     * @return \DateTime
     */
    public function toLocalTime(\DateTime $dateTime = null)
    {
        if($dateTime === null) 
        {
            $dateTime = new \DateTime();
        }
        
        return $dateTime->setTimezone($this->getTimeZone());
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        // if($this->isAttributeChanged('gravatar_email')) 
        // {
        //     $this->setAttribute('gravatar_id', md5(strtolower(trim($this->getAttribute('gravatar_email')))));
        // }

        return parent::beforeSave($insert);
    }
}