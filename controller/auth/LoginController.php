<?php

namespace controller\auth;

use controller\Controller;
use core\Request;
use JetBrains\PhpStorm\NoReturn;
use JwtHelper;
use model\UserModel;

class LoginController extends Controller
{
    private readonly UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    #[NoReturn]
    protected function store(Request $request): void
    {
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $entity = $this->userModel->findByUsername($request->get("username"));
        if (!$entity) {
            $this->json([
                "error" => "Username doesn't not exist",
            ], 400);
        }

        if (!password_verify($request->get('password'), $entity['password'])) {
            $this->json([
                "error" => "Wrong password",
            ], 400);
        }

        $token = JwtHelper::generateToken([
            'id' => $entity['id'],
            'username' => $entity['username'],
            'role' => $entity['role']
        ]);

        $this->json([
            'id' => $entity['id'],
            'full_name' => $entity['full_name'],
            'username' => $entity['username'],
            'role' => $entity['role'],
            "token" => $token,
        ]);
    }
}