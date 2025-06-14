<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookCategoryPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_category', function (Blueprint $table) {
            // $table->foreignId('book_id')->constrained()->onDelete('cascade');
            // $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('category_id');
            //No foreign key constraints
            //you could remove the constraints in your pivot table to mimic broken referential integrity.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_category');
    }
}
