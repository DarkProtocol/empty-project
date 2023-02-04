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
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('token')->primary()->comment('Reset token');
            $table->uuid('user_id')->comment('User ID');
            $table->string('create_ip')->nullable()->comment('Creation IP');
            $table->string('create_country', 2)->nullable()->comment('Creation country code');
            $table->text('create_ua')->nullable()->comment('Creation User-Agent');
            $table->string('complete_ip')->nullable()->comment('Completion IP');
            $table->string('complete_country', 2)->nullable()->comment('Completion country code');
            $table->text('complete_ua')->nullable()->comment('Completion User-Agent');
            $table->timestamp('completed_at')->nullable()->comment('Completion date');
            $table->timestamps();

            $table
                ->foreign('user_id')
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
        Schema::dropIfExists('password_resets');
    }
};
