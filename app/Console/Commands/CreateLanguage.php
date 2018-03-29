<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
// use Illuminate\Events\Dispatcher;
// use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use App\Models\Language;
use Artisan;

class CreateLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language:create {language} {original_language=en}';

    /** @var \Illuminate\Foundation\Application  */
    protected $app;
    /** @var \Illuminate\Filesystem\Filesystem  */
    protected $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new language to the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;
        $this->files = $files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $new_language = $this->argument('language');
        $original_language = $this->argument('original_language');

        
        $new_directory = $this->app['path.lang']. DIRECTORY_SEPARATOR . $new_language;
        $original_directory = $this->app['path.lang']. DIRECTORY_SEPARATOR . $original_language;
        
        if(!$this->files->exists($original_directory)){
            $this->error($original_language.' does not exist!');
            return;
        }

        if($this->files->exists($new_directory)){
            $this->error($new_language.' already exists!');
            while(!in_array($overwrite = $this->askOverwriteExistingFiles(),['y','n'])){

            }
            if($overwrite=='n')
                return;
        }

        $language = Language::firstOrCreate(['slug'=> $new_language]);
        $this->files->copyDirectory($original_directory,$new_directory);
        $this->files->copyDirectory(resource_path('views/lang/'.$original_language),resource_path('views/lang/'.$new_language));
        Artisan::call('translations:import');
     
    }

    private function askOverwriteExistingFiles(){
        return $this->ask('Overwrite the existing files (y/n)?');
    }
    
}
