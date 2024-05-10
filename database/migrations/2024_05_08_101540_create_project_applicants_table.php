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
        Schema::create('project_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prjoect_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('freelancer_id');
            $table->text('message');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_applicants');
    }
};
