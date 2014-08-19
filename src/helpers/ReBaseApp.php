<?php namespace Amitavroy\Sentryuser;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Foundation\Artisan;

class ReBaseApp extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'su:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the CMS to starting point.';

    /**
     * Create a new command instance.
     *
     * @return \Amitavroy\Sentryuser\ReBaseApp
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('migrate', array('--package' => 'l4mod/sentryuser'));
        $this->call('asset:publish', array('l4mod/sentryuser'));
        $this->call('config:publish', array('package' => 'l4mod/sentryuser'));
        $this->call('cache:clear');
        $this->call('dump-autoload');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
// 			array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
// 			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}