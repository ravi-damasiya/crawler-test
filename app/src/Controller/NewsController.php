<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/news/", name="news_")
 */
class NewsController extends AbstractController
{
    private NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * @Route("list", name="list")
     */
    public function listAction(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->render("news/list.html.twig", [
            "pagination" => $paginator->paginate($this->newsRepository->findBy([], ['id' => Criteria::DESC]), $request->query->getInt('page', 1), 10)
        ]);
    }

    /**
     * @Route("delete/{id}", name="delete")
     */
    public function deleteAction(News $news): Response
    {
        $this->newsRepository->remove($news, true);
        $this->addFlash("success", "news successfully deleted");

        return $this->redirectToRoute("news_list");
    }
}