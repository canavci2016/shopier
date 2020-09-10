<?php

namespace CanAvci\Shopier;

class Person implements GenerateArray
{

    private $id, $name, $surname, $email, $phone;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id ?? uniqid();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
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
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function generateArray(): array
    {
        return [
            'buyer_name' => $this->getName(),
            'buyer_surname' => $this->getSurname(),
            'buyer_email' => $this->getEmail(),
            'buyer_account_age' => 0,
            'buyer_id_nr' => $this->getId(),
            'buyer_phone' => $this->getPhone()
        ];
    }
}