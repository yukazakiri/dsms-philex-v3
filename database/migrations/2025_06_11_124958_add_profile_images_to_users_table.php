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
            $table->string('avatar')->nullable()->after('email_verified_at');
            $table->string('cover_image')->nullable()->after('avatar');
            
            // Facebook integration fields
            $table->string('facebook_id')->nullable()->after('cover_image');
            $table->string('facebook_avatar')->nullable()->after('facebook_id');
            $table->string('facebook_profile_url')->nullable()->after('facebook_avatar');
            $table->string('provider')->nullable()->after('facebook_profile_url'); // oauth provider (facebook, google, etc.)
            $table->string('provider_id')->nullable()->after('provider'); // provider specific ID
            $table->json('provider_data')->nullable()->after('provider_id'); // additional provider data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 
                'cover_image', 
                'facebook_id',
                'facebook_avatar', 
                'facebook_profile_url',
                'provider',
                'provider_id',
                'provider_data'
            ]);
        });
    }
};
