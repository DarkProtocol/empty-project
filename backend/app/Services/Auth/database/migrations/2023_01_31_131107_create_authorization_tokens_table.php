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
        Schema::create('authorization_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Token ID');
            $table->string('token', 64)->unique()->comment('User ID');
            $table->uuid('user_id')->comment('User ID');
            $table->uuid('session_id')->comment('Session ID');
            $table->string('type', 20)->comment('Token type');
            $table->string('action', 40)->nullable()->comment('Created action (login, login-as, etc)');
            $table->uuid('created_by')->nullable()->comment('Created by User ID');
            $table->string('create_ip')->nullable()->comment('Creation IP');
            $table->string('create_country', 2)->nullable()->comment('Creation country');
            $table->text('create_ua')->nullable()->comment('Creation User-Agent');
            $table->timestamp('expire_at')->comment('Expiration date');
            $table->timestamp('revoked_at')->nullable()->comment('Revocation date');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorization_tokens');
    }
};
