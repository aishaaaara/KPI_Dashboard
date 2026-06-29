<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    // Fix tipe data performance_insights
    Schema::table('performance_insights', function (Blueprint $table) {
        $table->unsignedBigInteger('member_id')->nullable()->change();
        $table->unsignedBigInteger('period_id')->nullable()->change();
    });

    // Fix tipe data register_requests
    Schema::table('register_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('member_id')->nullable()->change();
    });

    // Tambah FK performance_insights
    Schema::table('performance_insights', function (Blueprint $table) {
        $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
    });

    // Tambah FK register_requests
    Schema::table('register_requests', function (Blueprint $table) {
        $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('performance_insights', function (Blueprint $table) {
        $table->dropForeign(['member_id']);
        $table->dropForeign(['period_id']);
    });

    Schema::table('register_requests', function (Blueprint $table) {
        $table->dropForeign(['member_id']);
    });
}
};
