<?php

namespace App\Services;

use App\Mail\PartnerCreateStoreErrorMail;
use App\Models\Domain;
use App\Models\Getway;
use App\Models\Option;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Tenantorder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PartnerCreateStoreService
{
    public const STEP_ORDER = 'create_order';

    public const STEP_TENANT = 'create_tenant';

    public const STEP_DOMAIN = 'create_domain';

    public function handle(Request $request)
    {
        $name = Str::slug((string) $request->input('store_name'));
        $clubId = (int) $request->input('club_id');
        $existingTenant = $this->findTenantByStoreOrClub($name, $clubId);

        $validator = $this->validateRequest($request, $existingTenant);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()], 422);
        }

        if ($existingTenant && $this->isStoreProvisioningComplete($existingTenant)) {
            $error = 'Store is already creeated';
            $redirectUrl = $existingTenant->id . '.' . env('APP_PROTOCOLESS_URL') . '/redirect/login?email=' . $request->email . '&&password=' . $request->email;

            return response()->json([
                'status' => 0,
                'message' => $error,
                'redirect_url' => $redirectUrl,
            ], 422);
        }

        if ($existingTenant) {
            $name = $existingTenant->id;
        }

        $domainName = $name . '.' . env('APP_PROTOCOLESS_URL');
        $domain = Domain::where('domain', $domainName)->first();
        if ($domain && (!$existingTenant || $domain->tenant_id !== $existingTenant->id)) {
            return response()->json(['status' => 0, 'message' => 'Store URL is unavailable'], 422);
        }

        $storeData = $this->buildStoreData($request, $name, $clubId, $existingTenant);
        Session::put('store_data', $storeData);
        $this->rememberProgress($clubId, [
            'store_name' => $name,
            'email' => $storeData['email'],
            'club_id' => $clubId,
            'store_data' => $storeData,
        ]);

        try {
            $planId = 1;
            $plan = Plan::where([['status', 1]])->findOrFail($planId);

            if ($plan->is_trial != 1) {
                return response()->json(['status' => 0, 'message' => 'error']);
            }

            $order = $this->resolveOrCreateTrialOrder($request, $existingTenant, $clubId, $plan);
            Session::put('order_id', $order->id);
            Session::put('plan', $plan->id);
            Session::put('domain_data', ['name' => $storeData['store_name']]);
            $this->rememberProgress($clubId, ['order_id' => $order->id, 'plan_id' => $plan->id]);

            $tenant = $this->resolveOrCreateTenant($request, $existingTenant, $order, $plan, $storeData, $name, $clubId);

            $this->ensureDomainAndOrderLog($tenant, $order, $name);

            return $this->buildSuccessResponse($tenant, $name, $storeData, $plan);
        } catch (\Throwable $e) {
            return $this->handleStepFailure($e, $request, $clubId, $name);
        }
    }

    protected function validateRequest(Request $request, ?Tenant $existingTenant)
    {
        $name = Str::slug((string) $request->input('store_name'));
        $clubId = (int) $request->input('club_id');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'store_name' => 'required|max:150|regex:/^\S*$/u',
            'club_id' => 'required|integer|min:1|max:200000',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        if ($existingTenant) {
            return $validator;
        }

        $conflict = Tenant::where(function ($query) use ($name, $clubId) {
            $query->where('id', '=', $name)
                ->orWhere('club_id', '=', $clubId);
        })->first();

        if ($conflict) {
            $validator->after(function ($validator) {
                $validator->errors()->add('store_name', 'The store name has already been taken.');
            });
        }

        return $validator;
    }

    protected function buildStoreData(Request $request, string $name, int $clubId, ?Tenant $existingTenant): array
    {
        $storeData = [
            'store_name' => $name,
            'email' => $request->input('email'),
            'password' => $request->input('email'),
            'club_id' => $clubId,
            'wpuid' => $request->club_info['wpuid'],
            'club_info' => json_encode($request->club_info),
        ];

        if ($request->filled('logo')) {
            $storeData['logo'] = $request->input('logo');
        } elseif ($existingTenant && !empty($existingTenant->logo)) {
            $storeData['logo'] = $existingTenant->logo;
        }

        return $storeData;
    }

    protected function resolveOrCreateTrialOrder(Request $request, ?Tenant $existingTenant, int $clubId, Plan $plan): Order
    {
        if ($existingTenant && !empty($existingTenant->order_id)) {
            return Order::findOrFail($existingTenant->order_id);
        }

        $progress = $this->getProgress($clubId);
        if (!empty($progress['order_id'])) {
            $order = Order::find($progress['order_id']);
            if ($order) {
                return $order;
            }
        }

        try {
            $tax = Option::where('key', 'tax')->first();
            $taxAmount = ($plan->price / 100) * $tax->value;

            $order = new Order();
            $order->plan_id = $plan->id;
            $order->user_id = 2;
            $order->getway_id = 13;
            $order->tax = $taxAmount;
            $order->price = $plan->price;
            $order->status = 1;
            $order->payment_status = 1;
            $order->will_expire = Carbon::now()->addDays($plan->duration);
            $order->save();

            return $order;
        } catch (\Throwable $e) {
            throw new PartnerCreateStoreStepException(self::STEP_ORDER, $e);
        }
    }

    protected function resolveOrCreateTenant(
        Request $request,
        ?Tenant $existingTenant,
        Order $order,
        Plan $plan,
        array $storeData,
        string $name,
        int $clubId
    ): Tenant {
        if ($existingTenant) {
            if (empty($existingTenant->order_id)) {
                $existingTenant->order_id = $order->id;
                $existingTenant->save();
            }

            return $existingTenant->fresh();
        }

        try {
            $expDays = $plan->duration;
            $expiryDate = Carbon::now()->addDays($expDays)->format('Y-m-d');
            $status = env('AUTO_TENANT_APPROVE') == true ? 1 : 2;
            $planInfo = json_decode($plan->data ?? '');

            if (env('AUTO_DB_CREATE') == true) {
                if ($order->status == 1) {
                    $tenant = new Tenant();
                    foreach ($planInfo ?? [] as $key => $value) {
                        $tenant->$key = $value;
                    }
                    $tenant->status = $status;
                } else {
                    $tenant = new \App\Tenant();
                    $tenant->status = 2;
                }
            } else {
                $tenant = new \App\Tenant();
                $tenant->status = 2;
            }

            $tenant->id = $name;
            $tenant->uid = \App\Tenant::count() + 1;
            $tenant->order_id = $order->id;
            $tenant->user_id = 2;
            $tenant->will_expire = $expiryDate;
            $tenant->club_id = $clubId;
            $tenant->logo = $storeData['logo'] ?? '';
            $tenant->club_info = $storeData['club_info'] ?? '';
            $tenant->save();

            return $tenant->fresh();
        } catch (\Throwable $e) {
            throw new PartnerCreateStoreStepException(self::STEP_TENANT, $e);
        }
    }

    protected function ensureDomainAndOrderLog(Tenant $tenant, Order $order, string $name): void
    {
        $domainName = $name . '.' . env('APP_PROTOCOLESS_URL');
        $hasDomain = Domain::where('tenant_id', $tenant->id)->where('domain', $domainName)->exists();
        $hasOrderLog = Tenantorder::where('tenant_id', $tenant->id)->where('order_id', $order->id)->exists();

        if ($hasDomain && $hasOrderLog) {
            return;
        }

        DB::beginTransaction();

        try {
            $tenantId = $name;
            $type = 2;
            $domainStatus = env('AUTO_SUBDOMAIN_APPROVE') == true ? 1 : 2;

            if (!$hasDomain) {
                if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
                    $tenant->domains()->create([
                        'domain' => $domainName,
                        'tenant_id' => $tenantId,
                        'type' => $type,
                        'status' => $domainStatus,
                    ]);
                } else {
                    $domain = new Domain();
                    $domain->domain = $domainName;
                    $domain->tenant_id = $tenantId;
                    $domain->type = $type;
                    $domain->status = $domainStatus;
                    $domain->save();
                }
            }

            if (!$hasOrderLog) {
                if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
                    $existingLog = $tenant->tenantorderlog()->where('order_id', $order->id)->first();
                    if (!$existingLog) {
                        $tenant->tenantorderlog()->create(['order_id' => $order->id]);
                    }
                } else {
                    $log = new Tenantorder();
                    $log->order_id = $order->id;
                    $log->tenant_id = $tenantId;
                    $log->save();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new PartnerCreateStoreStepException(self::STEP_DOMAIN, $e);
        }
    }

    protected function buildSuccessResponse(Tenant $tenant, string $name, array $storeData, Plan $plan)
    {
        Session::forget('domain_data');
        Session::forget('order_id');
        Session::forget('store_data');
        Session::forget('plan');
        $this->forgetProgress((int) $storeData['club_id']);

        if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
            if (env('AUTO_SUBDOMAIN_APPROVE') == true) {
                $redirectUrl = $name . '.' . env('APP_PROTOCOLESS_URL') . '/redirect/login?email=' . $storeData['email'] . '&&password=' . $storeData['password'];
            } else {
                $redirectUrl = env('APP_URL_WITH_TENANT') . $name . '/redirect/login?email=' . $storeData['email'] . '&&password=' . $storeData['password'];
            }
        } else {
            $redirectUrl = route('merchant.domain.list');
        }

        if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
            return response()->json([
                'status' => 1,
                'message' => '',
                'result' => [
                    'redirect_url' => $redirectUrl,
                    'store_status' => $tenant->status,
                    'response' => 'success',
                    'store_id' => $name,
                ],
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => '',
            'result' => [
                'redirect_url' => $redirectUrl,
                'store_status' => $tenant->status,
                'response' => 'success_redirect',
                'store_id' => $name,
            ],
        ]);
    }

    protected function handleStepFailure(\Throwable $e, Request $request, int $clubId, string $name)
    {
        $step = $e instanceof PartnerCreateStoreStepException
            ? $e->getStep()
            : self::STEP_TENANT;

        $copy = $this->stepFailureCopy($step);
        $technical = trim($e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage());
        $message = $copy['message'];
        if ($technical !== '') {
            $message .= ' (' . $technical . ')';
        }

        Log::error('Partner createstore failed', [
            'club_id' => $clubId,
            'store_name' => $name,
            'step' => $step,
            'error' => $technical,
        ]);

        $this->rememberProgress($clubId, [
            'last_failed_step' => $step,
            'store_name' => $name,
            'email' => $request->input('email'),
        ]);

        $this->sendFailureEmail(
            (string) $request->input('email'),
            $name,
            $clubId,
            $step,
            $copy['message'],
            $copy['next'],
            $technical
        );

        return response()->json([
            'status' => 0,
            'message' => $message,
            'result' => [],
        ]);
    }

    protected function sendFailureEmail(
        string $email,
        string $storeName,
        int $clubId,
        string $failedStep,
        string $message,
        string $nextStep,
        string $technicalError
    ): void {
        if ($email === '') {
            return;
        }

        try {
            Mail::to($email)->send(new PartnerCreateStoreErrorMail([
                'store_name' => $storeName,
                'club_id' => $clubId,
                'failed_step' => $failedStep,
                'message' => $message,
                'next_step' => $nextStep,
                'technical_error' => $technicalError,
            ]));
        } catch (\Throwable $e) {
            Log::warning('Partner createstore failure email could not be sent', [
                'email' => $email,
                'club_id' => $clubId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function stepFailureCopy(string $step): array
    {
        switch ($step) {
            case self::STEP_ORDER:
                return [
                    'message' => 'Store setup stopped while creating the trial order. Please retry the same createstore API request to continue from the next step.',
                    'next' => 'On retry, we will resume tenant and store database creation, then complete domain linking.',
                ];
            case self::STEP_DOMAIN:
                return [
                    'message' => 'Store setup stopped while linking the store domain. Please retry the same createstore API request to finish setup.',
                    'next' => 'On retry, we will complete domain linking and return your store login details.',
                ];
            case self::STEP_TENANT:
            default:
                return [
                    'message' => 'Store setup stopped while creating the tenant and store database. Please retry the same createstore API request to continue.',
                    'next' => 'On retry, we will resume tenant provisioning and complete domain setup.',
                ];
        }
    }

    protected function findTenantByStoreOrClub(string $name, int $clubId): ?Tenant
    {
        return Tenant::where('id', $name)
            ->orWhere('club_id', $clubId)
            ->first();
    }

    protected function isStoreProvisioningComplete(Tenant $tenant): bool
    {
        if (empty($tenant->order_id)) {
            return false;
        }

        $domainName = $tenant->id . '.' . env('APP_PROTOCOLESS_URL');

        return Domain::where('tenant_id', $tenant->id)
            ->where('domain', $domainName)
            ->exists();
    }

    protected function progressCacheKey(int $clubId): string
    {
        return 'partner_createstore_progress_' . $clubId;
    }

    protected function rememberProgress(int $clubId, array $data): void
    {
        $existing = $this->getProgress($clubId);
        Cache::put($this->progressCacheKey($clubId), array_merge($existing, $data), now()->addDays(7));
    }

    protected function getProgress(int $clubId): array
    {
        $progress = Cache::get($this->progressCacheKey($clubId), []);

        return is_array($progress) ? $progress : [];
    }

    protected function forgetProgress(int $clubId): void
    {
        Cache::forget($this->progressCacheKey($clubId));
    }
}
