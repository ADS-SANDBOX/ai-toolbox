<?php

namespace App\Bundle\GitAssistant\Domain\Exception;

use Exception;

final class EmptyGitDiffException extends Exception
{
    public function __construct()
    {
        parent::__construct('Git diff content cannot be empty');
    }
}
