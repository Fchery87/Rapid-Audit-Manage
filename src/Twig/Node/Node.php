<?php

/**
 * This file reproduces the Twig\Node\Node class with PHP 8.x compatible ternary expressions
 * so legacy Symfony tooling can parse templates without upgrading the vendor tree.
 *
 * The implementation derives from Twig (c) Fabien Potencier and Armin Ronacher (MIT License).
 */

namespace Twig\Node;

use InvalidArgumentException;
use IteratorAggregate;
use Countable;
use Twig\Compiler;
use Twig\Source;

class Node implements Countable, IteratorAggregate
{
    protected $nodes;
    protected $attributes;
    protected $lineno;
    protected $tag;

    private $name;
    private $sourceContext;

    /**
     * @param array $nodes
     * @param array $attributes
     * @param int   $lineno
     * @param string|null $tag
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineno = 0, string $tag = null)
    {
        foreach ($nodes as $name => $node) {
            if (!$node instanceof self) {
                $typeDescription = is_object($node)
                    ? get_class($node)
                    : (null === $node ? 'null' : gettype($node));

                throw new InvalidArgumentException(sprintf('Using "%s" for the value of node "%s" of "%s" is not supported. You must pass a \Twig\Node\Node instance.', $typeDescription, $name, get_class($this)));
            }
        }
        $this->nodes = $nodes;
        $this->attributes = $attributes;
        $this->lineno = $lineno;
        $this->tag = $tag;
    }

    public function __toString()
    {
        $attributes = [];
        foreach ($this->attributes as $name => $value) {
            $attributes[] = sprintf('%s: %s', $name, str_replace("\n", '', var_export($value, true)));
        }

        $repr = [get_class($this).'('.implode(', ', $attributes)];

        if (count($this->nodes)) {
            foreach ($this->nodes as $name => $node) {
                $len = strlen($name) + 4;
                $noderepr = [];
                foreach (explode("\n", (string) $node) as $line) {
                    $noderepr[] = str_repeat(' ', $len).$line;
                }

                $repr[] = sprintf('  %s: %s', $name, ltrim(implode("\n", $noderepr)));
            }

            $repr[] = ')';
        } else {
            $repr[0] .= ')';
        }

        return implode("\n", $repr);
    }

    public function compile(Compiler $compiler)
    {
        foreach ($this->nodes as $node) {
            $node->compile($compiler);
        }
    }

    public function getTemplateLine()
    {
        return $this->lineno;
    }

    public function getNodeTag()
    {
        return $this->tag;
    }

    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new \LogicException(sprintf('Attribute "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->attributes[$name];
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    public function hasNode($name)
    {
        return array_key_exists($name, $this->nodes);
    }

    public function getNode($name)
    {
        if (!array_key_exists($name, $this->nodes)) {
            throw new \LogicException(sprintf('Node "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->nodes[$name];
    }

    public function setNode($name, self $node)
    {
        $this->nodes[$name] = $node;
    }

    public function removeNode($name)
    {
        unset($this->nodes[$name]);
    }

    public function count()
    {
        return count($this->nodes);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }

    public function setTemplateName($name)
    {
        $this->name = $name;
        foreach ($this->nodes as $node) {
            $node->setTemplateName($name);
        }
    }

    public function getTemplateName()
    {
        return $this->name;
    }

    public function setSourceContext(Source $source)
    {
        $this->sourceContext = $source;
        foreach ($this->nodes as $node) {
            $node->setSourceContext($source);
        }
    }

    public function getSourceContext()
    {
        return $this->sourceContext;
    }
}
