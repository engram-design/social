<?php

/**
 * Social Login for Craft
 *
 * @package   Social Login
 * @author    Benjamin David
 * @copyright Copyright (c) 2013, Dukt
 * @link      http://dukt.net/craft/social/
 * @license   http://dukt.net/craft/social/docs/license
 */

namespace Craft;

require_once(CRAFT_PLUGINS_PATH.'social/vendor/autoload.php');

use Guzzle\Http\Client;

class Social_PublicController extends BaseController
{
	public $allowAnonymous = true;

	public function actionLogout()
	{
        Craft::log(__METHOD__, LogLevel::Info, true);

		craft()->userSession->logout(false);

        $redirect = craft()->request->getParam('redirect');

        if(!$redirect) {
            if(isset($_SERVER['HTTP_REFERER'])) {
                $redirect = $_SERVER['HTTP_REFERER'];
            }

            $redirect = '';
        }

		$this->redirect($redirect);
	}

    public function actionLogin()
    {
        // request params
        $providerHandle = craft()->request->getParam('provider');
        $redirect = craft()->request->getParam('redirect');
        $errorRedirect = craft()->request->getParam('errorRedirect');

        // provider scopes & params
        $scopes = $this->getScopes($providerHandle);
        $params = $this->getParams($providerHandle);

        // redirect url
        $redirect = UrlHelper::getSiteUrl(
            craft()->config->get('actionTrigger').'/oauth/connect',
            array(
                'provider' => $providerHandle,
                'scopes' => base64_encode(serialize($scopes)),
                'params' => base64_encode(serialize($params))
            )
        );

        // redirect
        $this->redirect($redirect);
    }

    private function getScopes($handle)
    {
        switch($handle)
        {
            case 'google':

                return array(
                    'userinfo_profile',
                    'userinfo_email'
                );

                break;
        }

        return array();
    }

    private function getParams($handle)
    {
        switch($handle)
        {
            case 'google':

                return array(
                    'access_type' => 'offline'
                );

                break;
        }

        return array();
    }
}
