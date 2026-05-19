<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DzongkhagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dzongkhags = [
            ['id' => 1, 'name' => 'Bumthang'],
            ['id' => 2, 'name' => 'Chhukha'],
            ['id' => 3, 'name' => 'Dagana'],
            ['id' => 4, 'name' => 'Gasa'],
            ['id' => 5, 'name' => 'Haa'],
            ['id' => 6, 'name' => 'Lhuntse'],
            ['id' => 7, 'name' => 'Mongar'],
            ['id' => 8, 'name' => 'Paro'],
            ['id' => 9, 'name' => 'Pema Gatshel'],
            ['id' => 10, 'name' => 'Punakha'],
            ['id' => 11, 'name' => 'Samdrup Jongkhar'],
            ['id' => 12, 'name' => 'Samtse'],
            ['id' => 13, 'name' => 'Sarpang'],
            ['id' => 14, 'name' => 'Thimphu'],
            ['id' => 15, 'name' => 'Trashigang'],
            ['id' => 16, 'name' => 'Trashiyangtse'],
            ['id' => 17, 'name' => 'Trongsa'],
            ['id' => 18, 'name' => 'Tsirang'],
            ['id' => 19, 'name' => 'Wangdue Phodrang'],
            ['id' => 20, 'name' => 'Zhemgang'],
        ];

        foreach ($dzongkhags as $dzongkhag) {
            DB::table('tbldzongkhag')->insert([
                'id' => $dzongkhag['id'],
                'name' => $dzongkhag['name'],
            ]);
        }
    }
}
