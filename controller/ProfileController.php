<?php

namespace controller;

use core\Request;
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

        if (!$entity) {
            $this->json(['error' => "Missing authorization header"], 401);
        }

        $this->json([
            'id' => $entity['id'],
            'username' => $entity['username'],
            'full_name' => $entity['full_name'],
            'role' => $entity['role']
        ]);
    }

    #[NoReturn]
    protected function store(Request $request): void
    {
        $request->validate([
            'full_name' => 'required',
            'username' => 'required',
        ]);

        $userId = $this->user['id'];
        $entity = $this->userModel->findById($userId);

        if (!$entity) {
            $this->json(['error' => "Missing authorization header"], 401);
        }

        $data = [
            'full_name' => $request->get('full_name'),
            'username' => $request->get('username'),
        ];
        if ($request->get('password') !== null) {
            $data['password'] = password_hash($request->get('password'), PASSWORD_DEFAULT);
        }

        $entity = $this->userModel->update($userId, $data);

        $this->json([
            'id' => $entity['id'],
            'username' => $entity['username'],
            'full_name' => $entity['full_name'],
            'role' => $entity['role']
        ]);
    }
}