<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained('roles')->restrictOnDelete();
            $table->string('first_name')->after('role_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->unique()->after('last_name');
            $table->string('phone_number', 30)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone_number');
            $table->date('dob')->nullable()->after('address');
            $table->string('image')->nullable()->after('dob');
            $table->unsignedBigInteger('country_id')->nullable()->after('image');
            $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
            $table->string('zipcode', 20)->nullable()->after('city_id');
            $table->json('logs')->nullable()->after('zipcode');
            $table->tinyInteger('status')->default(1)->after('logs'); // 1 = Active, 2 = Deactive
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->softDeletes()->after('updated_at');

            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'first_name',
                'last_name',
                'username',
                'phone_number',
                'address',
                'dob',
                'image',
                'country_id',
                'state_id',
                'city_id',
                'zipcode',
                'logs',
                'status',
                'created_by',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
