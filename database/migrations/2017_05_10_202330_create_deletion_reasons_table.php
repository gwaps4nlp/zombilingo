<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\DeletionReason;

class CreateDeletionReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deletion_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('description');
            $table->timestamps();
        });
        DeletionReason::create([
            'slug' => 'user-delete',
            'description' => 'deleted by the user'
        ]); 
        DeletionReason::create([
            'slug' => 'admin-delete',
            'description' => 'deleted by an administrator'
        ]); 
        DeletionReason::create([
            'slug' => 'user-update',
            'description' => 'updated by the user'
        ]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('deletion_reasons');
    }
}
