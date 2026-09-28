<?php

namespace controller;

use JetBrains\PhpStorm\NoReturn;
use model\UserModel;

class ProfileController extends Controller
{
    protected array|null $authorization = ["*"];

    private readonly UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    #[NoReturn]
    protected function index(): void
    {
        $userId = $this->user['id'];
        $entity = $this->userModel->findById($userId);

        $this->json([
            'id' => $entity['id'],
            'username' => $entity['username'],
            'full_name' => $entity['full_name'],
            'role' => $entity['role']
        ]);
    }
}