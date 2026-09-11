<?php

namespace Database\Seeders;

use App\Models\NationalExecutivePosition;
use Illuminate\Database\Seeder;

class NationalExecutivePositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'code' => 'NEM-01',
                'position' => 'NATIONAL PRESIDENT',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-02',
                'position' => 'NATIONAL DEPUTY PRESIDENT',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-03',
                'position' => 'NATIONAL ZONAL VICE PRESIDENT SOUTH WEST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-04',
                'position' => 'NATIONAL VICE PRESIDENT SOUTH SOUTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-05',
                'position' => 'NATIONAL VICE PRESIDENT NORTH CENTRAL',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-06',
                'position' => 'NATIONAL ZONAL VICE PRESIDENT NORTH EAST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-07',
                'position' => 'NATIONAL ZONAL VICE PRESIDENT NORTH WEST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-08',
                'position' => 'NATIONAL ZONAL VICE PRESIDENT SOUTH EAST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-09',
                'position' => 'NATIONAL SECRETARY GENERAL',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-10',
                'position' => 'NATIONAL DEPUTY GENERAL SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-11',
                'position' => 'NATIONAL DIRECTOR OF AFFORESTATION',
                'status' => 'vacant',
            ],
            [
                'code' => 'NEM-12',
                'position' => 'NATIONAL ASSISTANT DIRECTOR OF AFFORESTATION',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-13',
                'position' => 'NATIONAL FINANCIAL SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-14',
                'position' => 'NATIONAL DEPUTY FINANCIAL SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-15',
                'position' => 'NATIONAL TREASURER',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-16',
                'position' => 'DEPUTY TREASURER',
                'status' => 'vacant',
            ],
            [
                'code' => 'NEM-17',
                'position' => 'NATIONAL PUBLICITY SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-18',
                'position' => 'NATIONAL DEPUTY PUBLICITY SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-19',
                'position' => 'NATIONAL ORGANIZING SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-20',
                'position' => 'NATIONAL DEPUTY ORGANIZING SECRETARY',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-21',
                'position' => 'NATIONAL ADVISER',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-22',
                'position' => 'NATIONAL DEPUTY ADVISER 1',
                'status' => 'vacant',
            ],
            [
                'code' => 'NEM-23',
                'position' => 'NATIONAL DEPUTY ADVISER 2',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-24',
                'position' => 'NATIONAL ZONAL ADVISER SOUTH WEST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-25',
                'position' => 'NATIONAL ZONAL ADVISER SOUTH SOUTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-26',
                'position' => 'NATIONAL ZONAL ADVISER SOUTH EAST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-27',
                'position' => 'NATIONAL ZONAL ADVISER NORTH EAST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-28',
                'position' => 'NATIONAL ZONAL ADVISER NORTH WEST',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-29',
                'position' => 'NATIONAL ZONAL ADVISER NORTH CENTRAL',
                'status' => 'vacant',
            ],
            [
                'code' => 'NEM-30',
                'position' => 'NATIONAL WOMEN LEADER',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-31',
                'position' => 'NATIONAL DEPUTY WOMEN LEADER SOUTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-32',
                'position' => 'NATIONAL DEPUTY WOMEN LEADER NORTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-33',
                'position' => 'NATIONAL AUDITOR',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-34',
                'position' => 'NATIONAL DEALER COORDINATOR NORTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-35',
                'position' => 'NATIONAL DEPUTY DEALER COORDINATOR NORTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-36',
                'position' => 'NATIONAL DEPUTY DEALER COORDINATOR SOUTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-37',
                'position' => 'NATIONAL DEPUTY DEALER COORDINATOR SOUTH',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-38',
                'position' => 'NATIONAL PROTOCOL OFFICER',
                'status' => 'occupied',
            ],
            [
                'code' => 'NEM-39',
                'position' => 'NATIONAL DEPUTY PROTOCOL OFFICER',
                'status' => 'occupied',
            ],
        ];

        foreach ($positions as $position) {
            NationalExecutivePosition::updateOrCreate(
                [
                    'code' => $position['code'],
                ],
                [
                    'position' => $position['position'],
                    'status' => $position['status'],
                ]
            );
        }
    }
}
