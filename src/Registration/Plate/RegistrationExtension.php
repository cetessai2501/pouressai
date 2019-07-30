<?php
namespace App\Registration\Plate;

use Projek\Slim\Plates;

class RegistrationExtension extends Plates
{
    /**
     * @var ViewInterface
     */
    private $view;
    public function __construct(Plates $view)
    {
        $this->view = $view;
    }
    public function getFunctions()
    {
        return [
            new \Plate_SimpleFunction('registration_form', [$this, 'renderForm'], ['is_safe' => ['html']])
        ];
    }
    public function renderForm()
    {
        return $this->view->render('@registration/form');
    }
}
