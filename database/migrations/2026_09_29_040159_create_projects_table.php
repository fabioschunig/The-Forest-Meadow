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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type');
            $table->string('status');
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('links')->nullable();
            $table->date('started_on')->nullable();
            $table->date('released_on')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            // datetime instead of timestamp: no 2038 limit and no MySQL timezone conversion.
            $table->dateTime('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
