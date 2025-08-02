<?php

namespace DeveloperAnnur\Belajar\PHP\MVC;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase {
    public function testRegex() {
        $path = "/products/P001/categories/gadget";
        
        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        $result = preg_match($pattern, $path, $variable);

        self::assertEquals(1, $result);
        
        var_dump($variable);

        array_shift($variable);
        var_dump($variable);
    }
}
