<?php

namespace App\Services;

use Aws\SesV2\SesV2Client;
use Aws\Credentials\Credentials;

class SesService
{
    protected $client;

    public function __construct()
    {
   
        $key    = get_secret('AWS_ACCESS_KEY_ID');
      
        $secret = get_secret('AWS_SECRET_ACCESS_KEY');


        if (empty($key) || empty($secret)) {
            throw new \Exception('AWS credentials not found in database.');
        }

        $this->client = new SesV2Client([
            'version' => 'latest',
            'region'  => config('services.ses.region', 'us-east-1'),
            'credentials' => new Credentials($key, $secret),
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }
}