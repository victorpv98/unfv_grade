<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('schools')->insert([
            ['code'=>'ELEC','name'=>'Escuela Profesional de Ingeniería Electrónica','created_at'=>$now,'updated_at'=>$now],
            ['code'=>'INFO','name'=>'Escuela Profesional de Ingeniería Informática','created_at'=>$now,'updated_at'=>$now],
            ['code'=>'MECA','name'=>'Escuela Profesional de Ingeniería Mecatrónica','created_at'=>$now,'updated_at'=>$now],
            ['code'=>'TELE','name'=>'Escuela Profesional de Ingeniería de Telecomunicaciones','created_at'=>$now,'updated_at'=>$now],
        ]);
    }
}