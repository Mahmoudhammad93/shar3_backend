<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('status_new')->default(0)->after('national_id');
        });

        DB::table('students')->where('status', 'pending')->update(['status_new' => 0]);
        DB::table('students')->where('status', 'active')->update(['status_new' => 1]);
        DB::table('students')->where('status', 'graduated')->update(['status_new' => 2]);
        DB::table('students')->where('status', 'suspended')->update(['status_new' => 3]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('status_old')->default('pending')->after('national_id');
        });

        DB::table('students')->where('status', 0)->update(['status_old' => 'pending']);
        DB::table('students')->where('status', 1)->update(['status_old' => 'active']);
        DB::table('students')->where('status', 2)->update(['status_old' => 'graduated']);
        DB::table('students')->where('status', 3)->update(['status_old' => 'suspended']);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'graduated', 'suspended'])
                ->default('pending')
                ->after('national_id');
        });

        DB::table('students')->update([
            'status' => DB::raw('status_old'),
        ]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status_old');
        });
    }
};
