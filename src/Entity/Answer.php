<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="answers")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question",inversedBy="answers")
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $value;
    /**
     * @ORM\Column(type="boolean")
     */
    private $correct;

    /**
     * Answer constructor.
     * @param $id
     * @param $user
     * @param $question
     * @param $value
     * @param $correct
     */
    public function __construct($id, $user, $question, $value, $correct)
    {
        $this->id = $id;
        $this->user = $user;
        $this->question = $question;
        $this->value = $value;
        $this->correct = $correct;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question): void
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getCorrect()
    {
        return $this->correct;
    }

    /**
     * @param mixed $correct
     */
    public function setCorrect($correct): void
    {
        $this->correct = $correct;
    }




}
