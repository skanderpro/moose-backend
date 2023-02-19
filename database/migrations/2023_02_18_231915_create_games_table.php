<?php

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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('first_team_id')->constrained('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('second_team_id')->constrained('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('seasons')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['left', 'right']);
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
        Schema::dropIfExists('games');
    }
};
