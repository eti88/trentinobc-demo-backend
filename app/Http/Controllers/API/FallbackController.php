<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FallbackController extends Controller
{
    public function index()
    {
        return response()->json([
            'code' => 404,
            'message' => 'Route non trovata.',
            'data' => null,
            'error' => null
        ], 404); 
    }
}
