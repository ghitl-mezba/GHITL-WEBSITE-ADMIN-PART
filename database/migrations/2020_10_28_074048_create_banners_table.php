<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->Integer('banner_id')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_title')->nullable();
            $table->string('banner_sub_title')->nullable();
            $table->string('banner_button_name')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('banner_target')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
