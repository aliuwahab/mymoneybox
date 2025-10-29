<?php

namespace App\Enums;

enum ContributorIdentity: string
{
    case AnonymousAllowed = 'anonymous_allowed';
    case MustIdentify = 'must_identify';
    case UserChoice = 'user_choice';
}
