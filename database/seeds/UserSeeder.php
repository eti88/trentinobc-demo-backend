<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = $this->rand_string(8);
        \Log::info('Account password: ' . $password);
        $admin = User::create([
            'name' => 'Demo',
            'email' => 'demo@trentinobc.it',
            'password' => Hash::make($password),
        ]);
        
        DB::table('users')->where('id', $admin->id)->update([
            'email_verified_at' => \Carbon\Carbon::now(),
        ]);
    }

    private function rand_string( $length ) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    
    }
}
