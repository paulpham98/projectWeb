<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class TeacherController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    /**
     * @Route("teachers/", name="teacher_index")
     */
    public function teacherIndex()
    {
        $teachers = $this->em->getRepository(Teacher::class)->findAll();
        return $this->render("teacher/index.html.twig", [
            'teachers' => $teachers
        ]);
    }
    /**
     * @Route("/teacher/delete/{id}", name="teacher_delete")
     */
    public function teacherDelete($id)
    {
        $teacher = $this->em->getRepository(Teacher::class)->find($id);
        if ($teacher == null) {
            $this->addFlash("Error", "Teacher delete failed");
        } else {
            $manager = $this->em->getManager();
            $manager->remove($teacher);
            $manager->flush();
            $this->addFlash("Success", "Author delete succeed");
        }
        return $this->redirectToRoute("teacher_index");
    }
    /**
     * @Route("/teacher/detail/{id}", name="teacher_detail")
     */
    public function teacherDetail($id)
    {
        $teacher = $this->em->getRepository(Teacher::class)->find($id);
        if ($teacher == null) {
            $this->addFlash("Error", "Teacher not found");
            return $this->redirectToRoute("teacher_index");
        }
        return $this->render("teacher/detail.html.twig", [
            'teacher' => $teacher
        ]);
    }
    /**
     * @Route("/teacher/add", name="teacher_add")
     */
    public function teacherAdd(Request $request)
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['avatar']->getData();
            if ($file != null) {
                $image = $teacher->getAvatar();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . "." . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('course_image'),
                        $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $teacher->setAvatar($imageName);
            }
            if ($file == null) {
                $imgName = 'avatar.jpg';
                $teacher->setAvatar($imgName);
            }
            $manager = $this->em->getManager();
            $manager->persist($teacher);
            $manager->flush();
            $this->addFlash("Success", "Add teacher succeed !");
            return $this->redirectToRoute("teacher_index");
        }
        return $this->renderForm("teacher/add.html.twig", [
            'form' => $form
        ]);
    }
    /**
     * @Route("/teacher/edit/{id}", name="teacher_edit")
     */
    public function teacherEdit(Request $request, $id)
    {
        $teacher = $this->em->getRepository(Teacher::class)->find($id);
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($teacher);
            $manager->flush();
            $this->addFlash("Success", "Edit teacher succeed!");
            return $this->redirectToRoute("teacher_index");
        }
        return $this->renderForm("teacher/edit.html.twig", [
            'form' => $form
        ]);
    }
    /**
     * @Route("/teacher/sort/asc", name="sort_teacher_id_asc")
     */
    public function sortTeacherByIdAsc(TeacherRepository $repository)
    {
        $teachers = $repository->sortIdAsc();
        return $this->render("teacher/index.html.twig", [
            'teachers' => $teachers
        ]);
    }
    /**
     * @Route("/teacher/sort/desc", name="sort_teacher_id_desc")
     */
    public function sortTeacherByIdDesc(TeacherRepository $repository)
    {
        $teachers = $repository->sortIdDesc();
        return $this->render("teacher/index.html.twig", [
            'teachers' => $teachers
        ]);
    }
    /**
     * @Route("/teacher/search", name="search_teacher_name")
     */
    public function searchTeacherByName (TeacherRepository $repository, Request $request) {
        $name = $request->get("name");
        $teachers = $repository->searchTeacher($name);
        return $this->render("teacher/index.html.twig",
        [
            'teachers' => $teachers
        ]);
    }
}