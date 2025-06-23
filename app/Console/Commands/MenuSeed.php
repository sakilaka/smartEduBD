<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Menu;
use Illuminate\Support\Facades\Artisan;

class MenuSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menu seed successful.';

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
        Menu::truncate();
        Artisan::call('db:seed', [
            '--class' => 'MenuSeeder',
            '--force' => true
        ]);
        Artisan::call('optimize:clear');
        $this->info('Menu seed successful.');
    }
}
