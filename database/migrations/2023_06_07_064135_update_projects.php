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
        Schema::table('projects', function(Blueprint $table)
        {
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('inactive');
            $table->string("link_github")->nullable();

            $table->integer("home_clicks")->default('0');
            $table->integer("home_link_clicks")->default('0');

            $table->integer("dedicated_page_clicks")->default('0');
            $table->integer("dedicated_page_link_clicks")->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function(Blueprint $table)
        {
            $table->dropColumn('status');
            $table->dropColumn('link_github');

            $table->dropColumn('home_clicks');
            $table->dropColumn('home_link_clicks');
            $table->dropColumn('dedicated_page_clicks');
            $table->dropColumn('dedicated_page_link_clicks');

            
        });
    }
};
