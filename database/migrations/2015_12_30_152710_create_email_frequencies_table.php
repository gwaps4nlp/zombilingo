<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\EmailFrequency;

class CreateEmailFrequenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_frequencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug',20);
            $table->timestamps();
        });

        EmailFrequency::create([
            'slug' => 'never'
        ]);   
        EmailFrequency::create([
            'slug' => 'daily'
        ]);    
        EmailFrequency::create([
            'slug' => 'weekly'
        ]);      
        EmailFrequency::create([
            'slug' => 'monthly'
        ]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('email_frequencies');
    }
}
