<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\throwException;

class RoomController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    /**
     * @Route("/rooms", name="room_index")
     */
    public function roomIndex()
    {
        $rooms = $this->em->getRepository(Room::class)->findAll();
        return $this->render("room/index.html.twig", [
            'rooms' => $rooms
        ]);
    }
    /**
     * @Route("/room/detail/{id}", name="room_detail")
     */
    public function roomDetail($id)
    {
        $room = $this->em->getRepository(Room::class)->find($id);
        if ($room == null) {
            $this->addFlash("Error", "Room not found");
            return $this->redirectToRoute("room_index");
        }
        return $this->render("room/detail.html.twig", [
            "room" => $room
        ]);
    }
    /**
     * @Route("/room/delete/{id}", name="room_delete")
     */
    public function roomDelete($id)
    {
        $room = $this->em->getRepository(Room::class)->find($id);
        if ($room == null) {
            $this->addFlash("Error", "Delete room failed");
        } else {
            $manager = $this->em->getManager();
            $manager->remove($room);
            $manager->flush();
            $this->addFlash("Success", "Delete room succeed");
        }
        return $this->redirectToRoute("room_index");
    }
    /**
     * @Route("/rooms/add", name="room_add")
     */
    public function roomAdd(Request $request)
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['avatar']->getData();
            if ($file != null) {
                $image = $room->getAvatar();
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
                $room->setAvatar($imageName);
            }
            if ($file == null) {
                $imgName = 'room.jpg';
                $room->setAvatar($imgName);
            }
            $manager = $this->em->getManager();
            $manager->persist($room);
            $manager->flush();
            return $this->redirectToRoute("room_index");
        }
        return $this->renderForm("room/add.html.twig", [
            'form' => $form
        ]);
    }
    /**
     * @Route("/room/edit/{id}", name="room_edit")
     */
    public function roomEdit(Request $request, $id)
    {
        $room = $this->em->getRepository(Room::class)->find($id);
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['avatar']->getData();
            if ($file != null) {
                $image = $room->getAvatar();
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
                $room->setAvatar($imageName);
            }
            if ($file == null) {
                $imgName = 'room.jpg';
                $room->setAvatar($imgName);
            }
            $manager = $this->em->getManager();
            $manager->persist($room);
            $manager->flush();
            $this->addFlash("Success", "Edit room succeed !");
            return $this->redirectToRoute("room_index");
        }
        return $this->renderForm("room/edit.html.twig", [
            'form' => $form
        ]);
    }
    /**
     * @Route("/room/sort/asc", name="sort_room_id_asc")
     */
    public function sortRoomByIdAsc(RoomRepository $repository)
    {
        $rooms = $repository->sortIdAsc();
        return $this->render("room/index.html.twig", [
            'rooms' => $rooms
        ]);
    }
    /**
     * @Route("/room/sort/desc", name="sort_room_id_desc")
     */
    public function sortRoomByIdDesc(RoomRepository $repository)
    {
        $rooms = $repository->sortIdDesc();
        return $this->render("room/index.html.twig", [
            'rooms' => $rooms
        ]);
    }
    /**
     * @Route("/room/search", name="search_room_name")
     */
    public function searchRoomByName (RoomRepository $repository, Request $request) {
        $name = $request->get("name");
        $rooms = $repository->searchRoom($name);
        return $this->render("room/index.html.twig",
        [
            'rooms' => $rooms
        ]);
    }
}
