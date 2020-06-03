<?php

namespace App\Http\Controllers\API;

use App\Transaction;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|string|max:25',
            'from' => 'sometimes|date_format:"Y-m-d"',
            'to' => 'sometimes|date_format:"Y-m-d"'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Not valid request.',
                'data' => null,
                'error' => $validator->errors()
            ], 422); 
        }

        $typeQuery = $request->get('type');
        $rangeDateFromQuery = $request->get('from');
        $rangeDateToQuery = $request->get('to');

        $items = Transaction::where('id', '>', 0)->when($typeQuery, function ($query) use ($typeQuery) {
            return $query->where('type', '=', $typeQuery);
        })->when($rangeDateFromQuery, function ($query) use ($rangeDateFromQuery) {
            return $query->where('created_at', '>=', $rangeDateFromQuery);
        })->when($rangeDateToQuery, function ($query) use ($rangeDateToQuery) {
            return $query->where('created_at', '<=', $rangeDateToQuery);
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'code' => 200,
            'message' => 'List transactions',
            'data' => $items,
            'error' => null
        ], 200);
    }
}
