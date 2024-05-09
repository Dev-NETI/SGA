<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserRole::truncate();

        $data = [
            '1' => [1,1],
            '2' => [2,1],
            '3' => [3,1],
            '4' => [4,1],
            '5' => [5,1],
            '6' => [6,1],
            '7' => [7,1],
            '8' => [8,1],
            '9' => [9,1],
            '10' => [10,1],
            '11' => [11,1],
            '12' => [12,1],
            '13' => [13,1],
            '14' => [14,1],
            '15' => [15,1],
            '16' => [16,1],
            '17' => [17,1],
            '18' => [18,1],
            '19' => [19,1],
            '20' => [20,1],
            '21' => [21,1],
            '22' => [22,1],
            '23' => [23,1],
            '24' => [24,1],
            '25' => [25,1],
            '26' => [26,1],
            '27' => [27,1],
            '28' => [28,1],
            '29' => [29,1],
            '30' => [30,1],
            '31' => [31,1],
            '32' => [32,1],
            '33' => [33,1],
            '34' => [34,1],
            '35' => [35,1],
            '36' => [36,1],
            '37' => [37,1],
            '38' => [38,1],
            '39' => [39,1],
            '40' => [40,1],
            '41' => [41,1],
            '42' => [42,1],
            '43' => [43,1],
            '44' => [44,1],
            '45' => [45,1],
            '46' => [46,1],
        ];

        foreach($data as $index=>[$role_id,$user_id]){
            UserRole::create([
                'role_id' => $role_id,
                'user_id' => $user_id
            ]);
        }
        
    }
}
