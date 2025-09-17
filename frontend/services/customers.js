var CustomerService = {
    load_customers: function () {
        RestClient.get("/customers/report", function (data) {
            var tbody = $("#customer-details tbody");
            tbody.empty();
            data.forEach(function (c) {
                // Wrap the backend's <tr> in a jQuery object so we can manipulate it
                var row = $(c.details);

                // Prepend the Details button in the first cell
                row.prepend(`
                <td class="text-center">
                    <button type="button" class="btn btn-success details-btn" data-id="${c.customer_id}">
                        Details
                    </button>
                </td>
            `);

                tbody.append(row);
            });

            // Attach click handlers
            $(".details-btn").click(function () {
                var customer_id = $(this).data("id");
                CustomerService.load_customer_rentals(customer_id);
            });
        });
    },

    load_customer_rentals: function (customer_id) {
        RestClient.get("/rentals/customer/" + customer_id, function (rentals) {
            var tbody = $("#customer-details-modal tbody");
            tbody.empty();
            var total = 0;
            rentals.forEach(function (r, index) {
                total += parseFloat(r.payment_amount);
                tbody.append(`
                    <tr>
                        <th scope="row">${index + 1}</th>
                        <td>${r.rental_date}</td>
                        <td>${r.film_title}</td>
                        <td>${r.payment_amount}</td>
                    </tr>
                `);
            });
            tbody.append(`
                <tr>
                    <td colspan="3"><strong>Total bill</strong></td>
                    <td>${total.toFixed(2)}</td>
                </tr>
            `);
            $("#customer-details-modal").modal("show");
        });
    }
};
