<?php  namespace App\Services;

use Event;

class SeleniumServer {

    public function __construct(){
        // echo "tetskdjfklgjdf";
        // system("java -jar /Users/Nicolas/Downloads/selenium-server-standalone-2.53.0.jar");
    }

    public function start(){
        // echo "42";
        system("java -jar /Users/Nicolas/Downloads/selenium-server-standalone-2.53.0.jar");
    }

}