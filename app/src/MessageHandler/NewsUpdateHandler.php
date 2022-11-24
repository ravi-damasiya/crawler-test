<?php

namespace App\MessageHandler;

use App\Entity\News;
use App\Message\NewsUpdate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NewsUpdateHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(NewsUpdate $newsUpdate)
    {
        foreach ($newsUpdate->getNewsArray() as $item) {
            $news = (new News())
                ->setTitle($item['title'])
                ->setShortDescription($item['description'])
                ->setImage($item['image'])
                ->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($news);
        }

        $this->entityManager->flush();
    }
}