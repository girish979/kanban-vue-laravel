<?php

namespace App\Http\Controllers;

use App\Column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\DbDumper\Databases\MySql;

class ColumnController extends Controller
{

    public function store(Request $request){
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
        ]);

        Column::create($request->only('title'));
        
        return Column::with('tasks')->get();
    }

    public function delete(Request $request, $id){
        DB::table('columns')->delete($id);
    }

    public function exportAllColumns()
    {
        try{
            unlink('dump.sql');
        }
        catch(Exception $e){
        }

        MySql::create()
        ->setDbName(env("DB_DATABASE"))
        ->setUserName(env("DB_BACKUP_USERNAME"))
        ->setPassword(env("DB_BACKUP_PASSWORD"))
        ->dumpToFile('dump.sql');

        return response()->download('dump.sql');
    }
}
