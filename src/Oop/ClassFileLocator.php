<?php

namespace Nip\Utility\Oop;

use SplFileInfo;

/**
 * @inspiration https://github.com/laminas/laminas-file/blob/2.10.x/src/ClassFileLocator.php
 * @internal
 */
class ClassFileLocator
{
    /**
     * @param   SplFileInfo  $file
     *
     * @return array|null
     */
    public static function classes(SplFileInfo $file)
    {
        // If we somehow have something other than an SplFileInfo object, just
        // return false
        if (!$file instanceof SplFileInfo) {
            return null;
        }

        // If we have a directory, it's not a file, so return false
        if (!$file->isFile()) {
            return null;
        }

        // If not a PHP file, skip
        if ($file->getBasename('.php') == $file->getBasename()) {
            return null;
        }

        $contents              = file_get_contents($file->getRealPath());
        $tokens                = token_get_all($contents);
        $count                 = count($tokens);
        $inFunctionDeclaration = false;
        $saveNamespace         = false;
        $classes               = [];

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            // single character token found; skip
            if (!is_array($token)) {
                // If we were in a function declaration, and we encounter an
                // opening paren, reset the $inFunctionDeclaration flag.
                if ('(' === $token) {
                    $inFunctionDeclaration = false;
                }

                $i++;
                continue;
            }

            switch ($token[0]) {
                case T_NAMESPACE:
                    // Namespace found; grab it for later
                    $namespace = '';
                    for ($i++; $i < $count; $i++) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            if (';' === $token) {
                                $saveNamespace = false;
                                break;
                            }
                            if ('{' === $token) {
                                $saveNamespace = true;
                                break;
                            }
                            continue;
                        }
                        list($type, $content) = $token;
                        $types = [T_STRING, T_NS_SEPARATOR];
                        if (PHP_VERSION_ID >= 80000) {
                            $types = array_merge($types, [T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED]);
                        }
                        if (in_array($type, $types, true)) {
                            $namespace .= $content;
                        }
                    }
                    if ($saveNamespace) {
                        $savedNamespace = $namespace;
                    }
                    break;
                case T_FUNCTION:
                    // `use function` should not enter function context
                    if ($i < 2 || !is_array($tokens[$i - 2]) || $tokens[$i - 2][0] !== T_USE) {
                        $inFunctionDeclaration = true;
                    }
                    break;
                case T_TRAIT:
                case T_CLASS:
                    // ignore T_CLASS after T_DOUBLE_COLON to allow PHP >=5.5 FQCN scalar resolution
                    if ($i > 0 && is_array($tokens[$i - 1]) && $tokens[$i - 1][0] === T_DOUBLE_COLON) {
                        break;
                    }

                    // Ignore if we are within a function declaration;
                    // functions are allowed to be named after keywords
                    // such as class, interface, and trait.
                    if ($inFunctionDeclaration) {
                        break;
                    }

                    // ignore anonymous classes on PHP 7.1 and greater
                    if ($i >= 2
                        && \is_array($tokens[$i - 1])
                        && T_WHITESPACE === $tokens[$i - 1][0]
                        && \is_array($tokens[$i - 2])
                        && T_NEW === $tokens[$i - 2][0]
                    ) {
                        break;
                    }

                // no break
                case T_INTERFACE:
                    // Abstract class, class, interface or trait found

                    // Ignore if we are within a function declaration;
                    // functions are allowed to be named after keywords
                    // such as class, interface, and trait.
                    if ($inFunctionDeclaration) {
                        break;
                    }

                    // Get the classname
                    for ($i++; $i < $count; $i++) {
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        if (T_STRING == $type) {
                            // If a classname was found, set it in the object, and
                            // return boolean true (found)
                            if (!isset($namespace) || null === $namespace) {
                                if ($saveNamespace) {
                                    $namespace = $savedNamespace;
                                } else {
                                    $namespace = null;
                                }
                            }
                            $class     = (null === $namespace) ? $content : $namespace . '\\' . $content;
                            $classes[] = $class;
                            if ($namespace) {
//                                $file->addNamespace($namespace);
                            }
                            $namespace = null;
                            break;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        return $classes;
    }
}