<?php
namespace App\Admin;

use App\Controller;

class AdminController extends Controller
{
    public function index(AdminWidgets $adminWidgets)
    {
        return $this->render();
    }
}
