<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\DeletionReason;

class MigrateNegativeAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert("insert into annotations (corpus_id, sentence_id, relation_id, score, word_position, word, category_id, pos_id, governor_position, source_id,undecided, best, playable) 
            select a1.corpus_id, a1.sentence_id, negative_annotations.relation_id, score, 99999, a1.word, a1.category_id, a1.pos_id, a1.word_position, a1.source_id, undecided,best,playable from negative_annotations, annotations a1 where negative_annotations.annotation_id = a1.id 
            and negative_annotations.visible = 1");     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::delete('delete from annotations where source_id = 1 and (word_position = 99999 or governor_position = 99999 )');
    }
}
