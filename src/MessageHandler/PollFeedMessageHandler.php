<?php

namespace App\MessageHandler;

use App\Dto\ParsedArticle;
use App\Dto\ParsedFeed;
use App\Entity\Article;
use App\Entity\Feed;
use App\Event\ArticleImported;
use App\Exceptions\UnsupportedFeedException;
use App\Feed\FeedTypeDetector;
use App\Feed\Parser\FeedParserInterface;
use App\Feed\Parser\FeedParserRegistry;
use App\Message\PollFeedMessage;
use App\Repository\ArticleRepository;
use App\Repository\FeedRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final readonly class PollFeedMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface    $httpClient,
        private FeedRepository         $feedRepository,
        private ArticleRepository      $articleRepository,
        private FeedTypeDetector       $feedTypeDetector,

        // Первый способ получить парсер автоматически.
        #[AutowireLocator('app.feed_parser')]
        private ServiceLocator         $feedParsersLocator,

        private FeedParserRegistry     $feedParsersRegistry,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger,
    )
    {

    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws UnsupportedFeedException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function __invoke(PollFeedMessage $message): void
    {
        try {
            $feed = $this->feedRepository->find($message->feedId);

            if (!$feed instanceof Feed) {
                return;
            }

            $url = $feed->getUrl();
            $response = $this->httpClient->request(Request::METHOD_GET, $url);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                // TODO logging
            }

            $body = $response->getContent();

            $feedType = $this->feedTypeDetector->detect($body, $response->getHeaders()['content-type'][0] ?? null);

            /** @var FeedParserInterface $feedParser */
            $feedParser = $this->feedParsersLocator->get($feedType->value);

            // Бессмысленно, просто для памяти, что е сть еще и такой способ.
            /** @var FeedParserInterface $feedParser */
            $feedParser = $this->feedParsersRegistry->get($feedType->value);

            $parsedFeed = $feedParser->parse($body);

            list($created, $skipped, $noGuid) = $this->handleFeed($parsedFeed, $feed);
            $row[] = [$url, $created, $skipped];

            // do something with your message
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param ParsedFeed $parsedFeed
     * @param Feed $feed
     * @return int[]
     */
    public function handleFeed(ParsedFeed $parsedFeed, Feed $feed): array
    {
        $allKeys = [];

        /** @var ParsedArticle $feedItem */
        foreach ($parsedFeed->articles as $feedItem) {
            $key = $this->getKey($feedItem);

            if ($key === null) {
                continue;
            }
            $allKeys[] = $key;
        }

        $existsGuids = $this->articleRepository->findExistingGuids($feed, $allKeys);

        $existsGuids = array_flip($existsGuids);

        $skipped = $created = $noGuid = 0;

        $articles = [];

        /** @var ParsedArticle $item */
        foreach ($parsedFeed->articles as $item) {
            $guid = $item->guid;
            $link = $item->url;

            $key = $this->getKey($item);
            if ($key === null) {
                continue;
            }
            if ($guid === null && $link !== null) {
                $noGuid++;
            }

            if (isset($existsGuids[$key])) {
                $skipped++;
                continue;
            }

            $article = new Article();
            $article->setFeed($feed);
            $article->setGuid($key);
            $article->setUrl($item->url);
            $article->setTitle((string)$item->title);
            $article->setSummary(isset($item->summary) ? (string)$item->summary : null);
            $article->setPublishedAt($item->publishedAt);

            $this->entityManager->persist($article);

            $articles[] = $article;

            $created++;
        }

        $feed->setLastPolledAt(new DateTimeImmutable());
        $this->entityManager->flush();

        foreach ($articles as $article) {
            $this->dispatcher->dispatch(new ArticleImported($article));
        }

        return array($created, $skipped, $noGuid);
    }

    /**
     * @param ParsedArticle $item
     * @return string|null
     */
    public function getKey(ParsedArticle $item): ?string
    {
        $guid = isset($item->guid) ? (string)$item->guid : null;
        $link = isset($item->url) ? (string)$item->url : null;
        return $guid ?? $link ?? null;
    }
}
