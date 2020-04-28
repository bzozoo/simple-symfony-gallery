<?php

namespace App\Controller;

use App\Service\ArrayPaginator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GalleryController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function listAction(Request $request)
    {

        // Képfájlok listája
        $images = [];
        foreach($this->getSortedFileList($this->getPath()) as $file) {
            $images[] = $file->getRelativePathname();
        }

        // Lapozó
        $paginator = new ArrayPaginator($request, $images, $this->getParameter('limit'));

        // Template renderelés
        return $this->render(
            'gallery/list.html.twig', [
                'images' => $paginator->getPagedItems(),
                'pagination' => $paginator->getPagination(),
                'rows_per_page' => $this->getParameter('rows_per_page'),
            ]
        );
    }

    /**
     * Képfájlok $path folderben legrissebb elöl
     */
    protected function getSortedFileList(string $path): Finder
    {
        $finder = new Finder();

        $finder->files()->in($path)->sort(
            function ($a, $b) {
                return ($b->getMTime() - $a->getMTime());
            }
        );

        return $finder;
    }

    protected function getPath(): string
    {
        return realpath(__DIR__ . $this->getParameter('gallery_path'));
    }

}
