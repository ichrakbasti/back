<?php

namespace App\Controller;

use App\Repository\UserProductivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserProductivityController extends AbstractController
{
    private UserProductivityRepository $userProductivityRepository;

    public function __construct(UserProductivityRepository $userProductivityRepository)
    {
        $this->userProductivityRepository = $userProductivityRepository;
    }

    #[Route('/user/productivity', name: 'app_user_productivity')]
    public function index(): Response
    {
        return $this->render('user_productivity/index.html.twig', [
            'controller_name' => 'UserProductivityController',
        ]);
    }

    #[Route('/user/productivity/all', name: 'api_user_productivity_all', methods: ['GET'])]
    public function getAllUserProductivity(): JsonResponse
    {
        // Récupérer les données de productivité des utilisateurs
        $productivityData = $this->userProductivityRepository->findAll();

        // Transformer les données en un tableau JSON formaté
        $data = [];
        foreach ($productivityData as $productivity) {
            $data[] = [
                'user_id' => $productivity->getUser()->getId(),
                'user_name' => $productivity->getUser()->getName(),
                'estimated_online_time' => $productivity->getEstimatedOnlineTime(),
                'occupancy_rate' => $productivity->getOccupancyRate(),
                'average_survey_score' => $productivity->getAverageSurveyScore(),
                'survey_count' => $productivity->getSurveyCount(),
            ];
        }

        return new JsonResponse($data);
    }
}
