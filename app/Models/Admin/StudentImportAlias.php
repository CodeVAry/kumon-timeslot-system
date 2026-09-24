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
<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentImportAlias extends Model
{
    protected $table = 'student_import_aliases';

    protected $fillable = [
        'source_name',
        'target_external_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            Student::class,
            'target_external_id',
            'external_id'
        );
    }
}
