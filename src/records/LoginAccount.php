<?php
/**
 * @link      https://dukt.net/social/
 * @copyright Copyright (c) 2018, Dukt
 * @license   https://dukt.net/social/docs/license
 */

namespace dukt\social\records;

use craft\db\ActiveRecord;

/**
 * Class LoginAccount record.
 *
 * @property int       $id              ID
 * @property int       $userId          User ID
 * @property string    $providerHandle  Provider handle
 * @property string    $socialUid       Social UID
 *
 * @author Dukt <support@dukt.net>
 * @since   1.0
 */
class LoginAccount extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the associated database table.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'social_login_accounts';
    }

    /**
     * Defines this model's relations to other models.
     *
     * @return array
     */
    public function defineRelations()
    {
        return [
            'element' => [static::BELONGS_TO, 'ElementRecord', 'id', 'onDelete' => static::CASCADE, 'required' => true],
            'user' => [static::BELONGS_TO, 'UserRecord', 'onDelete' => static::CASCADE, 'required' => true],
        ];
    }

    /**
     * Defines this model's database table indexes.
     *
     * @return array
     */
    public function defineIndexes()
    {
        return [
            ['columns' => ['providerHandle', 'socialUid'], 'unique' => true]
        ];
    }

    // Protected Methods
    // =========================================================================

    /**
     * Defines this model's attributes.
     *
     * @return array
     */
    public function defineAttributes()
    {
        return [
            'providerHandle' => [AttributeType::String, 'required' => true],
            'socialUid' => [AttributeType::String, 'required' => true],
        ];
    }
}
