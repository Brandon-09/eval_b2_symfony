<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    public function index(): Response
    {
        return $this->render('validate/index.html.twig');
    }
}
