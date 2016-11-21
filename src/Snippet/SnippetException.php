<?php
namespace PhpSandbox\Snippet;

use Exception;

/**
 * Class SnippetException
 * @package Evaluator
 */
class SnippetException extends Exception
{
    const MAX_NESTING = 1001;
    const WRONG_NAME = 1002;
    const MISSING_EXTENSION = 1003;
    const FILE_NOT_EXISTS = 1004;
    const NO_PERMISSION = 1005;

    /**
     * SnippetException constructor.
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}