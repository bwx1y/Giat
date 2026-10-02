<?php

namespace controller;

use JetBrains\PhpStorm\NoReturn;
use model\UserModel;
use core\Request;

class UserController extends Controller
{
    private readonly UserModel $userModel;

    public function __construct()
    {
        $this ->userModel = new UserModel();
    }
    #[NoReturn]
    protected function index():void
    {
        $users = $this->userModel->findAll();
        $this->json($users);
    }
    #[NoReturn]
    protected function show(string $id): void
    {
        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }
        $this->json($user);
    }
    #[NoReturn]
    protected function store(Request $request): void
    {
        $request->validate([
            'full_name' => 'required',
            'username' => 'required',
            'password' => 'required|min:6',
        ]);
        $data = [
            'full_name' => $request ->get('full_name'),
            'username' => $request->get('username'),
            'password' => password_hash($request->get('password'), PASSWORD_DEFAULT),
            'role' => $request ->get('role') ?? 'Member',
            'active' => true,
        ];
        $newUser = $this->userModel->create($data);

        $this->json($newUser, 201);
    }
    #[NoReturn]
    protected function update(Request $request, string $id): void
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }
        $request -> validate([
            'full_name' => 'required',
            'username' => 'required',
        ]);
        $data = [
            'full_name' => $request->get('full_name'),
            'username' => $request->get('username'),
        ];
        if ($request->get('password') !== null && $request ->get('password') !== '') {
            $data['password'] = password_hash($request->get('password'), PASSWORD_DEFAULT);
        }
        if ($request->get('role') !== null) {
            $data['role'] = $request->get('role');
        }
        $updatedUser = $this->userModel->update($id, $data);

        $this->json($updatedUser);
    }
    #[NoReturn]
    protected function destroy(string $id): void
    {
        $user = $this->userModel->findById($id);

        if(!$user) {
            $this->json(['error' => 'User not found'], 404);
        }
        $this->userModel->delete($id);

        $this->json(['message' => 'User deleted successfully']);
    }
}