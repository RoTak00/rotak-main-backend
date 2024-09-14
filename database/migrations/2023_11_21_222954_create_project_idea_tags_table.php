<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_idea_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("project_idea_id");
            $table->foreign('project_idea_id')->references('id')->on('project_ideas');
            $table->string("tag_name");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_idea_tags');
    }
};
