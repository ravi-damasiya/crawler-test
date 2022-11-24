<?php

namespace App\Command;

use App\Message\NewsUpdate;
use App\Repository\NewsRepository;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SyncNewsCommand extends Command
{
    private HttpClientInterface $client;
    private NewsRepository $newsRepository;
    private ParameterBagInterface $parameterBag;

    private const CRAWLER_URL = "https://highload.today/category/novosti";
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        HttpClientInterface $client,
        NewsRepository $newsRepository,
        ParameterBagInterface $parameterBag,
        MessageBusInterface $messageBus
    )
    {
        parent::__construct();
        $this->client = $client;
        $this->newsRepository = $newsRepository;
        $this->parameterBag = $parameterBag;
        $this->messageBus = $messageBus;
    }

    protected static $defaultName = 'app:sync-news';
    protected static $defaultDescription = 'sync news';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->client->request('GET', self::CRAWLER_URL);

        $statusCode = $response->getStatusCode();
        if ($statusCode === Response::HTTP_OK) {
            $crawler = new Crawler($response->getContent());

            $newsData = $crawler->filter('.lenta-item')->each(function (Crawler $node, $i) {
                if ($node->filter('h2')->count()) {
                    $title = $node->filter('h2')->first()->text();
                    $news = $this->newsRepository->findOneBy(['title' => $title]);
                    if (!$news) {
                        $description = $node->filter('p')->last()->text();
                        $image = $node->filter('.lenta-image > img')->attr('data-lazy-src');

                        $path = $this->parameterBag->get('image_directory');
                        $file = file_get_contents($image);
                        $imageName = basename($image);
                        file_put_contents($path.$imageName, $file);

                        return [
                            'title' => $title,
                            'description' => $description,
                            'image' => $imageName,
                        ];
                    }
                    return null;
                }
                return null;
            });

            $newsData = array_filter($newsData, fn($value) => !is_null($value) && $value !== '');
            if (count($newsData) > 0) {
                $this->messageBus->dispatch(new NewsUpdate($newsData));
            }

            $io->success('News Successfully Updated');
        }

        return Command::SUCCESS;
    }
}
