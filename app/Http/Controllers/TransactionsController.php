<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Musics;
use App\Models\Projects;
use App\Models\Invitations;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Models\User;

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

            $user = JWTAuth::parseToken()->authenticate();
            $transactionFree = Transactions::where('user_id', $user->id)->where('subscribe', 'DigitalDazzle')->first();
            if(!$transactionFree){
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
                }else{
                    return response()->json([
                        'success' => false,
                    ], 409);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'data' => $transactionFree,
                    'message' => 'DigitalDazzle has already claimed before',
                ], 409);
            }
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

        if($user->role === 'admin'){
            $transaction = Transactions::with('user')->orderBy('subscribe_start', 'desc')->get();
        }else{
            $transaction = Transactions::where('user_id', $user->id)->orderBy('subscribe_start', 'desc')->get();
        }


        return response()->json(['transaction' => $transaction], 200);
    }
    public function transactionExport(Request $request)
    {
        $transaction = Transactions::with('user')->whereMonth('subscribe_start', Carbon::now()->month)->orderBy('subscribe_start', 'desc')->get();

        $result = $transaction->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'id_payment' => $transaction->id_payment,
                'user_name' => $transaction->user->name,
                'email' => $transaction->user->email,
                'no_phone' => $transaction->user->no_phone,
                'subscribe' => $transaction->subscribe,
                'status' => $transaction->status,
                'subscribe_start' => $transaction->subscribe_start,
                'subscribe_expired' => $transaction->subscribe_expired,
            ];
        });

        return response()->json(['transaction' => $result], 200);
    }
    public function getMonthlyTransaction(Request $request)
    {

        $totalsMount = Transactions::select(
            DB::raw('EXTRACT(YEAR FROM subscribe_start) as year'),
            DB::raw('EXTRACT(MONTH FROM subscribe_start) as month'),
            DB::raw('SUM(CAST(amount AS numeric)) as total_amount')
        )
        ->groupBy('year', 'month')
        ->whereYear('subscribe_start', Carbon::now()->year)
        ->get()
        ->keyBy('month')
        ->transform(function ($item) {
            return $item->total_amount;
        })
        ->all();
        $monthlyTotals = array_fill(1, 12, 0);
        foreach ($totalsMount as $month => $totalAmount) {
            $monthlyTotals[$month] = (int) $totalAmount;
        }


        $DigitalDazzle = Transactions::select(
            DB::raw('EXTRACT(YEAR FROM subscribe_start) as year'),
            DB::raw('EXTRACT(MONTH FROM subscribe_start) as month'),
            'subscribe',
            DB::raw('COUNT(*) as total')
        )
        ->where('subscribe', 'DigitalDazzle')
        ->whereYear('subscribe_start', Carbon::now()->year)
        ->groupBy('year', 'month', 'subscribe')
        ->get()
        ->keyBy('month')
        ->transform(function ($item) {
            return $item->total;
        })
        ->all();
        $monthlyDigi = array_fill(1, 12, 0);
        foreach ($DigitalDazzle as $month => $total) {
            $monthlyDigi[$month] = (int) $total;
        }


        $SparkleSend = Transactions::select(
            DB::raw('EXTRACT(YEAR FROM subscribe_start) as year'),
            DB::raw('EXTRACT(MONTH FROM subscribe_start) as month'),
            'subscribe',
            DB::raw('COUNT(*) as total')
        )
        ->where('subscribe', 'SparkleSend')
        ->whereYear('subscribe_start', Carbon::now()->year)
        ->groupBy('year', 'month', 'subscribe')
        ->get()
        ->keyBy('month')
        ->transform(function ($item) {
            return $item->total;
        });
        $monthlySpark = array_fill(1, 12, 0);
        foreach ($SparkleSend as $month => $total) {
            $monthlySpark[$month] = (int) $total;
        }


        $DigitalDreamscape = Transactions::select(
            DB::raw('EXTRACT(YEAR FROM subscribe_start) as year'),
            DB::raw('EXTRACT(MONTH FROM subscribe_start) as month'),
            'subscribe',
            DB::raw('COUNT(*) as total')
        )
        ->where('subscribe', 'DigitalDreamscape')
        ->whereYear('subscribe_start', Carbon::now()->year)
        ->groupBy('year', 'month', 'subscribe')
        ->get()
        ->keyBy('month')
        ->transform(function ($item) {
            return $item->total;
        });
        $monthlyDream = array_fill(1, 12, 0);
        foreach ($DigitalDreamscape as $month => $total) {
            $monthlyDream[$month] = (int) $total;
        }

        return response()->json([
            'transaction' =>  array_values($monthlyTotals),
            'DigitalDazzle' => array_values($monthlyDigi),
            'SparkleSend' => array_values($monthlySpark),
            'DigitalDreamscape' => array_values($monthlyDream)
        ], 200);
    }
    public function getCardDashboard(Request $request)
    {
        $invitations = Invitations::select(
            DB::raw('COUNT(*) as total')
        )
        ->where('status', 'publish')
        ->first();

        $projetcs = Projects::select(
            DB::raw('COUNT(*) as total')
        )
        ->first();

        $musics = Musics::select(
            DB::raw('COUNT(*) as total')
        )
        ->first();

        $users = User::select(
            DB::raw('COUNT(*) as total')
        )
        ->where('role', 'user')
        ->first();

        return response()->json([
            'invitations' => $invitations,
            'projetcs' => $projetcs,
            'musics' => $musics,
            'users' => $users
        ]);
    }
}
