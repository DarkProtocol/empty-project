<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('User ID');
            $table->string('email')->unique()->comment('User email');
            $table->string('nickname', 30)->unique()->comment('User nickname');
            $table->string('password')->comment('Hashed password');
            $table->string('role', 40)->comment('User role');
            $table->uuid('ref_id')->nullable()->comment('Referer id');
            $table->string('utm')->nullable()->comment('User UTM');
            $table->string('create_ip')->nullable()->comment('Creation IP');
            $table->string('create_country', 2)->nullable()->comment('Creation country code');
            $table->text('create_ua')->nullable()->comment('Creation User-Agent');
            $table->boolean('is_banned')->default(false)->comment('Is user banned');
            $table->string('ban_reason', 100)->nullable()->comment('User ban reason');
            $table->timestamp('banned_at')->nullable()->comment('When user was banned');
            $table->timestamp('activated_at')->nullable()->comment('Account activation date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
