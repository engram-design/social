<?php

/**
 * Social Login for Craft
 *
 * @package   Social Login
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @link      https://dukt.net/craft/social/
 * @license   https://dukt.net/craft/social/docs/license
 */

namespace Craft;

class SocialPlugin extends BasePlugin
{
    /**
     * Get Name
     */
    function getName()
    {
        return Craft::t('Social Login');
    }

    /**
     * Get Version
     */
    function getVersion()
    {
        return '0.9.19';
    }

    /**
     * Get Developer
     */
    function getDeveloper()
    {
        return 'Dukt';
    }

    /**
     * Get Developer URL
     */
    function getDeveloperUrl()
    {
        return 'https://dukt.net/';
    }

    /**
     * Define Settings
     */
    protected function defineSettings()
    {
        return array(
            'allowSocialRegistration' => array(AttributeType::Bool, 'default' => true),
            'allowSocialLogin' => array(AttributeType::Bool, 'default' => true),
            'defaultGroup' => array(AttributeType::Number, 'default' => null),
            'requireEmailAddress' => array(AttributeType::Bool, 'default' => true),
        );
    }

    /**
     * Get Settings HTML
     */
    public function getSettingsHtml()
    {
        if(craft()->request->getPath() == 'settings/plugins')
        {
            return true;
        }

        $variables = array(
            'settings' => $this->getSettings()
        );

        // $oauthPlugin = craft()->plugins->getPlugin('OAuth');

        // if($oauthPlugin)
        // {
        //     if($oauthPlugin->isInstalled && $oauthPlugin->isEnabled)
        //     {

        //     }
        // }

        return craft()->templates->render('social/settings', $variables);
    }

    /**
     * Has CP Section
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     * Hook Register CP Routes
     */
    public function registerCpRoutes()
    {
        return array(
            'social\/settings\/(?P<serviceProviderClass>.*)' => 'social/settings/_provider',
        );
    }

    /**
     * Hook Register OAuth Connect
     */
    public function registerOauthConnect($variables)
    {
        try {
            $plugin = craft()->plugins->getPlugin('social');
            $settings = $plugin->getSettings();

            if(!$settings['allowSocialLogin'])
            {
                throw new Exception("Social login disabled");
            }

            $provider = $variables['provider'];
            $token = $variables['token'];

            // current user
            $user = craft()->userSession->getUser();

            // logged in ?
            $isLoggedIn = false;

            if($user)
            {
                $isLoggedIn = true;
            }

            // retrieve social user from uid

            $provider->source->setToken($token);

            $account = $provider->source->getAccount();

            $socialUser = craft()->social->getUserByUid($provider->handle, $account['uid']);

            // error if uid is associated with a different user
            if($user && $socialUser && $user->id != $socialUser->userId)
            {
                throw new Exception("UID is already associated with another user. Disconnect from your current session and retry.");
            }

            // create user if it doesn't exists
            if(!$user)
            {
                if(!empty($account['email']))
                {
                    // find with email
                    $user = craft()->users->getUserByUsernameOrEmail($account['email']);

                    if(!$user)
                    {
                        $user = craft()->social->registerUser($account);
                    }
                }
                else
                {
                    $user = craft()->social->registerUser($account);
                }
            }


            // save social user

            if(!$socialUser)
            {
                $socialUser = new Social_UserModel();
            }

            $socialUser->userId = $user->id;
            $socialUser->provider = $provider->handle;
            $socialUser->suid = $account['uid'];
            $socialUser->token = craft()->oauth->encodeToken($token);

            craft()->social->saveUser($socialUser);


            // login if not logged in
            if(!$isLoggedIn)
            {
                craft()->social_userSession->login($socialUser->token);
            }
        }
        catch(\Exception $e)
        {
            craft()->httpSession->add('error', $e->getMessage());
        }
    }
}