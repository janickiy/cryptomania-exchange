<?php

namespace Tests\Unit;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ApplicationCodeHealthTest extends TestCase
{
    public function test_every_app_class_and_trait_method_declares_argument_and_return_types(): void
    {
        $failures = [];

        foreach ($this->classAndTraitMethods() as [$path, $method]) {
            $methodName = strtolower($method->name->toString());
            $methodLabel = $path . ':' . $method->getStartLine() . '::' . $method->name->toString();

            if (!in_array($methodName, ['__construct', '__destruct'], true) && $method->returnType === null) {
                $failures[] = $methodLabel . ' has no return type.';
            }

            if ($method->returnType instanceof Node\Identifier && strtolower($method->returnType->toString()) === 'mixed') {
                $failures[] = $methodLabel . ' uses mixed return type.';
            }

            foreach ($method->params as $parameter) {
                if ($parameter->type === null) {
                    $parameterName = $parameter->var instanceof Node\Expr\Variable
                        ? (string) $parameter->var->name
                        : 'unknown';
                    $failures[] = $methodLabel . ' has no type for $' . $parameterName . '.';
                }
            }
        }

        self::assertSame([], $failures);
    }

    public function test_every_controller_method_documents_purpose_and_action(): void
    {
        $failures = [];

        foreach ($this->classAndTraitMethods($this->appPath('Http/Controllers')) as [$path, $method]) {
            $docblock = $method->getDocComment()?->getText() ?? '';

            if (!str_contains($docblock, 'Назначение:') || !str_contains($docblock, 'Действие:')) {
                $failures[] = $path . ':' . $method->getStartLine() . '::' . $method->name->toString();
            }
        }

        self::assertSame([], $failures);
    }

    public function test_every_app_symbol_is_autoloadable_and_reflectable(): void
    {
        $failures = [];

        foreach ($this->declaredSymbols() as [$path, $symbol]) {
            if (
                !class_exists($symbol)
                && !interface_exists($symbol)
                && !trait_exists($symbol)
                && !(function_exists('enum_exists') && enum_exists($symbol))
            ) {
                $failures[] = $path . ' declares ' . $symbol . ', but Composer cannot autoload it.';
                continue;
            }

            try {
                new \ReflectionClass($symbol);
            } catch (\Throwable $exception) {
                $failures[] = $path . ' declares ' . $symbol . ', but reflection failed: ' . $exception->getMessage();
            }
        }

        self::assertSame([], $failures);
    }

    /**
     * @return list<array{0: string, 1: Node\Stmt\ClassMethod}>
     */
    private function classAndTraitMethods(?string $rootPath = null): array
    {
        $methods = [];

        foreach ($this->phpFiles($rootPath ?? $this->appPath()) as $path) {
            $ast = $this->parse($path);
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($path, $methods) extends NodeVisitorAbstract {
                /** @var list<Node\Stmt\Class_|Node\Stmt\Trait_|Node\Stmt\Interface_> */
                private array $ownerStack = [];

                /**
                 * @param list<array{0: string, 1: Node\Stmt\ClassMethod}> $methods
                 */
                public function __construct(
                    private readonly string $path,
                    private array &$methods,
                ) {
                }

                public function enterNode(Node $node): null
                {
                    if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Interface_) {
                        $this->ownerStack[] = $node;
                        return null;
                    }

                    $owner = end($this->ownerStack);
                    if ($node instanceof Node\Stmt\ClassMethod && $owner !== false && !$owner instanceof Node\Stmt\Interface_) {
                        $this->methods[] = [$this->path, $node];
                    }

                    return null;
                }

                public function leaveNode(Node $node): null
                {
                    if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Interface_) {
                        array_pop($this->ownerStack);
                    }

                    return null;
                }
            });
            $traverser->traverse($ast);
        }

        return $methods;
    }

    /**
     * @return list<array{0: string, 1: class-string}>
     */
    private function declaredSymbols(): array
    {
        $symbols = [];

        foreach ($this->phpFiles($this->appPath()) as $path) {
            $namespace = null;

            foreach ($this->parse($path) as $node) {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $namespace = $node->name?->toString();
                    foreach ($node->stmts as $statement) {
                        if ($this->isNamedSymbol($statement)) {
                            $symbols[] = [$path, $namespace . '\\' . $statement->name->toString()];
                        }
                    }
                    continue;
                }

                if ($this->isNamedSymbol($node)) {
                    $symbols[] = [$path, $node->name->toString()];
                }
            }
        }

        return $symbols;
    }

    private function isNamedSymbol(Node $node): bool
    {
        return ($node instanceof Node\Stmt\Class_
                || $node instanceof Node\Stmt\Interface_
                || $node instanceof Node\Stmt\Trait_
                || $node instanceof Node\Stmt\Enum_)
            && $node->name !== null;
    }

    /**
     * @return list<Node\Stmt>
     */
    private function parse(string $path): array
    {
        return (new ParserFactory())
            ->createForHostVersion()
            ->parse((string) file_get_contents($path)) ?? [];
    }

    /**
     * @return list<string>
     */
    private function phpFiles(string $rootPath): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    private function appPath(?string $path = null): string
    {
        $appPath = dirname(__DIR__, 2) . '/app';

        return $path === null ? $appPath : $appPath . '/' . $path;
    }
}
