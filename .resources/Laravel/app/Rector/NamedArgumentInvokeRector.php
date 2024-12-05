<?php

declare(strict_types=1);

namespace App\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class NamedArgumentInvokeRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            description: 'Convert positional arguments to named arguments in __invoke method calls, excluding Laravel middleware $next($request) calls',
            codeSamples: []
        );
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Variable && ! $node->name instanceof PropertyFetch) {
            return null;
        }

        // Check if this is a $next($request) call in a Laravel middleware
        if ($this->isLaravelMiddlewareNextCall($node)) {
            return null;
        }

        $variableType = $this->getType($node->name);
        if (! $variableType instanceof TypeWithClassName) {
            return null;
        }

        $className = $variableType->getClassName();
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        if (! $classReflection->hasMethod('__invoke')) {
            return null;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod('__invoke');
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

    private function isLaravelMiddlewareNextCall(FuncCall $funcCall): bool
    {
        // Check if the variable name is '$next'
        if (! $funcCall->name instanceof Variable || $funcCall->name->name !== 'next') {
            return false;
        }

        // Check if there's exactly one argument
        if (count($funcCall->args) !== 1) {
            return false;
        }

        // Check if the argument is a variable named '$request'
        $arg = $funcCall->args[0];

        return $arg->value instanceof Variable && $arg->value->name === 'request';
    }
}
