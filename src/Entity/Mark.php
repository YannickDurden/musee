<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MarkRepository")
 */
class Mark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", name="coordinate_x", precision=6, scale=2)
     */
    private $coordinateX;

    /**
     * @ORM\Column(type="decimal", name="coordinate_y", precision=6, scale=2)
     */
    private $coordinateY;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="mark")
     */
    private $medias;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Route", mappedBy="marks")
     */
    private $routes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="mark")
     */
    private $questions;

    /**
     * Mark constructor.
     * @param $id
     * @param $name
     * @param $coordinateX
     * @param $coordinateY
     * @param $image
     * @param $medias
     * @param $description
     * @param $routes
     * @param $questions
     */
    public function __construct($id, $name, $coordinateX, $coordinateY, $image, $medias, $description, $routes, $questions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->coordinateX = $coordinateX;
        $this->coordinateY = $coordinateY;
        $this->image = $image;
        $this->medias = $medias;
        $this->description = $description;
        $this->routes = $routes;
        $this->questions = $questions;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCoordinateX()
    {
        return $this->coordinateX;
    }

    /**
     * @param mixed $coordinateX
     */
    public function setCoordinateX($coordinateX): void
    {
        $this->coordinateX = $coordinateX;
    }

    /**
     * @return mixed
     */
    public function getCoordinateY()
    {
        return $this->coordinateY;
    }

    /**
     * @param mixed $coordinateY
     */
    public function setCoordinateY($coordinateY): void
    {
        $this->coordinateY = $coordinateY;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param mixed $medias
     */
    public function setMedias($medias): void
    {
        $this->medias = $medias;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param mixed $routes
     */
    public function setRoutes($routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestions($questions): void
    {
        $this->questions = $questions;
    }



}
