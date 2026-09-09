<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {

            $table->string('cac_certificate')
                ->nullable()
                ->after('photo');

            $table->string('cac_particulars_of_directors')
                ->nullable()
                ->after('cac_certificate');

            $table->string('nepc_export_license')
                ->nullable()
                ->after('cac_particulars_of_directors');

        });
    }

    public function down(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {

            $table->dropColumn([
                'cac_certificate',
                'cac_particulars_of_directors',
                'nepc_export_license',
            ]);

        });
    }
};
