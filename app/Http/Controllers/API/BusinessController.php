<?php

namespace App\Http\Controllers\API;

use Validator;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function uploadFile (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|file|max:4096',
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Richiesta non valida.',
                'data' => null,
                'error' => $validator->errors()
            ], 422); 
        }
        $output = array();
        $STORAGE_PATH = storage_path('app/tmp/');
        if(!File::isDirectory($STORAGE_PATH)){
            File::makeDirectory($STORAGE_PATH, 0777, true, true);
        }

        foreach($request->file('files') as $file)
        {
            $filename = $file->getClientOriginalName();
            $NEW_FILE_NAME = md5($filename . microtime());
            $path = $file->storeAs('tmp', $NEW_FILE_NAME . '.' . $file->getClientOriginalExtension());
            
            // vars
            $s1 = date('YmdHisu');
            $s2 = date('YmdHisu') + 2;
            $s3 = storage_path('app/tmp/') + 'meta.json';
            $cmd = '/root/go/bin/bitsongcli tx content add '. $s1 . ' '. $s2 . ' '. $s3 . ' '. $path . ' --from faucet --stream-price 1ubtsg --download-price 10ubtsg --right-holder -y -o json "100:$RH1"';
            $confirmation = shell_exec($cmd);

            Storage::delete($path);
            $output[] = $confirmation;
        }

        return response()->json([
            'code' => 201,
            'message' => $output,
            'data' => null,
            'error' => null
        ], 201);
    }
}
