<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeedController extends AbstractController
{
    #[Route('/feeds')]
    public function index(FeedRepository $feedRepository): Response
    {
        $feeds = $feedRepository->getAllFeeds();

        return $this->render('feed/index.html.twig', [
            'feeds' => $feeds,
        ]);
    }
}
