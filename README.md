# Film Performance Report Query

This SQL query generates a **film-centric performance report** from the Sakila database, showing the number of rentals and total revenue for each film.

```sql
SELECT 
    f.film_id, 
    f.title,
    COUNT(r.rental_id) AS rentals,
    COALESCE(SUM(p.amount), 0) AS revenue
FROM film f
LEFT JOIN inventory i ON f.film_id = i.film_id
LEFT JOIN rental r ON i.inventory_id = r.inventory_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY f.film_id, f.title
ORDER BY revenue DESC;

## Explanation

- **`f.film_id, f.title`**  
  Retrieves the film’s unique ID and title.

- **`COUNT(r.rental_id) AS rentals`**  
  Counts how many times the film was rented. Returns `0` if the film has no rentals.

- **`COALESCE(SUM(p.amount), 0) AS revenue`**  
  Sums up the payment amounts for the film.  
  `COALESCE` ensures that films with no payments return `0` instead of `NULL`.

- **`FROM film f`**  
  Starts from the film table (aliased as `f`), since the report is centered on films.

- **`LEFT JOIN inventory i ON f.film_id = i.film_id`**  
  Joins the inventory table to include all copies of the film.  
  `LEFT JOIN` ensures films with no inventory are still included.

- **`LEFT JOIN rental r ON i.inventory_id = r.inventory_id`**  
  Joins the rental table to count rentals.  
  `LEFT JOIN` ensures films with no rentals are still included.

- **`LEFT JOIN payment p ON r.rental_id = p.rental_id`**  
  Joins the payment table to sum revenue.  
  `LEFT JOIN` ensures films with no payments are still included.

- **`GROUP BY f.film_id, f.title`**  
  Aggregates data per film because `COUNT` and `SUM` are used.

- **`ORDER BY revenue DESC`**  
  Sorts results by total revenue, showing the highest-grossing films first.
