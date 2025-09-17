<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$service = Flight::examService();
$jwt_secret = "hello";

Flight::route('GET /test', function () {
    Flight::json(["message" => "Routes are working!"]);
});

Flight::route('POST /login', function () use ($service, $jwt_secret) {
    /** TODO
     * This endpoint is used to login user to system
     * you can use email: demo.user@gmail.com and password: 123 (password is stored within db as plain - 123) to login
     * Output should be array containing success message, JWT, and user object
     * This endpoint should return output in JSON format
     * 5 points
     */
    $data = Flight::request()->data->getData();

    try {
        $user = $service->login($data);

        if (!$user) {
            Flight::json(["error" => "Invalid email or password"], 400);
            return;
        }

        $payload = [
            "sub" => $user["id"],
            "email" => $user["email"],
            "iat" => time(),
            "exp" => time() + 3600
        ];

        $jwt = JWT::encode($payload, $jwt_secret, 'HS256');

        Flight::json([
            "message" => "Login successful",
            "token" => $jwt,
            "user" => $user
        ]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

Flight::route('GET /film/performance', function () use ($service) {
    /** TODO
     * This endpoint returns performance report for every film category.
     * It should return array of all categories where every element
     * in array should have following properties
     *   `id` -> id of category
     *   `name` -> category name
     *   `total` -> total number of movies that belong to that category
     * This endpoint should return output in JSON format
     * 10 points
     */
    try {
        $data = $service->film_performance_report();
        Flight::json($data);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

Flight::route('DELETE /film/delete/@film_id', function ($film_id) use ($service) {
    /** TODO
     * This endpoint should delete the film from database with provided id.
     * This endpoint should return output in JSON format that contains only 
     * `message` property that indicates that process went successfully.
     * 5 points
     */
    try {
        $service->delete_film($film_id);
        Flight::json(["message" => "Film deleted successfully"]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

Flight::route('PUT /film/edit/@film_id', function ($film_id) use ($service) {
    /** TODO
     * This endpoint should save edited film to the database.
     * The data that will come from the form has following properties
     *   `title` -> title of the film
     *   `description` -> description of the film
     *   `release_year` -> release_year of the film
     * This endpoint should return the edited customer in JSON format
     * 10 points
     */
    $data = Flight::request()->data->getData();
    try {
        $service->edit_film($film_id, $data);
        Flight::json(["message" => "Film updated successfully"]);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

Flight::route('GET /customers/report', function () use ($service) {
    /** TODO
     * This endpoint should return the report for every customer in the database.
     * For every customer we need the amount of money earned from customer rentals. 
     * The data should be summarized in order to get accurate report. 
     * Every item returned should have following properties:
     *   `details` -> the html code needed on the frontend. Refer to `customers.html` page
     *   `customer_full name` -> first and last name of customer concatenated
     *   `total_amount` -> aggregated amount of money earned from rentals per customer
     * This endpoint should return output in JSON format
     * 10 points
     */
    try {
        $customers = $service->get_customers_report();
        $result = [];

        foreach ($customers as $c) {
            $result[] = [
                'details' => "<tr><td>{$c['customer_id']}</td><td>{$c['first_name']} {$c['last_name']}</td><td>{$c['total_spent']}</td></tr>",
                'customer_full_name' => "{$c['first_name']} {$c['last_name']}",
                'total_amount' => $c['total_spent']
            ];
        }
        Flight::json($result);
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()], 400);
    }
});

Flight::route('GET /rentals/customer/@customer_id', function ($customer_id) use ($service) {
    /** TODO
     * This endpoint should return the array of all rentals from the customer
     * Every item returned should have 
     * following properties:
     *   `rental_date` -> rental_date 
     *   `film_title` -> title of the film 
     *   `payment_amount` -> amount of payment for given rental
     * This endpoint should return output in JSON format
     * 10 points
     */
    try {
        $rentals = $service->get_customer_rental_details((int) $customer_id);
        $result = [];
        foreach ($rentals as $r) {
            $result[] = [
                'rental_date' => $r['rental_date'],
                'film_title' => $r['title'],
                'payment_amount' => $r['amount']
            ];
        }
        Flight::json($result);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
