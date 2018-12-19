<?php

namespace App\Rules;

use Laravel\Nova\Rules\RelatableAttachment as NovaRelatableAttachment;

class RelatableAttachment extends NovaRelatableAttachment
{
    public function passes($attribute, $value)
    {
        $this->query->getQuery()->bindings['select'] = [];
        return parent::passes($attribute, $value);
    }
}
