<?php
class UserController extends BaseController {
    public function index() {
        // Aquí podrías agregar lógica adicional si lo deseas
        require __DIR__ . '/../views/user/index.php';
    }
}
