<?php

declare(strict_types=1);

namespace Boeq\Uat\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    // The #[Unguarded] attribute only exists from Laravel 13 on, and this package supports Laravel 12.
    protected $guarded = [];

    #[\Override]
    public function getTable(): string
    {
        // Eloquent copies the resolved table onto every instance it creates, so only an unset table gets the prefix.
        return $this->table ?? config()->string('uat.table_prefix').parent::getTable();
    }
}
