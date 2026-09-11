<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlacklistedMember;

class BlacklistedMemberSeeder extends Seeder
{
    public function run(): void
    {
        BlacklistedMember::create([
            'membership_number' => 'NACP/EXP/2026/0001',
            'member_name' => 'Mary Jane',
            'company_name' => 'XYZ Charcoal Export Ltd.',
            'state' => 'Lagos',
            'photo' => 'assets/img/blacklisted/avatar2.jpeg',
            'effective_date' => '2026-07-14',
            'status' => 'blacklisted',
            'reason' => 'Following a disciplinary investigation, the stakeholder was found to have engaged in fraudulent business practices, including the presentation of falsified export documentation and actions detrimental to the integrity and objectives of the Association. The Executive Council approved the blacklisting until further notice.',
            'blacklisted_until' => null,
        ]);

        BlacklistedMember::create([
            'membership_number' => 'NACP/EXP/2026/0002',
            'member_name' => 'Alex Smith',
            'company_name' => 'XYZ Charcoal Export Ltd.',
            'state' => 'Lagos',
            'photo' => 'assets/img/blacklisted/avatar3.jpeg',
            'effective_date' => '2026-07-14',
            'status' => 'blacklisted',
            'reason' => 'Following a disciplinary investigation, the stakeholder was found to have engaged in fraudulent business practices, including the presentation of falsified export documentation and actions detrimental to the integrity and objectives of the Association. The Executive Council approved the blacklisting until further notice.',
            'blacklisted_until' => null,
        ]);

        BlacklistedMember::create([
            'membership_number' => 'NACP/SUP/2026/0003',
            'member_name' => 'John Doe',
            'company_name' => 'Doe Charcoal Trading Company',
            'state' => 'Ogun',
            'photo' => 'assets/img/blacklisted/avatar4.jpeg',
            'effective_date' => '2026-08-02',
            'status' => 'blacklisted',
            'reason' => 'Following a disciplinary investigation, the stakeholder was found to have engaged in fraudulent business practices and activities considered detrimental to the integrity and objectives of the Association. The Executive Council approved the blacklisting until further notice.',
            'blacklisted_until' => null,
        ]);
    }
}
