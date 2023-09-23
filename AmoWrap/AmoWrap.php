<?php

class AmoWrap
{
    /**
     * @var string
     */
    private static string $tokenFile = __DIR__ . '/token.json';

    /**
     * @param string $code
     * @throws Exception
     */
    public static function getAccessTokenWithCode(string $code): void
    {
        $url = AMO_DOMAIN . '/oauth2/access_token/';
        $data = [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => REDIRECT_URI
        ];
        $response = self::request($url, $data);
        self::saveToken($response);
    }

    /**
     * @param string $entityType
     * @param string $entityId
     * @param AmoNote $note
     * @throws Exception
     */
    public static function addNote(string $entityType, string $entityId, AmoNote $note): void
    {
        $accessToken = self::getAccessTokenWithRefresh();
        $url = AMO_DOMAIN . "/api/v4/$entityType/$entityId/notes";

        $data = [
            $note->toArray()
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        self::request($url, $data, $headers);
    }

    /**
     * @param string $responsibleId
     * @return string
     * @throws Exception
     */
    public static function getResponsibleNameById(string $responsibleId): string
    {
        $accessToken = self::getAccessTokenWithRefresh();
        $url = AMO_DOMAIN . "/api/v4/users/$responsibleId";

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        $response = self::request($url, '', $headers, 'GET');
        return $response['name'] ?? '';
    }

    /**
     * @return string
     * @throws Exception
     */
    private static function getAccessTokenWithRefresh(): string
    {
        $isNeedNewToken = self::checkIfAccessTokenExpired();
        $tokens = json_decode(self::getTokenFromFile(), true);

        if ($isNeedNewToken) {
            $url = AMO_DOMAIN . '/oauth2/access_token/';
            $data = [
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'grant_type' => 'refresh_token',
                'redirect_uri' => REDIRECT_URI,
                'refresh_token' => $tokens['refresh_token']
            ];
            $response = self::request($url, $data);
            self::saveToken($response);

            $accessToken = json_decode($response, true)['access_token'] ?? die('Auth failed');
        } else {
            $accessToken = $tokens['access_token'] ?? throw new Exception('Auth failed');
        }

        return $accessToken;

    }

    /**
     * @return bool
     */
    private static function checkIfAccessTokenExpired(): bool
    {
        $tokens = json_decode(self::getTokenFromFile(), true);
        $tokenTimeExpired = filemtime(self::$tokenFile) + $tokens['expires_in'];
        $currentTime = (new DateTime())->getTimestamp();
        return $tokenTimeExpired < $currentTime;
    }

    private static function saveToken(string $token): void
    {
        file_put_contents(self::$tokenFile, json_encode($token));
    }

    private static function getTokenFromFile(): string
    {
        return file_get_contents(self::$tokenFile);
    }

    /**
     * @param $url
     * @param $data
     * @param string[] $headers
     * @param string $method
     * @return array
     * @throws Exception
     */
    private static function request($url, $data, $headers = ['Content-Type: application/json'], $method = 'POST'): array
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true) ?? throw new Exception('Request error ' . print_r($response, true));

    }
}