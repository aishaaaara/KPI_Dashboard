<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {

            $table->id();

            $table->string('eid')->unique();

            $table->string('name');

            $table->foreignId('position_id')
      ->constrained('positions')
      ->onDelete('cascade');

$table->foreignId('team_id')
      ->constrained('teams')
      ->onDelete('cascade');

$table->foreignId('employment_type_id')
      ->constrained('employment_types')
      ->onDelete('cascade');

            $table->date('join_date')->nullable();

            $table->date('end_date')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};