<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StudentImportAlias extends Model
{
    protected $table =
        'student_import_aliases';


    protected $fillable = [
        'source_name',
        'target_external_id',
    ];
}
