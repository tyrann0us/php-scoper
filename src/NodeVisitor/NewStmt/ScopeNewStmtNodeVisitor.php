<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\NodeVisitor\NewStmt;

use Humbug\PhpScoper\NodeVisitor\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

final class ScopeNewStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $namespaceStatements;
    private $useStatements;
    private $whitelist;

    public function __construct(
        string $prefix,
        NamespaceStmtCollection $namespaceStatements,
        UseStmtCollection $useStatements,
        array $whitelist
    ) {
        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Name) || false === $node->hasAttribute('parent') || 1 === count($node->parts)) {
            return $node;
        }

        $parentNode = $node->getAttribute('parent');

        if (false === ($parentNode instanceof New_)) {
            return $node;
        }

        if (1 === count($node->parts)) {
            //TODO
            $x = '';
        }

        $useStatement = $this->useStatements->findStatementForName($node->getFirst());

        if (null === $useStatement) {
            if (0 === count($this->namespaceStatements)) {
                return $node;
            }

            $namespaceStatement = $this->namespaceStatements->getNamespaceName();

            $newNode = FullyQualified::concat($namespaceStatement, $node, $node->getAttributes());
        } else {
            $newNode = FullyQualified::concat($useStatement, $node->slice(1), $node->getAttributes());
        }

        $newNode->setAttribute('phpscoper_ignore', true);

        if (in_array((string) $newNode, $this->whitelist)) {
            return $newNode;
        }

        return $node;
    }
}