<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Midtrans\CreateSnapTokenService;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class TransactionsController extends Controller
{
    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'id_payment' => 'required',
            'amount' => 'required|numeric',
            'status' => 'required|in:success,failed,pending',
            'subscribe' => 'required|in:DigitalDazzle,SparkleSend,DigitalDreamscape',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if($request->subscribe === 'SparkleSend'){
            $currentDateTime = date("Y-m-d H:i:s");
            $expiredTime = date("Y-m-d H:i:s", strtotime('+1 months', strtotime($currentDateTime)));
        }else if($request->subscribe === 'DigitalDreamscape'){
            $currentDateTime = date("Y-m-d H:i:s");
            $expiredTime = date("Y-m-d H:i:s", strtotime('+3 months', strtotime($currentDateTime)));
        }else{
            $currentDateTime = date("Y-m-d H:i:s");
            $expiredTime = date("Y-m-d H:i:s", strtotime('+7 days', strtotime($currentDateTime)));
        }

        $transaction = Transactions::create(array_merge(
            $validator->validated(),
            ['subscribe_start' => $currentDateTime],
            ['subscribe_expired' => $expiredTime],
        ));

        if($transaction) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction successfully created',
            ], 200);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }
    public function showToken(Request $request)
    {
       try {
            $midtrans = new CreateSnapTokenService();
            $snapToken = $midtrans->getSnapToken($request->detailsTransaction);
           return response()->json([
               'success' => true,
               'token' => $snapToken
           ], 200);
       } catch (\Throwable $th) {
           return response()->json([
               'success' => false,
               'message' =>$th->getMessage(),
           ]);
       }
    }
    public function historyByUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $transaction = Transactions::where('user_id', $user->id)->orderBy('subscribe_start', 'asc')->get();

        return response()->json(['transaction' => $transaction], 200);
    }
}
