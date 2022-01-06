<?php

namespace App\Controller;

use App\Entity\CourseDetail;
use App\Form\CourseDetailType;
use App\Repository\CourseDetailRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class CourseDetailController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    /**
     * @Route("/courseDetail/api", methods={"GET"}, name="courseDetail_api")
     */
    public function courseDetailAPI(): JsonResponse
    {
        $courses = $this->em->getRepository(CourseDetail::class)->findAll();
        return $this->json(['courses' => $courses], 200);
    }

    /**
     * @Route("/courseDetail", name="courseDetail_index")
     */

    public function courseDetailIndex()
    {
        $courses = $this->em->getRepository(CourseDetail::class)->findAll();
        return $this->render(
            "course_detail/index.html.twig",
            [
                'courses' => $courses
            ]
        );
    }

    /**
     * @Route("/courseDetail/delete/{id}", name="courseDetail_delete")
     */
    public function courseDetailDelete($id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        if ($courses == null) {
            $this->addFlash("Error", "Delete course failed");
        } else {
            $manager = $this->em->getManager();
            $manager->remove($courses);
            $manager->flush();
            $this->addFlash("Success", "Delete course succeed");
        }
        return $this->redirectToRoute("courseDetail_index");
    }

    /**
     * @Route("/courseDetail/detail/{id}", name="courseDetail_detail")
     */
    public function categoryDetail($id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        if ($courses == null) {
            $this->addFlash("Error", "Course Detail not found");
            return $this->redirectToRoute("courseDetail_index");
        }
        return $this->render(
            "course_detail/detail.html.twig",
            [
                "courses" => $courses
            ]
        );
    }

    /**
     * @Route("/courseDetail/add", name="courseDetail_add")
     */
    public function courseAdd(Request $request)
    {
        $courses = new CourseDetail();
        $form = $this->createForm(CourseDetailType::class, $courses);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //code upload và xử lý tên ảnh
            //B1: lấy tên ảnh từ file upload
            $image = $courses->getPhoto();
            /*B2: đặt tên mới cho file ảnh 
            => đảm bảo tên ảnh là duy nhất */
            $imgName = uniqid(); //unique id
            //B3: lấy đuôi ảnh (image extension)
            $imgExtension = $image->guessExtension();
            //Note: cần sửa lại code getter/setter của Book Entity
            //B4: nối tên mới và đuôi thành tên file ảnh hoàn thiện
            $imageName = $imgName . "." . $imgExtension;
            //B5: copy ảnh vào thư mục chỉ định
            try {
                $image->move(
                    $this->getParameter('course_image'),
                    $imageName
                    /* Note: cần khai báo đường dẫn thư mục chứa ảnh
                  ở file config/services.yaml */
                );
            } catch (FileException $e) {
                throwException($e);
            }
            //B6: lưu tên ảnh vào DB
            $courses->setPhoto($imageName);

            //add dữ liệu vào DB
            $manager = $this->em->getManager();
            $manager->persist($courses);
            $manager->flush();

            //hiển thị thông báo và redirect về book index
            $this->addFlash("Success", "Add Course succeed !");
            return $this->redirectToRoute("courseDetail_index");
        }

        return $this->renderForm(
            "course_detail/add.html.twig",
            [
                'form' => $form
            ]
        );
    }
    /**
     * @Route("/courseDetail/edit/{id}", name="courseDetail_edit")
     */
    public function courseEdit(Request $request, $id)
    {
        $courses = $this->em->getRepository(CourseDetail::class)->find($id);
        $form = $this->createForm(CourseDetailType::class, $courses);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['photo']->getData();

            if ($file != null) {

                $image = $courses->getPhoto();

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

                $courses->setPhoto($imageName);
            }


            $manager = $this->em->getManager();
            $manager->persist($courses);
            $manager->flush();
            $this->addFlash("Success", "edit Course succeed !");
            return $this->redirectToRoute("courseDetail_index");
        }

        return $this->renderForm(
            "course_detail/edit.html.twig",
            [
                'form' => $form
            ]
        );
    }

    /**
     * @Route("/courseDetail/search", name="search_course_name")
     */

    public function searchcategoryByName(CourseDetailRepository $repository, Request $request)
    {
        $name = $request->get("name");
        $courses = $repository->searchCourseDetail($name);
        return $this->render(
            "course_detail/index.html.twig",
            [
                'courses' => $courses
            ]
        );
    }
}
