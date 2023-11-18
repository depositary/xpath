<?php

namespace Depositary\Xpath;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

class XPath extends DOMXPath
{
    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $document = new DOMDocument('1.0', 'UTF-8');

        if ($content !== '') {
            $document->loadHTML($content, LIBXML_NOERROR | LIBXML_NOWARNING);
        }

        parent::__construct($document);
    }

    /**
     * @param string $expression
     * @param int $index
     * @return int|null
     * @throws InvalidExpressionException
     * @throws InvalidItemException
     */
    public function getIntValue(string $expression, int $index = 0): ?int
    {
        $value = $this->getStringValue($expression, $index);
        $value = preg_replace('/([^0-9]+)/', '', $value);

        return ($value === '') ? null : (int)$value;
    }

    /**
     * @param string $expression
     * @param int $index
     * @return string
     * @throws InvalidExpressionException
     * @throws InvalidItemException
     */
    public function getStringValue(string $expression, int $index = 0): string
    {
        return trim($this->getNode($expression, $index)->nodeValue);
    }

    /**
     * @param string $expression
     * @param int $index
     * @return DOMNode
     * @throws InvalidExpressionException
     * @throws InvalidItemException
     */
    public function getNode(string $expression, int $index = 0): DOMNode
    {
        $items = $this->getNodes($expression);

        if ($items->length < $index + 1) {
            throw new InvalidItemException(sprintf('No result for expression: %s (item #%d)', $expression, $index));
        }

        return $items->item($index);
    }

    /**
     * @param string $expression
     * @param DOMNode|null $context
     * @return DOMNodeList|DOMNode[]
     * @throws InvalidExpressionException
     */
    public function getNodes(string $expression, ?DOMNode $context = null): DOMNodeList|array
    {
        $nodeList = $this->query($expression, $context);

        if ($nodeList instanceof DOMNodeList) {
            return $nodeList;
        }

        throw new InvalidExpressionException('Invalid expression: ' . $expression);
    }
}
