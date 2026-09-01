<?php

namespace App\Tests;

use App\Feed\FeedTypeDetector;
use App\Validator\ReachableFeed;
use App\Validator\ReachableFeedValidator;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReachableFeedValidatorTest extends ConstraintValidatorTestCase
{
    private HttpClientInterface $httpClient;

    public function testJsonValidate(): void
    {
        $json = file_get_contents(__DIR__ . '/fixtures/feeds/bbc-news.rss.xml');

        $responses = [
            new MockResponse($json)
        ];

        $this->httpClient->setResponseFactory($responses);

        $this->validator->validate('https://news.ycombinator.com/rss', new ReachableFeed());

        $this->assertNoViolation();
    }

    public function testHttpErrorValidate(): void
    {
        $json = file_get_contents(__DIR__ . '/fixtures/feeds/bbc-news.rss.xml');

        $responses = [
            new MockResponse($json, ['http_code' => 404])
        ];

        $this->httpClient->setResponseFactory($responses);

        $this->validator->validate('https://news.ycombinator.com/rss', new ReachableFeed());

        $this->buildViolation('«{{ url }}: не удалось получить валидный фид (%reason%)»')
            ->setParameter('url', 'https://news.ycombinator.com/rss')->setParameter('reason', 'HTTP 404')->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->httpClient = new MockHttpClient();

        return new ReachableFeedValidator(
            $this->httpClient, new FeedTypeDetector()
        );
    }
}
