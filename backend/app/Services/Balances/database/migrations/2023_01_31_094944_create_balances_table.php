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
        Schema::create('balances', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Balance ID');
            $table->uuid('user_id')->comment('User ID');
            $table->uuid('currency_id')->comment('Currency ID');
            $table->decimal('value', 99, 18)->default(0)->comment('Balance value');
            $table->timestamps();
        });

        Schema::table('balances', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table
                ->foreign('currency_id')
                ->references('id')
                ->on('currencies')
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
        Schema::dropIfExists('balances');
    }
};
