<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
     protected $fillable = ['project_id', 'division_id', 'status_id', 'created_by', 'update', 'status_date'];

    protected function casts(): array
    {
        return ['status_date' => 'date'];
    }

    public function project()  { return $this->belongsTo(Project::class); }
    public function division() { return $this->belongsTo(Division::class); }
    public function status()   { return $this->belongsTo(Status::class); }
    public function author()   { return $this->belongsTo(User::class, 'created_by'); }
}
