<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Imports\DataImport;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\Data;
use Auth, Validator, Hash, Excel;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            $response = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($response, $response['code']);
        }
        
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($user) {
            $response = [
                'code' => 200,
                'user_id' => $user->id,
                'status' => "success",
                'message' => "Anda berhasil Register"
            ];
            return response()->json($response, $response['code']);
        }else{
            $response = [
                'code' => 401,
                'status' => "error",
                'message' => "Register gagal"
            ];
            return response($response, $response['code']);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            $output = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($output, $output['code']);
        }
        $response = [];
        $user = User::where('email', $request->email)->first();
        if($user) {
            if (Hash::check($request->password, $user->password)) {
                $response = [
                    'code' => 200,
                    'status' => "success",
                    'message' => "Anda berhasil login",
                    'token' => $user->createToken(config('app.name'))->accessToken
                ];
            }else {
                $response = [
                    'code' => 401,
                    'status' => "error",
                    'message' => "Email atau Password yang Anda masukkan salah"
                ];
            }
        }else{
            $response = [
                'code' => 401,
                'status' => "error",
                'message' => "Email yang Anda masukkan belum terdaftar"
            ];
        }
        return response()->json($response, $response['code']);
    }

    public function quote()
    {
        $source = 'https://api.chucknorris.io/jokes/random';
        $result = Http::get($source)->object();
        $response = [
            'quote' => $result->value,
            'status' => $result ? "success" : "error",
            'source' => $source
        ];
        return response()->json($result);
    }

    public function transaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'trx_id' => 'required|string',
            'user_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $output = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($output, $output['code']);
        }

        $output = array( 'trx_id' => '', 'amount' => '', 'message' => '');

        // 1. If input.amount = 0.00000001, decline
        if($request->amount == 0.00000001) {
            $output['message'] = 'Nilai amount tidak diterima';
            return response()->json($output, 400);
        }
        // 2. find balance by user_id from table balance,
        $user = User::with('balance')->find($request->user_id);
        if(!$user || !$user->balance) {
            $output['message'] = 'Data user balance tidak ditemukan';
            return response()->json($output, 400);  
        }
        // 3. if balance.amount_available < input.amount, decline (insufficient)
        if($user->balance->amount_available < $request->amount) {
            $output['message'] = 'Nilai balance amount available tidak memadai';
            return response()->json($output, 400); 
        }
        
        // 4. Insert table transaction
        try {
            DB::beginTransaction();
            $trx = Transaction::create($request->all());
            // 5. add sleep/delay 30 seconds
            sleep(30);
            if(Transaction::where('trx_id', $request->trx_id)->count() > 1) {
                // 6. If trx_id exists, decline & rollback transaction
                DB::rollBack();
                $output['message'] = 'transaksi gagal disimpan, rollback transaksi';
            return response()->json($output, 200); 
            }else {
                DB::commit();
                // 7. update balance.amount_available, (balance.amount_available - input.amount)
                $bal = Balance::where('user_id', $trx->user_id)->first();
                $bal->amount_available = ($bal->amount_available - $request->amount);
                $bal->save();

                // 8. Return data transaction & balance by user_id. Trim (not rounding) to 6
                $output['trx_id'] = $trx->trx_id;
                $output['amount'] = $bal->amount_available;
                $output['message'] = 'transaksi berhasil disimpan';

                return response()->json($output, 200);
            }
        } catch(\Exception $exp) {
            DB::rollBack(); 
            $output['message'] = 'transaksi gagal disimpan, rollback transaksi';
            return response()->json($output, 200);
        }
    }

    public function uploadPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt|max:100000'
        ]);
        if ($validator->fails()) {
            $output = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($output, $output['code']);
        }

        Excel::import(new DataImport, $request->file('file')->store('temp'));

        $response = [
            'status' => "success",
            'message' => "Harga berhasil diupload",
            'time' => date('Y-m-d H:i:s', 1502323200)
        ];
        return response($response, 201);
    }

    public function priceLowHigh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'week' => 'required',
            'year' => 'required',
            'ticker' => 'required',
            'currency' => 'required'
        ]);
        if ($validator->fails()) {
            $output = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($output, $output['code']);
        }  

        $getDate = ['type'=>'lowhigh', 'week' => $request->week, 'year' => $request->year];
        $date = self::getDate($getDate);
        $trx = Data::where('ticker', $request->ticker)->whereBetween('record_time',[$date['start_mktime'],$date['end_mktime']])->get();
        $result = collect($trx);        
        if($request->currency == 'IDR') {
            $min = $result->min('idr');
            $max = $result->max('idr');
        }else {
            $min = $result->min('usd');
            $max = $result->max('usd');
        }
        
        $output = [
            'status' => 'success',
            'message' => 'Data found',
            'data' => array(
                        'min' => $min, 
                        'max' => $max, 
                        'week'=> $request->week, 
                        'year'=> $request->year, 
                        'pair'=> ''
                    )
        ];
        return response()->json($output, 200);
    }

    public function priceHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timeframe' => 'required',
            'ticker' => 'required',
            'currency' => 'required'
        ]);
        if ($validator->fails()) {
            $output = [
                'code' => 400,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ];
            return response()->json($output, $output['code']);
        }

        $getDate = ['type'=>'history', 'timeframe' => $request->timeframe];
        $date = self::getDate($getDate);       
        if($request->currency == 'IDR') {
            $trx = Data::where('ticker', $request->ticker)->whereBetween('record_time',[$date['start_mktime'],$date['end_mktime']])->select(DB::raw('from_unixtime(record_time) as datetime'),'idr')->get();
        }else {
            $trx = Data::where('ticker', $request->ticker)->whereBetween('record_time',[$date['start_mktime'],$date['end_mktime']])->select(DB::raw('from_unixtime(record_time) as datetime'),'usd')->get();
        }
        
        $output = [
            'status' => 'success',
            'message' => 'Data found',
            'data' => $trx
        ];
        return response()->json($output, 200);
    }

    public static function getDate($data)
    {
        if($data['type'] == 'history') {
            $date = explode(' - ', $data['timeframe']);
            $date['start_mktime'] = strtotime($date[0]);;
            $date['end_mktime'] = strtotime($date[1]);;
        }else {
            $dto = new \DateTime();
            $dto->setISODate($data['year'], $data['week']);
            $date['start'] = $dto->format('Y-m-d');
            $date['start_mktime'] = mktime($dto->format('H'), $dto->format('i'), $dto->format('s'), $dto->format('m'), $dto->format('d'), $dto->format('Y'));
            $dto->modify('+6 days');
            $date['end'] = $dto->format('Y-m-d');
            $date['end_mktime'] = mktime($dto->format('H'), $dto->format('i'), $dto->format('s'), $dto->format('m'), $dto->format('d'), $dto->format('Y'));
        }
        return $date;
    }
}
