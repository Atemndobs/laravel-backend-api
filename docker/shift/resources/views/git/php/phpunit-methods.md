Set return type of base TestCase methods

From the [PHPUnit 8 release notes][1], the `TestCase` methods below now declare a `void` return type:
                                       
- `setUpBeforeClass()`
- `setUp()`
- `assertPreConditions()`
- `assertPostConditions()`
- `tearDown()`
- `tearDownAfterClass()`
- `onNotSuccessfulTest()`

[1]: https://phpunit.de/announcements/phpunit-8.html
