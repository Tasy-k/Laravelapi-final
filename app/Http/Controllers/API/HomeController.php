<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use App\Models\PanicAlert;
use Auth;
use Validator;
use DB;
use App\Jobs\HttpRequestsManager;

class HomeController extends ApiController
{

	//Login User Tasneem
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['api_access_token'] =  $user->createToken('WayneEnterprises')-> accessToken;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    //Create Panic

    public function create_panic(Request $request){
    	$validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = array(
        	'longitude'=>$request->longitude,
        	'latitude'=>$request->latitude,
        	'user_id'=>Auth::user()->id,
        	'panic_type'=>$request->panic_type,
        	'details'=>$request->details,
        	'status'=>'active'
        );
        $panic = PanicAlert::create($data);

        if($panic){
        	$success['panic_id'] = $panic->id;
            $httpJob = new HttpRequestsManager(1, $panic);
            $this->dispatch($httpJob);
        	return $this->sendResponse($success, 'Panic raised successfully');
        }else{
        	return $this->sendError('Error', 'Something went wrong!');
        }

    }

    //Cancel Panic
    public function cancel_panic(Request $request){
    	$validator = Validator::make($request->all(), [
            'panic_id' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $panic = PanicAlert::find($request->panic_id);
        if($panic){
            $panic->status = "cencelled";
            $panic->save();
        	$success= array();
            $httpJob = new HttpRequestsManager(2, $panic);
            $this->dispatch($httpJob);
        	return $this->sendResponse($success, 'Panic cancelled successfully');
        }else{
        	return $this->sendError('Error', 'Something went wrong!');
        }

    }


    //Get Panic History
    public function panic_history(Request $request){

//        $panics = DB::table('panic_alerts')
//        			->select('panic_alerts.*','users.*','panic_alerts.id as panic_id','panic_alerts.created_at as panic_created')
//        			->join('users','panic_alerts.user_id','=','users.id')
//        			->get();
//
//        $data = array();
//        foreach ($panics as $key => $panic) {
//        	$data[$key]['id'] = $panic->panic_id;
//        	$data[$key]['longitude'] = $panic->longitude;
//        	$data[$key]['latitude'] = $panic->latitude;
//        	$data[$key]['panic_type'] = $panic->panic_type;
//        	$data[$key]['details'] = $panic->details;
//        	$data[$key]['status'] = $panic->status;
//        	$data[$key]['created_at'] = $panic->panic_created;
//        	$data[$key]['created_by']['id'] = $panic->user_id;
//        	$data[$key]['created_by']['name'] = $panic->name;
//        	$data[$key]['created_by']['email'] = $panic->email;
//        }

        $panics = PanicAlert::with('user')->get();
        $data = array();
        foreach($panics as $panic) {
            $data[] = $panic->return_for_api();
        }

        return $this->sendResponse($data, 'Action completed successfully');

    }


}
