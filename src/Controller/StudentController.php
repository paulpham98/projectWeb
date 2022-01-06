<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class StudentController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/students/", name="student_index")
     */
    public function studentIndex()
    {
        $student = $this->em->getRepository(Student::class)->findAll();
        return $this->render("student/index.html.twig", ['student' => $student]);
    }

    /**
     * @Route("/student/detail/{id}", name="student_detail")
     */
    public function studentDetail($id)
    {
        $student = $this->em->getRepository(Student::class)->find($id);
        return $this->render("student/detail.html.twig", ['student' => $student]);
    }

    /**
     * @Route("/student/edit/{id}", name="student_edit" )
     */
    public function editStudent(Request $request, $id)
    {
        $student = $this->em->getRepository(Student::class)->find($id);
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        $site = 'edit';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['avatar']->getData();
            if ($file != null) {
                $image = $student->getAvatar();
                $ranName = uniqid();
                $imgExt = $image->guessExtension();
                $newName = $ranName . "." . $imgExt;
                try {
                    $image->move(
                        $this->getParameter('course_image'),
                        $newName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $student->setAvatar($newName);
            }
            if ($file == null) {
                $newName = 'default-avatar.png';
                $student->setAvatar($newName);
            }

            $manager = $this->em->getManager();
            $manager->persist($student);
            $manager->flush();
            return $this->redirectToRoute("student_index");
        }
        return $this->renderForm("student/add_edit.html.twig", ['form' => $form, 'student' => $student, 'site' => $site]);
    }

    /**
     * @Route("/student/delete/{id}", name="student_delete" )
     */
    public function deleteStudent(Request $request, $id)
    {
        if ($id == 'checkbox') {
            $inputValue = $request->get('checkbox');
            for ($i = 0; $i < count((array)$inputValue); $i++) {
                $iv = $inputValue[$i];
                $student = $this->em->getRepository(Student::class)->find($iv);
                $manager = $this->em->getManager();
                $manager->remove($student);
            }
            $manager->flush();
            $this->addFlash("Success", "Delete student succeed");
        } else {
            $student = $this->em->getRepository(Student::class)->find($id);
            if ($student == null) {
                $this->addFlash("Error", "Delete student failed");
            } else {
                $manager = $this->em->getManager();
                $manager->remove($student);
                $manager->flush();
                $this->addFlash("Success", "Delete student succeed");
            }
        }
        return $this->redirectToRoute("student_index");
    }

    /**
     * @Route("/student/add/", name="student_add" )
     */
    public function addStudent(Request $request)
    {
        $student = new student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        $site = 'add';

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['avatar']->getData();
            if ($file != null) {
                $image = $student->getAvatar();
                $ranName = uniqid();
                $imgExt = $image->guessExtension();
                $newName = $ranName . "." . $imgExt;
                try {
                    $image->move(
                        $this->getParameter('course_image'),
                        $newName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $student->setAvatar($newName);
            }
            if ($file == null) {
                $newName = 'default-avatar.png';
                $student->setAvatar($newName);
            }
            $manager = $this->em->getManager();
            $manager->persist($student);
            $manager->flush();
            return $this->redirectToRoute("student_index");
        }
        return $this->renderForm("student/add_edit.html.twig", ['form' => $form, 'site' => $site]);
    }


    /**
     * @Route("/student/search", name="student_search" )
     */
    public function searchCourseFunction(StudentRepository $repo, Request $request)
    {
        $sort = 'asc';
        $search = $request->get('searchValue');
        $student = $repo->searchStudent($search);
        return $this->render("student/index.html.twig", ['student' => $student, 'sort' => $sort]);
    }
}
