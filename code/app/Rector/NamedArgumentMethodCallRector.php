<?php

declare(strict_types=1);

namespace App\Rector;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class NamedArgumentMethodCallRector extends AbstractRector
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            description: 'Convert positional arguments to named arguments in non-native PHP method calls',
            codeSamples: []
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param  MethodCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (count($node->args) === 0) {
            return null;
        }

        $methodName = $this->getName($node->name);
        if ($methodName === null) {
            return null;
        }

        $callerType = $this->getType($node->var);
        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        $className = $callerType->getClassName();

        // Skip native PHP classes
        if ($this->isNativePhpClass($className)) {
            return null;
        }

        try {
            if (! $this->reflectionProvider->hasClass($className)) {
                return null;
            }

            $classReflection = $this->reflectionProvider->getClass($className);

            if (! $classReflection->hasMethod($methodName)) {
                return null;
            }

            $methodReflection = $classReflection->getNativeMethod($methodName);
            $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
            $parameters = $parametersAcceptor->getParameters();

            $newArgs = [];
            $hasChanges = false;

            foreach ($parameters as $index => $parameter) {
                $paramName = $parameter->getName();

                if (isset($node->args[$index])) {
                    $arg = $node->args[$index];

                    // Skip if the argument value is null (can happen in Laravel routes)
                    if ($arg->value === null) {
                        $newArgs[] = $arg;

                        continue;
                    }

                    // If the argument is not named or has the wrong name, update it
                    if (! $arg->name || $arg->name->name !== $paramName) {
                        $newArg = new Arg(
                            value: $arg->value,
                            byRef: $arg->byRef,
                            unpack: $arg->unpack,
                            attributes: $arg->getAttributes(),
                            name: new Identifier(name: $paramName)
                        );
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
        } catch (Exception) {
            // Log the exception or handle it as needed
            // For now, we'll just skip this node
            return null;
        }

        return null;
    }

    private function isNativePhpClass(string $className): bool
    {
        return str_starts_with($className, 'PHP') || in_array($className, get_declared_classes());
    }
}
