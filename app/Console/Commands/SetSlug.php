<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetSlug extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bucket {slug} {read?} {write?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the bucket slug';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $files;
    protected $read;
    protected $write;

    public function __construct(\Illuminate\Filesystem\Filesystem $files) {
        parent::__construct();
        $this->files = $files;
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->read = $this->argument('slug');
        $this->write = $this->argument('slug');
        $config_path = base_path() . "/config/cosmic.php";
        $content = "<?php\n\treturn [\n\t\t'slug' => '" . $this->argument('slug') . "',\n\t\t"
                . "'read' => '" . $this->argument('read')."',\n\t\t"
                ."'write' => '" . $this->argument('write')."',\n\t];";
        $this->files->put($config_path, $content);
        echo "Bucket variables set";
    }

}
