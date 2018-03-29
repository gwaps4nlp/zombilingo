<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Number;


class NumberBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'numbers:proba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch to compute scores of parser with different parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $result=1;
        $i=0;
        // while($i++<=10000){
        while($i++<100000){
            // 10 000 => 0.060884692455838
            // 50 000 => 0.05187242612288
            // 100 000 => 0.048752917851015
            // 432 450 => 0.043260172691049
            $number = Number::select('base10')->where('prime','=',1)->where('id','=',$i)->first();
            if($number){
                $result *= ($number->base10-1)/$number->base10;
            }
        }
        echo $result;
    }
}
