<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Todos;

class TodosController extends AbstractController
{
    /**
     * Отобразить задачи
     */
    public function showTodos()
    {
        // Смотрим какие задачи надо показать, если передано ?all=1, значит показать и выполненные
        if (isset($_GET['all']) && $_GET['all'] == '1') {
            // Получаем массив с задачами
            $todos = $this->getDoctrine()
                            ->getRepository(Todos::class)
                            ->findAll();
        } else {
            // Иначе показать только не выполненные
            // Получаем массив с задачами с условием 'completed' == '0'
            $todos = $this->getDoctrine()
                            ->getRepository(Todos::class)
                            ->findBy(['completed' => '0']);
        }

        // Рендерим вьюху и передаем данные
        return $this->render('showTodos.html.twig', ['todos' => $todos, 'all' => isset($_GET['all']) && $_GET['all'] == '1']);
    }

    /**
     * Изменить состояние задачи
     */
    public function changeTask()
    {
        // Получаем доктрин-менеджер
        $em = $this->getDoctrine()->getManager();
        // Получаем модель с нужным id
        $todo = $em->getRepository(Todos::class)->find($_GET['id']);

        // Если модель не найдена
        if (!$todo) {
            // Выкидываем ошибку
            throw $this->createNotFoundException(
                'No todo found for id ' . $_GET['id']
            );
        }

        // Назначем свойству модели 'Completed' переданное значение
        $todo->setCompleted($_GET['change']);
        // Записываем изменения
        $em->flush();

        // Редиректим на ту же страницу с которой пришли
        return $this->redirect($_GET['all'] ? '/?all=1' : '/');
    }
}
