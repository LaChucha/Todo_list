<?php

namespace TodoListBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use TodoListBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{

    /**
     * @Route("/edit_task", name="edit_task")
     * @Template()
     */
    public function editTaskAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $taskId = $request->request->get('taskId');
        $data = array();
        $task = $this->getDoctrine()->getRepository('TodoListBundle:Task')->findOneBy(array('id' => $taskId));
        $task->setStatus('Completed');
        $data['id'] = $task->getId();
        $data['title'] = $task->getTitle();
        $em->persist($task);
        $em->flush();

        return new JsonResponse($data,Response::HTTP_ACCEPTED);
    }
}
