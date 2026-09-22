<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExecutiveBioSeeder extends Seeder
{
    public function run(): void
    {
        $bios = [

            'EDU BABATUNDE' => <<<BIO
Mr. Edu Babatunde currently serves as the National President of NACPDEAN. Re-elected for a second term on April 15, 2026, he previously served as President from 2021 to December 2025, reflecting his exemplary leadership, vision, and unwavering commitment to the Association's growth and development.

He is widely recognized as a pioneer founder and key architect behind the establishment and formation of NACPDEAN, having played a pivotal role in laying the foundation for the Association's growth, institutional development, and nationwide expansion.

With over 15 years of experience in charcoal export, agro-commodity trading, and international business development, Babatunde is a distinguished accomplished exporter of agro-commodities and business executive renowned for his professionalism, integrity, and expertise in sustainable charcoal production and global marketing.

Under his leadership, NACPDEAN has strengthened industry standards, enhanced regulatory compliance, championed afforestation initiatives, and forged strategic partnerships that continue to advance Nigeria's charcoal and agro-commodity sectors globally.
BIO,

            'ALHAJI ABDULSALAMI ABUBAKAR RIJANA' => <<<BIO
Alhaji Abdulsalami Abubakar Rijana currently serves as the National Deputy President of NACPDEAN. He previously served as the National Organizing Secretary, demonstrating outstanding leadership and commitment to the Association's growth and development.

A native of Rijana Town in Kachia Local Government Area of Kaduna State, Alhaji Rijana is a seasoned entrepreneur who has been actively engaged in the charcoal business since 2014, making it his primary occupation and source of livelihood. He is also recognized as one of the active figures involved in the establishment and formation of NACPDEAN.

Professionally, he began his career with Kaduna State Textile as a Clerical Officer and later worked with Arab Bank and Flour Mills Nigeria. He also held several strategic political appointments, including advisory roles within Kaduna State and Kachia Local Government.
BIO,

            'CHIEDOZIE ONYEKWERE' => <<<BIO
Mr. Chiedozie Onyekwere currently serves as the National Zonal Vice President (South-East) of NACPDEAN. He previously served as the National Publicity Secretary, demonstrating his dedication and commitment to the Association's growth and development.

Mr. Onyekwere is recognized as one of the core figure pioneer members and founding leaders to the formation of NACPDEAN, having played a significant role in the establishment and expansion of the Association.

He is the Managing Director of Capitano Nigeria Limited, a company specializing in the production, supply, and export of premium natural hardwood charcoal for barbecue, restaurant, domestic, and industrial use. With over 10 years of experience in the charcoal export business, he has successfully supplied clients across the Middle East, Europe, and the United States, earning a reputation for quality, reliability, and long-term business partnerships.
BIO,

            'GABRIEL NWACHUKWU IKPIDE' => <<<BIO
Gabriel Nwachukwu Ikpide currently serves as the National Vice President (South-South) of NACPDEAN, a position he has previously held with distinction. He is a seasoned entrepreneur, exporter, and respected business leader with over a decade of experience in the charcoal export industry.

As the Chief Executive Officer and Managing Director of GABWIDE Nigeria Limited, Mr. Ikpide has successfully built a reputable enterprise engaged in charcoal trading and international export, contributing significantly to the growth of Nigeria's non-oil export sector.

He is accepted as an active stakeholder and contributor to the development of NACPDEAN, demonstrating unwavering commitment to advancing the interests of the charcoal industry in Nigeria.
BIO,

            // ---- Continue with the rest of the 34 bios ----
            // (See the full list below)

        ];

        foreach ($bios as $fullName => $bio) {

            // Match against surname + first_name + middle_name in member_profiles
            $parts = explode(' ', $fullName);

            $query = DB::table('member_profiles');

            // Build a query that matches the profile regardless of name order
            $query->where(function ($q) use ($parts) {
                foreach ($parts as $part) {
                    $q->where(function ($inner) use ($part) {
                        $inner->where('surname', 'like', "%{$part}%")
                              ->orWhere('first_name', 'like', "%{$part}%")
                              ->orWhere('middle_name', 'like', "%{$part}%");
                    });
                }
            });

            $query->update(['bio' => $bio]);
        }
    }
}
