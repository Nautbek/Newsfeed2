<?php

namespace App\Validator;

use App\Feed\FeedTypeDetector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReachableFeedValidator extends ConstraintValidator
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly FeedTypeDetector $feedTypeDetector,
    )
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReachableFeed) {
            throw new UnexpectedTypeException($constraint, ReachableFeed::class);
        }

        try {
            $response = $this->httpClient->request(
                Request::METHOD_GET, $value
            );

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $this->context->buildViolation($constraint->message)->setParameter('url', $value)->setParameter('reason', 'HTTP ' . $response->getStatusCode())->addViolation();
                return;
            }

            $this->feedTypeDetector->detect($response->getContent(), $response->getHeaders()['content-type'][0] ?? null);
        } catch (\Exception $exception) {
            $this->context->buildViolation($constraint->message)->setParameter('url', $value)->setParameter('reason', $exception->getMessage())->addViolation();
        }
    }
}
