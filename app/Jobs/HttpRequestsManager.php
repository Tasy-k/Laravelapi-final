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

    protected $step;
    protected $panic;
    protected $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($step, $panic)
    {
        $this->step = $step;
        $this->panic = $panic;
        $this->url = 'https://wayne.fusebox-staging.co.za/api/v1/';
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
        if($this->step == 1){
            $endpoint = "panic/create";
            $data = [
                'longitude' => $this->panic->longitude,
                'latitude' => $this->panic->latitude,
                'panic_type' => $this->panic->panic_type,
                'details' => $this->panic->details,
                'reference_id' => $this->panic->id,
                'user_name' => $this->panic->user->name
            ];
            $response = $this->send_request($endpoint, $data);
            if (isset ($response->data->panic_id)) {
                $this->panic->wayne_id = $response->data->panic_id;
                $this->panic->save();
            }
        } else if ($this->step == 2){
            $endpoint = "panic/cancel";
            $data = [
                'panic_id' => $this->panic->wayne_id
            ];
            $this->send_request($endpoint, $data);
        }

        //todo log this request and response to db

    }

    public function send_request ($endpoint, $data)
    {
        $url = $this->url . $endpoint;
        $response = Http::withToken($this->token)->post($url, $data);
        $json_decoded_response = json_decode($response);
        $log = [
            'REQUEST_TYPE' => $this->step,
            'URI' => $url,
            'METHOD' => 'POST',
            'REQUEST_BODY' => $data,
            'RESPONSE' => $json_decoded_response
        ];
        Log::debug(json_encode($log));
        return $json_decoded_response;
    }

}
