<?php


namespace App\Controller;


use App\BotmanService;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BotmanController
{
    /**
     * @Route(path="/botman", name="botman_index")
     * @return Response
     */
    public function indexAction(BotmanService $botmanService, Request $request)
    {
        $request->request->add($request->query->all());
        $botmanService->handleRequest($request);
        return new Response('', 200);
    }

}