<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSizePivotTable extends Migration
{
   
    public function up()
    {
        Schema::create('product_size', function (Blueprint $table) {
            $table->bigInteger('product_id');
            $table->bigInteger('size_id');
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('size_tag');
    }
}
