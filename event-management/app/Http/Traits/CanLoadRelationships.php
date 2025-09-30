<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait CanLoadRelationships
{
    public function LoadRelationships(Model|Builder $for, ?array $relations = null): Model|Builder
    {

        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $q ->with($relation) //Use instanceof because if true: The model is already loaded so it won't have with()
            );
        }

        return $for;
    }

    protected function shouldIncludeRelation(string $relation):bool
    {
        $include = request()->query('include');

        if (!$include){
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }
}