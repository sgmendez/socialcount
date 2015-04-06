<?php

/**
 * Copyright (c) 2015, Salvador Mendez
 * All rights reserved. 
 * 
 * This software is licensed by BSD 2-Clause License, you may obtain 
 * a copy of the license at the LICENSE file or at:
 * 
 * http://opensource.org/licenses/bsd-license.php
 * 
 * @author Salvador Mendez <salva@sgmendez.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD 2-Clause License
 * @copyright (c) 2015, Salvador Mendez
 * @package sgmendez/socialcount
 * @version 1.0
 * 
 */

namespace Sgmendez\SocialCount;

require __DIR__ . '/../vendor/autoload.php';

use RuntimeException;
use Sgmendez\Json\Json;
use InvalidArgumentException;

class SocialCount
{
    /**
     * Seconds CURLOPT_TIMEOUT
     */
    const CURL_TIMEOUT = 10;
    
    const EXCEPTION_NOTVALIDURL = 1;
    const EXCEPTION_CURL = 2;
    const EXCEPTION_JSON = 3;
    
    /**
     * Get count in Facebook, default value total_count
     * 
     * @param string $url
     * @param string $content Posible values:   like, total, share, click, comment
     * @return int
     */
    public function getCountFacebook($url)
    {        
        $fbUrl = 'https://api.facebook.com/method/links.getStats?urls='.$this->checkUrl($url, 'encode').'&format=json';
        $fbData = $this->decodeJson($this->getCurlGetContents($fbUrl));
        
        return (!empty($fbData[0]['total_count']) ? $fbData[0]['total_count'] : 0);
    }
    
    /**
     * Get count in Twitter
     * 
     * @param string $url
     * @return type
     */
    public function getCountTwitter($url)
    {
        $twUrl = 'http://urls.api.twitter.com/1/urls/count.json?url='.$this->checkUrl($url, 'encode');
        $twData = $this->decodeJson($this->getCurlGetContents($twUrl));
        
        return (!empty($twData['count'])) ? intval($twData['count']) : 0;
    }
    
    /**
     * Get count in Linkedin
     * 
     * @param string $url
     * @return int
     */
    public function getCountLinkedin($url)
    {
        $liUrl = 'http://www.linkedin.com/countserv/count/share?url='.$this->checkUrl($url, 'encode').'&format=json';
        $liData = $this->decodeJson($this->getCurlGetContents($liUrl));
        
        return (!empty($liData['count'])) ? intval($liData['count']) : 0;
    }
    
    /**
     * Get count in Google 
     * 
     * @param string $url
     * @return int
     */
    public function getCountGoogle($url)
    {
        $urlGo = 'https://clients6.google.com/rpc';
        $postFields = '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.$this->checkUrl($url, 'decode').'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]';
        $headers = array('Content-type: application/json');
        
        $goData = $this->decodeJson($this->getCurlPostContents($urlGo, $postFields, $headers));
        
        return (!empty($goData[0]['result']['metadata']['globalCounts']['count'])) ? intval($goData[0]['result']['metadata']['globalCounts']['count']) : 0;
    }
    
    /**
     * Get count in Reddit
     * 
     * @param string $url
     * @return int
     */
    public function getCountReddit($url)
    {
        $reUrl = 'http://buttons.reddit.com/button_info.json?url='.$this->checkUrl($url, 'encode');
        $reData = $this->decodeJson($this->getCurlGetContents($reUrl));
        
        return (!empty($reData['data']['children'][0]['data']['ups'])) ? intval($reData['data']['children'][0]['data']['ups']) : 0;
    }
    
    /**
     * Get count in StumbleUpon
     * 
     * @param string $url
     * @return int
     */
    public function getCountStumbleUpon($url)
    {
        $stUrl = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url='.$this->checkUrl($url, 'encode');
        $stData = $this->decodeJson($this->getCurlGetContents($stUrl));
        
        return (!empty($stData['result']['views'])) ? intval($stData['result']['views']) : 0;
    }
    
    /**
     * Get count in Pinterest
     * 
     * @param string $url
     * @return int
     */
    public function getCountPinterest($url)
    {
        $piUrl = 'http://api.pinterest.com/v1/urls/count.json?callback=count&url='.$this->checkUrl($url, 'encode');
        $piData = $this->decodeJson($this->getCurlGetContents($piUrl));
        
        return (!empty($piData['count'])) ? intval($piData['count']) : 0;
    }
    
    /**
     * Decode JSON data into array
     * 
     * @param string $jsonData
     * @return array
     * @throws RuntimeException
     */
    protected function decodeJson($jsonData)
    {
        if(!class_exists('Sgmendez\Json\Json'))
        {
            throw new RuntimeException('Can not initialize Sgmendez\Json\Json class', self::EXCEPTION_JSON);
        }
        
        $json = new Json();
        
        return $json->decode($this->convertJsonp($jsonData));
    }
    
    /**
     * Get cURL contents from $url by POST method
     * 
     * @param string $url
     * @param string $postFields
     * @param array $headers
     * @return type
     * @throws RuntimeException
     */
    protected function getCurlPostContents($url, $postFields, $headers)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_MAXREDIRS, 2);//only 2 redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                
        $result = curl_exec($ch);

        if(curl_errno($ch))
        {
            throw new RuntimeException('ERROR CURL [No '.curl_errno($ch).']: '.curl_error($ch).' | URL: '.$url, self::EXCEPTION_CURL);
        }
        
        return $result;
    }
    
    /**
     * Get cURL contents from $url by GET method
     * 
     * @param string $url
     * @return type
     * @throws RuntimeException
     */
    protected function getCurlGetContents($url)
    {        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_MAXREDIRS, 2);//only 2 redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        
        $result = curl_exec($ch);

        if(curl_errno($ch))
        {
            throw new RuntimeException('ERROR CURL [No '.curl_errno($ch).']: '.curl_error($ch).' | URL: '.$url, self::EXCEPTION_CURL);
        }
        
        return $result;
    }
    
    /**
     * Get user agent for cURL
     * 
     * @return string
     */
    private function getUserAgent()
    {
        $userAgents = array(
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
            "Opera/9.20 (Windows NT 6.0; U; en)",
            "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
            "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48",
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36"
        );
        
        $random = array_rand($userAgents);

        return $userAgents[$random];
    }
    
    /**
     * Check if $url is valid and set encode or decode
     * 
     * @param string $url
     * @param string $type encode|decode|false
     * @return string
     * @throws InvalidArgumentException
     */
    protected function checkUrl($url, $type = false)
    {
        $urlValid = filter_var($url, FILTER_VALIDATE_URL);
        if(false === $urlValid)
        {
            throw new InvalidArgumentException('Not valid url: '.$url, self::EXCEPTION_NOTVALIDURL);
        }
        
        switch ($type)
        {
            case 'decode':
                $urlPack = rawurldecode($urlValid);
                break;
            case 'encode':
                $urlPack = rawurlencode($urlValid);
                break;
            default :
                $urlPack = $urlValid;
        }
        
        return $urlPack;
    }
    
    /**
     * When $jsonData is jsonp convert to json for decode
     * 
     * @param stirng $jsonData
     * @return string
     */
    private function convertJsonp($jsonData)
    {
        $jsonp = $jsonData;
        if ($jsonData[0] !== '[' && $jsonData[0] !== '{')
        {
            $jsonp = trim(substr($jsonp, strpos($jsonp, '(')), '();');
        }
        
        return $jsonp;
    }
}

