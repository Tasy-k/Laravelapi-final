<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;
use Log;

class HttpRequestsManager implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 120;
    protected $token;

    protected $request;
    protected $step;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($token, $step,$request,$data)
    {
       // $this->token = $token;

        $this->step = $step;
        $this->request = $request;
        $this->data = $data;
        //
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYmJhNjQwODkwYzg0OWZhNGZiYmUzODliYTc2YzI5YTVjZTc0NzIxZTRhYjA5NWI1NzMxMjM0NjQxODZiMTU2ZjQxODdkMGYxZGJhNTgyNWYiLCJpYXQiOjE2MTcxMDQ3ODMsIm5iZiI6MTYxNzEwNDc4MywiZXhwIjoxNjQ4NjQwNzgzLCJzdWIiOiI1Iiwic2NvcGVzIjpbXX0.ouIZI-uXCZstqqMnVGlAq6-ya2l6i4unkHHVqJ2MIOyiXr_giO4Ah3Zew2ZKtlSAzBl7yWjMHiljKzNgUpM78fxHzolIVFxzXNKtnRRIjAjhbLEzhpl39lzZogZ4tHLtdptS6Ils2H8ovq_AEvS1aDcOoBR-pZoNuISvoKGPeDs0aH2sS_Lx1Su6MzoOGHt0XYinkYTXXgGHO8wvNomhWO17YgSMzzejxltlE8Io5jRgmf2w1wYNMKJ2MNw3N4yGfa-wWSBvPtdVKzitMFbnvXjn-DcT5GFiJljvsMm5wTE80WS8YEvMEcHIkB3NBdQzSYnUEMsjwxRz1inf9Hy5sbri8poNqJbpUrCwx3NVoNaPrglapL64wURBucuvMcaG7lZCb3LmXKYlz9czZ_-gbPKyyFkMEWz_nRO4gOiZ7_ZfHReBJ4Dr3_cN2QZPFbARyU_WYiIvi1cnqXJFKWN-oCrQHaQaOgf85Ob4tqvG01MsMBGouwVXDU6Q-54ORIKfjmWsUcxCkX-WYGI1fJWoYL6WTxfGv8ChH92tr0dSXfwED9st3wwe6KdP0OQCRv9QT_Q-P5qDHB2CcvTbls3q1pI6oGceEX8Ea0aj89Vg1hKoB6TD8HARyxsHPVregp31RmAQqDRRFPFZONd1sno0uGppEfSBskTDSjg612PJk4A';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug($this->request);
        //
        if($this->request == 1){
            $url = 'https://wayne.fusebox-staging.co.za/api/v1/panic/create';
            $response = Http::withToken($this->token)->post($url, $this->data);
            $log = [
                'REQUEST_TYPE' => $this->request,
                'URI' => $url,
                'METHOD' => 'POST',
                'REQUEST_BODY' => $this->data,
                'RESPONSE' => json_decode($response)
            ];

          //  Log::info(json_encode($log));
         //   Log::debug($response);
            Log::debug(json_encode($log));
        }else if($this->request == 2){
            
            $url = 'https://wayne.fusebox-staging.co.za/api/v1/panic/cancel';
        $response = Http::withToken($this->token)->post($url, $this->data);
        $log = [
            'REQUEST_TYPE' => $this->request,
            'URI' => $url,
            'METHOD' => 'POST',
            'REQUEST_BODY' => $this->data,
            'RESPONSE' => json_decode($response)
        ];
        Log::debug(json_encode($log));
    }
        //todo log this request and response to db

    }

}
