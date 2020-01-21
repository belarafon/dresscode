<?php

namespace SALESmanago\Entity;


class Settings
{
    const ACTIVE     = 'active';
    const ENDPOINT   = 'endpoint';
    const CLIENT_ID  = 'clientId';
    const API_KEY    = 'apiKey';
    const API_SECRET = 'apiSecret';
    const OWNER      = 'owner';
    const EMAIL      = 'email';
    const SHA        = 'sha';
    const TOKEN      = 'token';

    /**
     * @var boolean
     */
    protected $active = false;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var string
     */
    protected $sha;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $tags;

    /**
     * @var array
     */
    protected $removeTags;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $domainToken;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var array
     */
    protected $consentDetails;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getRequestEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        if (preg_match("@^(?:https?:\/\/)([^/]+)@i", $this->endpoint, $matches)) {
            return $matches[1];
        }
    }

    /**
     * @param string $endpoint
     * @param boolean $ssl
     * @return $this
     */
    public function setEndpoint($endpoint, $ssl = true)
    {
        if (preg_match('/^https?:\/\//', $endpoint)) {
            if ($ssl === true && preg_match("@^(?:http:\/\/)([^/]+)@i", $endpoint, $matches)) {
                $this->endpoint = "https://" . $matches[1];
            } else {
                $this->endpoint = $endpoint;
            }
        } else {
            if ($ssl === true) {
                $this->endpoint = "https://" . $endpoint;
            } else {
                $this->endpoint = "http://" . $endpoint;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return $this
     */
    public function setDefaultApiKey()
    {
        $this->apiKey = md5(time() . get_class($this));
        return $this;
    }

    /**
     * @return string
     */
    protected function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param string $apiSecret
     * @return $this
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
        return $this;
    }

    /**
     * @param string $sha
     * @return $this
     */
    public function setSha($sha)
    {
        $this->sha = $sha;
        return $this;
    }

    public function generateSha()
    {
        $this->sha = sha1($this->getApiKey() . $this->getClientId() . $this->getApiSecret());
        return $this;
    }

    /**
     * @return string
     */
    public function getSha()
    {
        if (empty($this->sha)) {
            self::generateSha();
        }
        return $this->sha;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $tags = explode(',', $tags);
        foreach ($tags as $key => $tag) {
            $tags[$key] = strtoupper(trim($tag));
        }
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param string $removeTags
     * @return $this
     */
    public function setRemoveTags($removeTags)
    {
        $removeTags = explode(',', $removeTags);
        foreach ($removeTags as $key => $tag) {
            $removeTags[$key] = strtoupper(trim($tag));
        }
        $this->removeTags = $removeTags;
        return $this;
    }

    /**
     * @return array
     */
    public function getRemoveTags()
    {
        return $this->removeTags;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domainToken
     * @return $this
     */
    public function setDomainToken($domainToken)
    {
        $this->domainToken = $domainToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomainToken()
    {
        return $this->domainToken;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getConfig()
    {
        return [
            self::CLIENT_ID => $this->getClientId(),
            self::SHA       => $this->getSha(),
            self::API_KEY   => $this->getApiKey(),
            self::OWNER     => $this->getOwner(),
            self::ENDPOINT  => $this->getEndpoint(),
            self::TOKEN     => $this->getToken()
        ];
    }

    /**
     * @param  array $params
     * @return int
     */
    public function count($params)
    {
        return !is_null($params) && is_array($params) ? count($params) : 0;
    }

    public function getUserIP() {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ip = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = 'UNKNOWN';

        $ips = explode(",",$ip);
        if(is_array($ips)) {
            return trim($ips[0]);
        }

        return $ip;
    }
}
