<?php

namespace App\Services;

use App\Models\Getway;
use Stripe\Stripe;
use Stripe\PaymentMethodDomain;

class StripeDomainSyncService
{
    public function run($tenantId)
    {

        $domainRow = tenancy()->central(function () use ($tenantId) {
            return \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $tenantId)
                ->latest('id')
                ->first();
        });

        if (!$domainRow?->domain) {
            return; 
        }

        $domainName = strtolower(trim($domainRow->domain));
        $domainName = preg_replace('#^https?://#', '', $domainName);
        $domainName = preg_replace('#/.*$#', '', $domainName);
        $domainName = rtrim($domainName, '/');

        // Stripe key
        $gateway = Getway::where('status','!=',0)
            ->where('namespace','=','App\Lib\Stripe')
            ->first();

        $gwData = json_decode($gateway->data ?? '{}', true);

        $secretKey = ($gateway?->test_mode == 1)
            ? ($gwData['test_secret_key'] ?? null)
            : ($gwData['secret_key'] ?? null);

        if (!$secretKey) return;

        Stripe::setApiKey($secretKey);

        $existing = PaymentMethodDomain::all(['limit' => 1000]);

        foreach ($existing->data as $row) {
            if (strtolower($row->domain_name) === $domainName) {
                return; 
            }
        }

        PaymentMethodDomain::create([
            'domain_name' => $domainName
        ]);
    }
}
