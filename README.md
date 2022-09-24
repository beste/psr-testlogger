# PSR Test Logger

PSR-3 compliant test logger for developers who like tests and want to check if their application logs messages as they expect.

[![Current version](https://img.shields.io/packagist/v/beste/psr-testlogger.svg?logo=composer)](https://packagist.org/packages/beste/psr-testlogger)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/beste/psr-testlogger)](https://packagist.org/packages/beste/psr-testlogger)
[![Monthly Downloads](https://img.shields.io/packagist/dm/beste/psr-testlogger.svg)](https://packagist.org/packages/beste/psr-testlogger/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/beste/psr-testlogger.svg)](https://packagist.org/packages/beste/psr-testlogger/stats)
[![Tests](https://github.com/beste/psr-testlogger/actions/workflows/tests.yml/badge.svg)](https://github.com/beste/psr-testlogger/actions/workflows/tests.yml)
[![Sponsor](https://img.shields.io/static/v1?logo=GitHub&label=Sponsor&message=%E2%9D%A4&color=ff69b4)](https://github.com/sponsors/jeromegamez)

## Installation

```shell
composer require --dev beste/psr-testlogger
```

## Usage

In your unit tests, inject the `Beste\Psr\Log\TestLogger` class into tested subjects that expect a
`Prs\Log\LoggerInterface`.

The test logger records all log messages and exposes them via the `records` property which is
an instance of `Beste\Psr\Log\Records`.

```php
use Beste\Psr\Log\Record;
use Beste\Psr\Log\TestLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class Subject
{
    public function __construct(
        public readonly LoggerInterface $logger
    ) {}
    
    public function doSomething(): void
    {
        $this->logger->info('Doing something');
        $this->logger->warning('1st problem');
        $this->logger->warning('2nd problem');
        $this->logger->critical('Uh oh!');
    }
}

final class SubjectTest extends \PHPUnit\Framework\TestCase
{
    private TestLogger $logger;
    private Subject $subject;
    
    protected function setUp() : void{
        $this->logger = TestLogger::create();
    }
    
    /** @test */
    public function it_does_something(): void
    {
        $this->subject->doSomething();
        
        self::assertCount(4, $this->logger->records);
        
        self::assertEqualsCanonicalizing(
            [LogLevel::INFO, LogLevel::WARNING, LogLevel::CRITICAL],
            $this->logger->records->levels()
        );
        
        self::assertTrue($this->logger->records->includeMessagesWithLevel('info'));
        self::assertCount(1, $this->logger->records->filteredByLevel('info'));
        self::assertCount(3, $this->logger->records->filteredByLevel('info', 'warning'));
        
        self::assertTrue($this->logger->records->includeMessagesContaining('problem'));
        self::assertCount(2, $this->logger->records->filteredByMessageContaining('problem'));
        
        self::assertTrue($this->logger->records->includeMessagesMatching('/^\d{1,}(st|nd)/i'));
        self::assertCount(2, $this->logger->records->filteredByMessageMatching('/^\d{1,}(st|nd)/i'));
        
        // You can filter by your own criteria. If you discover criteria not natively
        // covered by the test logger, please consider a pull request.
        self::assertTrue($this->logger->records->includeMessagesBy(fn (Record $r) => str_contains($r->level, 'n')));
        self::assertCount(3, $this->logger->records->filteredBy(fn (Record $r) => str_contains($r->level, 'n')));  
    }
}
```

## License

This project is published under the [MIT License](LICENSE).
