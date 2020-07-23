<?php

namespace App\Console\Commands;

use App\Notifications\SSHLoginNotification;
use App\SSHLoginLogs;
use App\User;
use Illuminate\Console\Command;

class LoginCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'login:check {user} {ip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sistema de seguridad ssh tornadocore';

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
        $user = $this->argument('user');
        $ip = $this->argument('ip');

        $registro = SSHLoginLogs::create([
            'user' => $user,
            'ip' => $ip,
        ]);

        $ivan = User::find(1);
        $ivan->notify(new SSHLoginNotification($registro->id));
    }
}
