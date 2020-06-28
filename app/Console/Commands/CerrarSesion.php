<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CerrarSesion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sesiones:cerrar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra las sesiones de los usuarios que las tienen abiertas tras 30 dias';

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
        $sesiones_caducadas = DB::select('SELECT id, TIMESTAMPDIFF(DAY,created_at, CURRENT_TIMESTAMP()) tiempo  FROM sesiones
            HAVING tiempo > 29');

        foreach($sesiones_caducadas as $s)
        {
            $sesion = \App\Sesiones::find($s->id);
            $sesion->active = 0;
            $sesion->save();
        }

        //
    }
}
