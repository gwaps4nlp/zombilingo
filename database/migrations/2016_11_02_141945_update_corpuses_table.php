<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorpusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corpuses', function ($table) {
            $table->boolean('playable')->after('source_id')->default(0);
            $table->string('title',500)->after('name')->default("");
            $table->string('url_source',500)->after('title')->default("");
            $table->string('url_info_license',500)->after('url_source')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
