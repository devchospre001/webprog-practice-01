<?php
require_once __DIR__ . "/../dao/ExamDao.php";

class ExamService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new ExamDao();
    }

    public function login($data)
    {
        if (empty($data["email"]) || empty($data["password"])) {
            throw new InvalidArgumentException("Email and password are required.");
        }

        return $this->dao->login($data);
    }

    public function film_performance_report()
    {
        return $this->dao->film_performance_report();
    }

    public function delete_film($film_id)
    {
        if ($film_id <= 0) {
            throw new InvalidArgumentException("Invalid film ID.");
        }

        return $this->dao->delete_film($film_id);
    }

    public function edit_film($film_id, $data)
    {
        if ($film_id <= 0) {
            throw new InvalidArgumentException("Invalid film ID.");
        }

        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException("No data provided to update.");
        }

        return $this->dao->edit_film($film_id, $data);
    }

    public function get_customers_report()
    {
        return $this->dao->get_customers_report();
    }

    public function get_customer_rental_details($customer_id)
    {
        if ($customer_id <= 0) {
            throw new InvalidArgumentException('Invalid customer ID.');
        }

        return $this->dao->get_customer_rental_details($customer_id);
    }
}
