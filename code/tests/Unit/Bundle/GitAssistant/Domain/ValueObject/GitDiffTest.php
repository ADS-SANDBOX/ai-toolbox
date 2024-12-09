<?php

namespace Tests\Unit\Bundle\GitAssistant\Domain\ValueObject;

use App\Bundle\GitAssistant\Domain\ValueObject\GitDiff;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GitDiffTest extends TestCase
{
    #[Test]
    public function it_should_detect_empty_diff(): void
    {
        $emptyDiffs = ['', ' ', '0', 'null'];

        foreach ($emptyDiffs as $diff) {
            $gitDiff = new GitDiff($diff);
            $this->assertTrue($gitDiff->isEmpty(), "Failed asserting that '$diff' is considered empty");
        }
    }

    #[Test]
    public function it_should_detect_non_empty_diff(): void
    {
        $validDiffs = [
            'diff --git a/file.txt b/file.txt',
            '+New line',
            '-Removed line',
        ];

        foreach ($validDiffs as $diff) {
            $gitDiff = new GitDiff($diff);
            $this->assertFalse($gitDiff->isEmpty(), "Failed asserting that '$diff' is considered non-empty");
        }
    }

    #[Test]
    public function it_should_preserve_original_diff_content(): void
    {
        $originalDiff = "diff --git a/file.txt b/file.txt\n+New content\n-Old content";
        $gitDiff = new GitDiff($originalDiff);

        $this->assertEquals($originalDiff, $gitDiff->value());
    }

    #[Test]
    public function it_should_handle_multiline_diffs(): void
    {
        $multilineDiff = <<<'DIFF'
        diff --git a/file1.txt b/file1.txt
        index 1234567..89abcdef 100644
        --- a/file1.txt
        +++ b/file1.txt
        @@ -1,3 +1,4 @@
         Line 1
        -Line 2
        +Modified Line 2
        +New Line 3
         Last Line
        DIFF;

        $gitDiff = new GitDiff($multilineDiff);

        $this->assertFalse($gitDiff->isEmpty());
        $this->assertEquals($multilineDiff, $gitDiff->value());
    }
}
