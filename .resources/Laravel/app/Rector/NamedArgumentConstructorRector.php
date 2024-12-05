<?php

declare(strict_types=1);

namespace App\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class NamedArgumentConstructorRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            description: 'Convert positional arguments to named arguments in constructor calls and correct misnamed arguments',
            codeSamples: []
        );
    }

    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param  New_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (count($node->args) === 0) {
            return null;
        }

        $className = $this->getName($node->class);
        if ($className === null) {
            return null;
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $extendedMethodReflection = $classReflection->getConstructor();

        $parametersAcceptorWithPhpDocs = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        $parameters = $parametersAcceptorWithPhpDocs->getParameters();

        $newArgs = [];
        $hasChanges = false;

        foreach ($parameters as $index => $parameter) {
            $paramName = $parameter->getName();

            if (isset($node->args[$index])) {
                $arg = $node->args[$index];

                // If the argument is not named or has the wrong name, update it
                if (! $arg->name || $arg->name->name !== $paramName) {
                    $newArg = new Arg(value: $arg->value, byRef: false, unpack: false, attributes: [], name: new Identifier(name: $paramName));
                    $newArgs[] = $newArg;
                    $hasChanges = true;
                } else {
                    $newArgs[] = $arg;
                }
            }
        }

        if ($hasChanges) {
            $node->args = $newArgs;

            return $node;
        }

        return null;
    }
}
