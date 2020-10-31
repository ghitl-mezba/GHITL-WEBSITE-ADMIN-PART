<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialLinks extends Migration
{
   
    public function up()
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('link_icon_name');
            $table->string('link_title');
            $table->string('link_url')->nullable();
            $table->string('link_target')->nullable();
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('social_links');
    }
}
