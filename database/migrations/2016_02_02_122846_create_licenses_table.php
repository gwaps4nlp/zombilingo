<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\License;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug',20);
            $table->string('label',30);
            $table->string('image',100);
            $table->string('url',255);
            $table->timestamps();
        });

        License::create([
            'label' => 'CC BY-NC-SA',
            'slug' => 'by-nc-sa',
            'image' => 'logos/by-nc-sa.svg',
            'url' => ''
        ]);
        
        License::create([
            'label' => 'CC BY-SA',
            'slug' => 'by-sa',
            'image' => 'logos/by-sa.svg',
            'url' => ''
        ]);

        License::create([
            'label' => 'CC BY',
            'slug' => 'by',
            'image' => 'logos/by.svg',
            'url' => ''
        ]);

        License::create([
            'label' => 'GNU GPL',
            'slug' => 'gpl',
            'image' => 'logos/gpl.svg',
            'url' => ''
        ]);
        
        Schema::table('corpuses', function ($table) {
            $table->integer('license_id')->unsigned()->after('source_id')->default(1);
            $table->foreign('license_id')
                        ->references('id')
                        ->on('licenses')
                        ->onDelete('restrict')
                        ->onUpdate('restrict');          
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('licenses');
    }
}
