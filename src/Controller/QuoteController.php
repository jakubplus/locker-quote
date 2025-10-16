<?php
declare(strict_types=1);

namespace App\Controller;

use App\Application\LockerQuoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class QuoteController extends AbstractController
{
    #[Route('/api/quote', name: 'api_quote', methods: ['GET','POST'])]
    public function quote(Request $request, LockerQuoteService $service): JsonResponse
    {
        if ($request->isMethod('GET')) {
            // /api/quote?length=60&width=35&height=10
            $length = (float) $request->query->get('length', 0);
            $width  = (float) $request->query->get('width', 0);
            $height = (float) $request->query->get('height', 0);
        } else { // POST
            $data   = json_decode($request->getContent() ?: '{}', true);
            $length = (float) ($data['length'] ?? 0);
            $width  = (float) ($data['width'] ?? 0);
            $height = (float) ($data['height'] ?? 0);
        }

        foreach (['length' => $length, 'width' => $width, 'height' => $height] as $k => $v) {
            if ($v <= 0) {
                return $this->json(['error' => "Missing/invalid dimension: {$k} (positive cm required)."], 422);
            }
        }

        $res = $service->quote($length, $width, $height);

        return $this->json([
            'input' => ['length'=>$length,'width'=>$width,'height'=>$height,'units'=>'cm'],
            'result' => $res,
        ]);
    }
}
