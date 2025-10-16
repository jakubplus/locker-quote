<?php
declare(strict_types=1);

namespace App\Controller;

use App\Application\LockerQuoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FormController extends AbstractController
{
    #[Route('/', name: 'form_index', methods: ['GET','POST'])]
    public function index(Request $request, LockerQuoteService $service): Response
    {
        $result = null;
        $error  = null;
        $values = [
            'length' => $request->request->get('length', ''),
            'width'  => $request->request->get('width', ''),
            'height' => $request->request->get('height', ''),
        ];

        if ($request->isMethod('POST')) {
            $length = (float) $values['length'];
            $width  = (float) $values['width'];
            $height = (float) $values['height'];

            if ($length > 0 && $width > 0 && $height > 0) {
                $result = $service->quote($length, $width, $height);
            } else {
                $error = 'Wprowadź poprawne wymiary (większe od 0).';
            }
        }

        return $this->render('form/index.html.twig', [
            'values' => $values,
            'result' => $result,
            'error'  => $error,
        ]);
    }
}
