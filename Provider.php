<?php

namespace Kuainiu\Lark;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'LARK';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.zjurl.cn/connect/qrconnect/page/sso/', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://open.zjurl.cn/connect/qrconnect/oauth2/access_token/';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://open.zjurl.cn/connect/qrconnect/oauth2/user_info/', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['EmployeeID'],
            'name'     => $user['Name'],
            'email'    => $user['Email'],
            'avatar'   => $user['AvatarUrl'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        return [
            'state' => $state,
            'response_type' => 'code',
            'app_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($this->getTokenFields($code)),
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'app_id' => $this->clientId,
            'app_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
    }
}
