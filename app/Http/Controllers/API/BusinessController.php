<?php

namespace App\Http\Controllers\API;

use Validator;
use File;
use App\Transaction;
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
                'message' => 'Not valid request.',
                'data' => null,
                'error' => $validator->errors()
            ], 422); 
        }

        $STORAGE_PATH = storage_path('app/tmp/');
        if(!File::isDirectory($STORAGE_PATH)){
            File::makeDirectory($STORAGE_PATH, 0777, true, true);
        }
        
        $insertedRecords = array();
        foreach($request->file('files') as $file)
        {
            $filename = $file->getClientOriginalName();
            $NEW_FILE_NAME = md5($filename . microtime());
            $path = $file->storeAs('tmp', $NEW_FILE_NAME . '.' . $file->getClientOriginalExtension());
            \Log::info('tmp file path: ' . $path);
            // vars
            $s1 = date('YmdHisu');
            $s2 = date('YmdHisu') - 22;
            $s3 = storage_path('app/tmp/') . 'meta.json';
            shell_exec('RH1=$(/root/go/bin/bitsongcli keys show faucet -a --keyring-backend=test)');
            $cmd = '/root/go/bin/bitsongcli tx content add '. $s1 . ' '. $s2 . ' '. $s3 . ' '. $path . ' -y -o json --from faucet --stream-price 1ubtsg --download-price 10ubtsg --right-holder "100:$RH1"';
            \Log::info($cmd);
            $confirmation = shell_exec($cmd);

            Storage::delete($path);
            $tmp = json_decode($confirmation);
            $item = Transaction::create([
                'type' => 'FILE',
                'hash' => $tmp->txhash,
                'fileName' => $filename,
                'extension' => $file->getClientOriginalExtension()
            ]);
            $insertedRecords[] = $tmp->txhash;
        }

        return response()->json([
            'code' => 201,
            'message' => $insertedRecords,
            'data' => null,
            'error' => null
        ], 201);
    }
}
