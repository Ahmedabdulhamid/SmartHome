<?php

namespace App\Repositories\Frontend;

use App\Models\Contact;

class ContactRepository
{
    public function create(array $attributes): Contact
    {
        return Contact::query()->create($attributes);
    }
}
