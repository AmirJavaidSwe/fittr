<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class ArrayFieldExistsInDatabase implements ValidationRule
{

    protected $connection;
    protected $table;
    protected $field;

    public function __construct($connection, $table, $field)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->field = $field;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $count = DB::connection($this->connection)->table($this->table)
            ->whereIn($this->field, $value)
            ->count();
        if (!$count) {
            $fail('The selected :attribute do not exist in the database.');
        }
    }
}
