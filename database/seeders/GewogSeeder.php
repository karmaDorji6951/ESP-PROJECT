<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GewogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gewogs = [
            // Bumthang (1)
            ['dzongkhag_id' => 1, 'name' => 'Chhoekhor'],
            ['dzongkhag_id' => 1, 'name' => 'Chumey'],
            ['dzongkhag_id' => 1, 'name' => 'Tang'],
            ['dzongkhag_id' => 1, 'name' => 'Ura'],
            
            // Chhukha (2)
            ['dzongkhag_id' => 2, 'name' => 'Bjagchhog'],
            ['dzongkhag_id' => 2, 'name' => 'Bongo'],
            ['dzongkhag_id' => 2, 'name' => 'Chapcha'],
            ['dzongkhag_id' => 2, 'name' => 'Darla'],
            ['dzongkhag_id' => 2, 'name' => 'Dungna'],
            ['dzongkhag_id' => 2, 'name' => 'Geling'],
            ['dzongkhag_id' => 2, 'name' => 'Getana'],
            ['dzongkhag_id' => 2, 'name' => 'Lokchina'],
            ['dzongkhag_id' => 2, 'name' => 'Metakha'],
            ['dzongkhag_id' => 2, 'name' => 'Phuentsholing'],
            ['dzongkhag_id' => 2, 'name' => 'Sampheling'],
            
            // Dagana (3)
            ['dzongkhag_id' => 3, 'name' => 'Dorona'],
            ['dzongkhag_id' => 3, 'name' => 'Drukjeygang'],
            ['dzongkhag_id' => 3, 'name' => 'Gesarling'],
            ['dzongkhag_id' => 3, 'name' => 'Gozhi'],
            ['dzongkhag_id' => 3, 'name' => 'Kana'],
            ['dzongkhag_id' => 3, 'name' => 'Karmaling'],
            ['dzongkhag_id' => 3, 'name' => 'Khebisa'],
            ['dzongkhag_id' => 3, 'name' => 'Lagyab'],
            ['dzongkhag_id' => 3, 'name' => 'Lhamoi Zingkha'],
            ['dzongkhag_id' => 3, 'name' => 'Nichula'],
            ['dzongkhag_id' => 3, 'name' => 'Trashiding'],
            ['dzongkhag_id' => 3, 'name' => 'Tsangkha'],
            ['dzongkhag_id' => 3, 'name' => 'Tsendagang'],
            ['dzongkhag_id' => 3, 'name' => 'Tseza'],
            
            // Gasa (4)
            ['dzongkhag_id' => 4, 'name' => 'Khamaed'],
            ['dzongkhag_id' => 4, 'name' => 'Khatoed'],
            ['dzongkhag_id' => 4, 'name' => 'Laya'],
            ['dzongkhag_id' => 4, 'name' => 'Lunana'],
            
            // Haa (5)
            ['dzongkhag_id' => 5, 'name' => 'Bji'],
            ['dzongkhag_id' => 5, 'name' => 'Gakiling'],
            ['dzongkhag_id' => 5, 'name' => 'Katsho'],
            ['dzongkhag_id' => 5, 'name' => 'Samar'],
            ['dzongkhag_id' => 5, 'name' => 'Sangbay'],
            ['dzongkhag_id' => 5, 'name' => 'Uesu'],
            
            // Lhuntse (6)
            ['dzongkhag_id' => 6, 'name' => 'Gangzur'],
            ['dzongkhag_id' => 6, 'name' => 'Khoma'],
            ['dzongkhag_id' => 6, 'name' => 'Jarey'],
            ['dzongkhag_id' => 6, 'name' => 'Kurtoed'],
            ['dzongkhag_id' => 6, 'name' => 'Menbi'],
            ['dzongkhag_id' => 6, 'name' => 'Maedtsho'],
            ['dzongkhag_id' => 6, 'name' => 'Minjay'],
            ['dzongkhag_id' => 6, 'name' => 'Tsenkhar'],
            
            // Mongar (7)
            ['dzongkhag_id' => 7, 'name' => 'Balam'],
            ['dzongkhag_id' => 7, 'name' => 'Chhaling'],
            ['dzongkhag_id' => 7, 'name' => 'Chaskhar'],
            ['dzongkhag_id' => 7, 'name' => 'Dramdetse'],
            ['dzongkhag_id' => 7, 'name' => 'Drepong'],
            ['dzongkhag_id' => 7, 'name' => 'Gongdue'],
            ['dzongkhag_id' => 7, 'name' => 'Jurmey'],
            ['dzongkhag_id' => 7, 'name' => 'Kengkhar'],
            ['dzongkhag_id' => 7, 'name' => 'Monggar'],
            ['dzongkhag_id' => 7, 'name' => 'Narang'],
            ['dzongkhag_id' => 7, 'name' => 'Ngatshang'],
            ['dzongkhag_id' => 7, 'name' => 'Saling'],
            ['dzongkhag_id' => 7, 'name' => 'Shermuhoong'],
            ['dzongkhag_id' => 7, 'name' => 'Silambi'],
            ['dzongkhag_id' => 7, 'name' => 'Thangrong'],
            ['dzongkhag_id' => 7, 'name' => 'Tsakaling'],
            ['dzongkhag_id' => 7, 'name' => 'Tsamang'],
            
            // Paro (8)
            ['dzongkhag_id' => 8, 'name' => 'Dogar'],
            ['dzongkhag_id' => 8, 'name' => 'Dopshari'],
            ['dzongkhag_id' => 8, 'name' => 'Doteng'],
            ['dzongkhag_id' => 8, 'name' => 'Hoongrel'],
            ['dzongkhag_id' => 8, 'name' => 'Lamgong'],
            ['dzongkhag_id' => 8, 'name' => 'Lungnyi'],
            ['dzongkhag_id' => 8, 'name' => 'Naja'],
            ['dzongkhag_id' => 8, 'name' => 'Shapa'],
            ['dzongkhag_id' => 8, 'name' => 'Tsento'],
            ['dzongkhag_id' => 8, 'name' => 'Wangchang'],
            
            // Pema Gatshel (9)
            ['dzongkhag_id' => 9, 'name' => 'Chhimoong'],
            ['dzongkhag_id' => 9, 'name' => 'Chokhorling'],
            ['dzongkhag_id' => 9, 'name' => 'Chongshing'],
            ['dzongkhag_id' => 9, 'name' => 'Dechheling'],
            ['dzongkhag_id' => 9, 'name' => 'Dungmaed'],
            ['dzongkhag_id' => 9, 'name' => 'Khar'],
            ['dzongkhag_id' => 9, 'name' => 'Nanong'],
            ['dzongkhag_id' => 9, 'name' => 'Norbugang'],
            ['dzongkhag_id' => 9, 'name' => 'Shumar'],
            ['dzongkhag_id' => 9, 'name' => 'Yurung'],
            ['dzongkhag_id' => 9, 'name' => 'Zobel'],
            
            // Punakha (10)
            ['dzongkhag_id' => 10, 'name' => 'Barp'],
            ['dzongkhag_id' => 10, 'name' => 'Chubbu'],
            ['dzongkhag_id' => 10, 'name' => 'Dzomi'],
            ['dzongkhag_id' => 10, 'name' => 'Goenshari'],
            ['dzongkhag_id' => 10, 'name' => 'Guma'],
            ['dzongkhag_id' => 10, 'name' => 'Kabisa'],
            ['dzongkhag_id' => 10, 'name' => 'Lingmukha'],
            ['dzongkhag_id' => 10, 'name' => 'Shelnga Bjemi'],
            ['dzongkhag_id' => 10, 'name' => 'Talog'],
            ['dzongkhag_id' => 10, 'name' => 'Toepaisa'],
            ['dzongkhag_id' => 10, 'name' => 'Toewang'],
            
            // Samdrup Jongkhar (11)
            ['dzongkhag_id' => 11, 'name' => 'Dewathang'],
            ['dzongkhag_id' => 11, 'name' => 'Gomdar'],
            ['dzongkhag_id' => 11, 'name' => 'Langchenphu'],
            ['dzongkhag_id' => 11, 'name' => 'Lauri'],
            ['dzongkhag_id' => 11, 'name' => 'Martshala'],
            ['dzongkhag_id' => 11, 'name' => 'Orong'],
            ['dzongkhag_id' => 11, 'name' => 'Pemathang'],
            ['dzongkhag_id' => 11, 'name' => 'Phuntshothang'],
            ['dzongkhag_id' => 11, 'name' => 'Samrang'],
            ['dzongkhag_id' => 11, 'name' => 'Serthi'],
            ['dzongkhag_id' => 11, 'name' => 'Wangphu'],
            
            // Samtse (12)
            ['dzongkhag_id' => 12, 'name' => 'Dungtoe'],
            ['dzongkhag_id' => 12, 'name' => 'Dophoogchen'],
            ['dzongkhag_id' => 12, 'name' => 'Denchukha'],
            ['dzongkhag_id' => 12, 'name' => 'Namgaychhoeling'],
            ['dzongkhag_id' => 12, 'name' => 'Norbugang'],
            ['dzongkhag_id' => 12, 'name' => 'Norgaygang'],
            ['dzongkhag_id' => 12, 'name' => 'Pemaling'],
            ['dzongkhag_id' => 12, 'name' => 'Phuentshogpelri'],
            ['dzongkhag_id' => 12, 'name' => 'Sangngagchhoeling'],
            ['dzongkhag_id' => 12, 'name' => 'Samtse'],
            ['dzongkhag_id' => 12, 'name' => 'Tading'],
            ['dzongkhag_id' => 12, 'name' => 'Tashicholing'],
            ['dzongkhag_id' => 12, 'name' => 'Tendruk'],
            ['dzongkhag_id' => 12, 'name' => 'Ugentse'],
            ['dzongkhag_id' => 12, 'name' => 'Yoeseltse'],
            
            // Sarpang (13)
            ['dzongkhag_id' => 13, 'name' => 'Chhuzagang'],
            ['dzongkhag_id' => 13, 'name' => 'Chhudzom'],
            ['dzongkhag_id' => 13, 'name' => 'Dekiling'],
            ['dzongkhag_id' => 13, 'name' => 'Gakiling'],
            ['dzongkhag_id' => 13, 'name' => 'Gelephu'],
            ['dzongkhag_id' => 13, 'name' => 'Jigmechholing'],
            ['dzongkhag_id' => 13, 'name' => 'Samtenling'],
            ['dzongkhag_id' => 13, 'name' => 'Senggey'],
            ['dzongkhag_id' => 13, 'name' => 'Sherzhong'],
            ['dzongkhag_id' => 13, 'name' => 'Shompangkha'],
            ['dzongkhag_id' => 13, 'name' => 'Tareythang'],
            ['dzongkhag_id' => 13, 'name' => 'Umling'],
            
            // Thimphu (14)
            ['dzongkhag_id' => 14, 'name' => 'Chang'],
            ['dzongkhag_id' => 14, 'name' => 'Darkala'],
            ['dzongkhag_id' => 14, 'name' => 'Genye'],
            ['dzongkhag_id' => 14, 'name' => 'Kawang'],
            ['dzongkhag_id' => 14, 'name' => 'Lingzhi'],
            ['dzongkhag_id' => 14, 'name' => 'Mewang'],
            ['dzongkhag_id' => 14, 'name' => 'Naro'],
            ['dzongkhag_id' => 14, 'name' => 'Soe'],
            ['dzongkhag_id' => 14, 'name' => 'Thim Throm'],
            
            // Trashigang (15)
            ['dzongkhag_id' => 15, 'name' => 'Bartsham'],
            ['dzongkhag_id' => 15, 'name' => 'Bidung'],
            ['dzongkhag_id' => 15, 'name' => 'Kanglung'],
            ['dzongkhag_id' => 15, 'name' => 'Kangpar'],
            ['dzongkhag_id' => 15, 'name' => 'Khaling'],
            ['dzongkhag_id' => 15, 'name' => 'Lumang'],
            ['dzongkhag_id' => 15, 'name' => 'Merag'],
            ['dzongkhag_id' => 15, 'name' => 'Phongmed'],
            ['dzongkhag_id' => 15, 'name' => 'Radi'],
            ['dzongkhag_id' => 15, 'name' => 'Sagteng'],
            ['dzongkhag_id' => 15, 'name' => 'Samkhar'],
            ['dzongkhag_id' => 15, 'name' => 'Shongphoog'],
            ['dzongkhag_id' => 15, 'name' => 'Thrimshing'],
            ['dzongkhag_id' => 15, 'name' => 'Uzorong'],
            ['dzongkhag_id' => 15, 'name' => 'Yangnyer'],
            
            // Trashiyangtse (16)
            ['dzongkhag_id' => 16, 'name' => 'Bumdeling'],
            ['dzongkhag_id' => 16, 'name' => 'Jamkhar'],
            ['dzongkhag_id' => 16, 'name' => 'Khamdang'],
            ['dzongkhag_id' => 16, 'name' => 'Ramjar'],
            ['dzongkhag_id' => 16, 'name' => 'Toetsho'],
            ['dzongkhag_id' => 16, 'name' => 'Tongshang'],
            ['dzongkhag_id' => 16, 'name' => 'Yalang'],
            ['dzongkhag_id' => 16, 'name' => 'Yangtse'],
            
            // Trongsa (17)
            ['dzongkhag_id' => 17, 'name' => 'Dragteng'],
            ['dzongkhag_id' => 17, 'name' => 'Korphoog'],
            ['dzongkhag_id' => 17, 'name' => 'Langthil'],
            ['dzongkhag_id' => 17, 'name' => 'Nubi'],
            ['dzongkhag_id' => 17, 'name' => 'Tangsibji'],
            
            // Tsirang (18)
            ['dzongkhag_id' => 18, 'name' => 'Barshong'],
            ['dzongkhag_id' => 18, 'name' => 'Dunglagang'],
            ['dzongkhag_id' => 18, 'name' => 'Gosarling'],
            ['dzongkhag_id' => 18, 'name' => 'Kilkhorthang'],
            ['dzongkhag_id' => 18, 'name' => 'Mendrelgang'],
            ['dzongkhag_id' => 18, 'name' => 'Patshaling'],
            ['dzongkhag_id' => 18, 'name' => 'Phuntenchhu'],
            ['dzongkhag_id' => 18, 'name' => 'Rangthangling'],
            ['dzongkhag_id' => 18, 'name' => 'Semjong'],
            ['dzongkhag_id' => 18, 'name' => 'Sergithang'],
            ['dzongkhag_id' => 18, 'name' => 'Tsholingkhar'],
            ['dzongkhag_id' => 18, 'name' => 'Tsirang Toed'],
            
            // Wangdue Phodrang (19)
            ['dzongkhag_id' => 19, 'name' => 'Athang'],
            ['dzongkhag_id' => 19, 'name' => 'Bjena'],
            ['dzongkhag_id' => 19, 'name' => 'Darkar'],
            ['dzongkhag_id' => 19, 'name' => 'Dangchu'],
            ['dzongkhag_id' => 19, 'name' => 'Gangtey'],
            ['dzongkhag_id' => 19, 'name' => 'Gasetsho Gom'],
            ['dzongkhag_id' => 19, 'name' => 'Gasetsho Wom'],
            ['dzongkhag_id' => 19, 'name' => 'Kazhi'],
            ['dzongkhag_id' => 19, 'name' => 'Nahi'],
            ['dzongkhag_id' => 19, 'name' => 'Nyisho'],
            ['dzongkhag_id' => 19, 'name' => 'Phangyul'],
            ['dzongkhag_id' => 19, 'name' => 'Phobji'],
            ['dzongkhag_id' => 19, 'name' => 'Ruepisa'],
            ['dzongkhag_id' => 19, 'name' => 'Sephu'],
            ['dzongkhag_id' => 19, 'name' => 'Thedtsho'],
            
            // Zhemgang (20)
            ['dzongkhag_id' => 20, 'name' => 'Bardo'],
            ['dzongkhag_id' => 20, 'name' => 'Bjoka'],
            ['dzongkhag_id' => 20, 'name' => 'Goshing'],
            ['dzongkhag_id' => 20, 'name' => 'Nangkor'],
            ['dzongkhag_id' => 20, 'name' => 'Ngangla'],
            ['dzongkhag_id' => 20, 'name' => 'Phangkhar'],
            ['dzongkhag_id' => 20, 'name' => 'Shingkhar'],
            ['dzongkhag_id' => 20, 'name' => 'Trong'],
        ];

        foreach ($gewogs as $gewog) {
            DB::table('tblgewog')->insert([
                'dzongkhag_id' => $gewog['dzongkhag_id'],
                'name' => $gewog['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
