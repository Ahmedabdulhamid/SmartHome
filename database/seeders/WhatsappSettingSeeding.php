<?php

namespace Database\Seeders;

use App\Models\WhatsappSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhatsappSettingSeeding extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $whatsappSettings = [
            'meta_app_id' => '1489178819035239',
            'phone_number_id' => '855819817617247',
            'whatsapp_business_account_id' => '1128152369100167',
            'meta_access_token' => 'EAAVKZAnaEAGcBQK2ZCZBr0M84s4LCl21ME5d9zc9I6DZB6N0oZCsbStXi1Iwiic2iv1XbvVE6VVi2Ho58nM2U3xaEpfAeJ1uLYSi35QvsZBr1O6dvdJ55ZASPTmGchiHAeqfv7eJ020VIucw2odTPTZB0wE7UmrMrV51VePzvHBULho3renZA5ZC8a1iIDfScjPBFW7reQFRh26MyIZCXeJr0TYbY7nd4qH7lfjWY7TRtFM3gt3zmbl95A4wpzKFRZAlaXJcv1oaaX2dHEHCFF6y7RGjs1aEqwQZD',
            'meta_verify_token' => 'MetaWhatsappToken',
        ];
        WhatsappSetting::create($whatsappSettings);

    }
}
