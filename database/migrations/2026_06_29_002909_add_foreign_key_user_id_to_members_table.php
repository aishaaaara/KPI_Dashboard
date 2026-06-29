<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // Fix tipe data dulu
    Schema::table('members', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->change();
    });

    // Tambah FK
    Schema::table('members', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('members', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->bigInteger('user_id')->nullable()->change();
    });
}
};
