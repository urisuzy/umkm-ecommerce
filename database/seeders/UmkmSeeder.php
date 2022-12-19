<?php

namespace Database\Seeders;

use App\Models\Holding;
use App\Models\Umkm;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UmkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        $c = 0;
        try {
            $data = file_get_contents(storage_path('app/data.csv'));
            $umkms = explode(PHP_EOL, $data);
            echo count($umkms) . PHP_EOL;

            foreach ($umkms as $umkm) {
                $explodeColumn = explode(';', $umkm);
                // 1 pemilik
                // 2 umkm
                // 3 no telp peru [all same]
                // 4 email peru [all same]
                // 6 alamat
                // 
                $namePemilik = $explodeColumn[1];
                $emailPemilik = Str::slug($explodeColumn[1]) . '@gmail.com';
                $password = bcrypt('123456789');
                $umkmName = $explodeColumn[2];
                $companyPhoneNumber = $explodeColumn[3];
                $address = $explodeColumn[6];

                $user = User::where('email', $emailPemilik)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $namePemilik,
                        'email' => $emailPemilik,
                        'password' => $password,
                        'balance' => 0,
                        'email_verified_at' => date("Y-m-d H:i:s")
                    ]);
                    UserProfile::create([
                        'user_id' => $user->id,
                        'nama' => $namePemilik,
                        'alamat' => $address,
                        'no_telp' => $companyPhoneNumber
                    ]);
                }

                $holding = Holding::where('user_id', $user->id)->first();
                if (!$holding)
                    $holding = Holding::create([
                        'user_id' => $user->id,
                        'nama' => $namePemilik . ' Holding',
                        'foto' => null
                    ]);

                $umkm = Umkm::whereRelation('user', 'email', $emailPemilik)->where('nama_umkm', $umkmName)->first();
                if (!$umkm) {
                    $umkm =  Umkm::create([
                        'user_id' => $user->id,
                        'nama_umkm' => $umkmName,
                        'alamat' => $address,
                        'no_telp_umkm' => $companyPhoneNumber
                    ]);
                    $umkm->holdings()->attach($holding->id);
                }
                echo ++$c . ' | ' .  $user->id . ' | ' .  $umkm->id . ' | ' .  $user->email . ' | ' . $umkm->nama_umkm . PHP_EOL;

                if ($c >= 1000) break;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
        }
        DB::commit();
    }
}
